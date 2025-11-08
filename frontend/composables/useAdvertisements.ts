import { ref } from 'vue'
import type { Ref } from 'vue'

interface Advertisement {
  id: number
  title: string
  type: string
  position: string
  image_url: string
  link: string | null
  open_new_tab: boolean
  width: number | null
  height: number | null
  content: string | null
}

export const useAdvertisements = () => {
  const config = useRuntimeConfig()
  const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

  const advertisements: Ref<Advertisement[]> = ref([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  /**
   * Get advertisements by position
   * ✨ Nuxt 4.2: مع دعم Abort Control
   */
  const getByPosition = async (position: string, device: string | null = null, signal?: AbortSignal) => {
    loading.value = true
    error.value = null

    try {
      const params: any = {}
      if (device) {
        params.device = device
      }

      const data: any = await $fetch(`${apiBase}/advertisements/position/${position}`, {
        params,
        signal, // ✨ Nuxt 4.2: دعم إلغاء الطلبات
        onResponseError({ response }) {
          throw new Error(response._data?.message || 'Failed to fetch advertisements')
        }
      })

      if (data?.success && data?.data) {
        advertisements.value = data.data
        return data.data
      }
      
      return []
    } catch (err: any) {
      // تجاهل أخطاء الإلغاء
      if (err.name === 'AbortError') {
        console.log('Request aborted:', position)
        return []
      }
      error.value = err.message
      console.error('Error fetching advertisements:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  /**
   * Get advertisements for specific page
   */
  const getForPage = async (page: string, device: string | null = null) => {
    loading.value = true
    error.value = null

    try {
      const params: any = {}
      if (device) {
        params.device = device
      }

      const data: any = await $fetch(`${apiBase}/advertisements/page/${page}`, {
        params,
        onResponseError({ response }) {
          throw new Error(response._data?.message || 'Failed to fetch advertisements')
        }
      })

      if (data?.success && data?.data) {
        advertisements.value = data.data
        return data.data
      }
      
      return []
    } catch (err: any) {
      error.value = err.message
      console.error('Error fetching advertisements:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  /**
   * Track advertisement view
   */
  const trackView = async (id: number) => {
    try {
      await $fetch(`${apiBase}/advertisements/${id}/view`, {
        method: 'POST',
      })
    } catch (err) {
      // Silent fail for tracking
      if (process.client) {
        console.warn('Could not track view:', err)
      }
    }
  }

  /**
   * Track advertisement click
   */
  const trackClick = async (id: number) => {
    try {
      await $fetch(`${apiBase}/advertisements/${id}/click`, {
        method: 'POST',
      })
    } catch (err) {
      // Silent fail for tracking
      if (process.client) {
        console.warn('Could not track click:', err)
      }
    }
  }

  /**
   * Get advertisements after specific section
   */
  const getAfterSection = async (sectionId: number, page: string = 'home', signal?: AbortSignal) => {
    loading.value = true
    error.value = null

    try {
      const data: any = await $fetch(`${apiBase}/advertisements/after-section/${sectionId}`, {
        params: { page },
        signal,
        onResponseError({ response }) {
          throw new Error(response._data?.message || 'Failed to fetch advertisements')
        }
      })

      if (data?.success && data?.data) {
        return data.data
      }
      
      return []
    } catch (err: any) {
      if (err.name === 'AbortError') {
        return []
      }
      error.value = err.message
      console.error('Error fetching advertisements after section:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  /**
   * Detect device type
   */
  const getDeviceType = (): string => {
    if (process.client && window) {
      const width = window.innerWidth
      if (width < 768) return 'mobile'
      if (width < 1024) return 'tablet'
      return 'desktop'
    }
    return 'desktop'
  }

  return {
    advertisements,
    loading,
    error,
    getByPosition,
    getForPage,
    getAfterSection,
    trackView,
    trackClick,
    getDeviceType,
  }
}
