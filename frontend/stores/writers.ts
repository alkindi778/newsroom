import { defineStore } from 'pinia'
import type { Writer } from '~/types'

interface Pagination {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export const useWritersStore = defineStore('writers', {
  state: () => ({
    writers: [] as Writer[],
    loading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 24,
      total: 0
    } as Pagination
  }),

  getters: {
    hasMore: (state) => state.pagination.current_page < state.pagination.last_page
  },

  actions: {
    async fetchWriters(params?: any) {
      this.loading = true
      this.error = null

      try {
        const { apiFetch } = useApi()
        const response = await apiFetch<any>('/writers', { params })
        
        if (response) {
          // إذا كان append mode (تحميل المزيد)
          if (params?.page && params.page > 1) {
            this.writers = [...this.writers, ...(response.data || [])]
          } else {
            this.writers = response.data || []
          }

          // تحديث pagination
          if (response.meta) {
            this.pagination = {
              current_page: response.meta.current_page || 1,
              last_page: response.meta.last_page || 1,
              per_page: response.meta.per_page || 24,
              total: response.meta.total || 0
            }
          }
        }
      } catch (err: any) {
        this.error = err.message || 'حدث خطأ في تحميل الكُتاب'
        console.error('Error fetching writers:', err)
      } finally {
        this.loading = false
      }
    }
  }
})
