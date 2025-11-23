import { defineStore } from 'pinia'
import type { Opinion, Writer, PaginatedResponse, FilterParams } from '~/types'

export const useOpinionsStore = defineStore('opinions', {
  state: () => ({
    opinions: [] as Opinion[],
    featuredOpinions: [] as Opinion[],
    currentOpinion: null as Opinion | null,
    writers: [] as Writer[],
    currentWriter: null as Writer | null,
    loading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
      per_page: 10,
      from: 0,
      to: 0
    }
  }),

  actions: {
    // جلب جميع مقالات الرأي
    async fetchOpinions(params: FilterParams = {}) {
      this.loading = true
      this.error = null
      
      const { apiFetch } = useApi()
      
      try {
        const queryParams = new URLSearchParams()
        if (params.search) queryParams.append('search', params.search)
        if (params.page) queryParams.append('page', params.page.toString())
        if (params.per_page) queryParams.append('per_page', params.per_page.toString())
        
        // إضافة الترتيب (الافتراضي: من الأحدث)
        queryParams.append('sort_by', 'published_at')
        queryParams.append('sort_dir', 'desc')
        
        const response = await apiFetch<any>(
          `/opinions?${queryParams.toString()}`
        )

        if (response && response.data) {
          this.opinions = response.data
          // Laravel pagination uses 'meta' instead of 'pagination'
          if (response.meta) {
            this.pagination = {
              current_page: response.meta.current_page,
              last_page: response.meta.last_page,
              total: response.meta.total,
              per_page: response.meta.per_page,
              from: response.meta.from,
              to: response.meta.to
            }
            console.log('Opinions Pagination Data:', this.pagination)
          } else {
            console.log('No pagination data in response:', response)
          }
        }
      } catch (err: any) {
        this.error = err.message
        console.error('Error fetching opinions:', err)
      } finally {
        this.loading = false
      }
    },

    // جلب المقالات المميزة
    async fetchFeaturedOpinions() {
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>('/opinions/featured')
        if (response && response.data) {
          this.featuredOpinions = response.data
        }
      } catch (err) {
        console.error('Error fetching featured opinions:', err)
      }
    },

    // جلب مقال واحد
    async fetchOpinion(slug: string) {
      this.loading = true
      this.error = null
      
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>(`/opinions/${slug}`)
        if (response && response.data) {
          this.currentOpinion = response.data
        }
      } catch (err: any) {
        this.error = err.message
        console.error('Error fetching opinion:', err)
      } finally {
        this.loading = false
      }
    },

    // جلب جميع الكُتاب
    async fetchWriters() {
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>('/writers')
        if (response && response.data) {
          this.writers = response.data
        }
      } catch (err) {
        console.error('Error fetching writers:', err)
      }
    },

    // جلب كاتب واحد
    async fetchWriter(slug: string) {
      const { apiFetch } = useApi()
      
      try {
        const data = await apiFetch<Writer>(`/writers/${slug}`)
        if (data) {
          this.currentWriter = data
        }
      } catch (err) {
        console.error('Error fetching writer:', err)
      }
    },

    // إعجاب بمقال
    async likeOpinion(opinionId: number) {
      const { apiFetch } = useApi()
      
      try {
        await apiFetch<void>(`/opinions/${opinionId}/like`, {
          method: 'POST'
        })
        
        // تحديث العدد محلياً
        if (this.currentOpinion && this.currentOpinion.id === opinionId) {
          this.currentOpinion.likes++
        }
      } catch (err) {
        console.error('Error liking opinion:', err)
      }
    }
  },

  getters: {
    getOpinionBySlug: (state) => (slug: string) => {
      return state.opinions.find(opinion => opinion.slug === slug)
    },
    
    hasMore: (state) => {
      return state.pagination?.current_page < state.pagination?.last_page
    }
  }
})
