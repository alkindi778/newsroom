import { defineStore } from 'pinia'
import type { Category } from '~/types'

export const useCategoriesStore = defineStore('categories', {
  state: () => ({
    categories: [] as Category[],
    currentCategory: null as Category | null,
    loading: false,
    error: null as string | null
  }),

  actions: {
    // جلب جميع الأقسام
    async fetchCategories() {
      this.loading = true
      this.error = null
      
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>('/categories')
        if (response && response.data) {
          this.categories = response.data
        }
      } catch (err: any) {
        this.error = err.message
        console.error('Error fetching categories:', err)
      } finally {
        this.loading = false
      }
    },

    // جلب قسم واحد
    async fetchCategory(slug: string) {
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>(`/categories/${slug}`)
        if (response && response.data) {
          this.currentCategory = response.data
        }
      } catch (err) {
        console.error('Error fetching category:', err)
      }
    }
  },

  getters: {
    getCategoryBySlug: (state) => (slug: string) => {
      return state.categories.find(cat => cat.slug === slug)
    }
  }
})
