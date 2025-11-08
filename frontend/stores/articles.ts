import { defineStore } from 'pinia'
import type { Article, PaginatedResponse, FilterParams } from '~/types'

export const useArticlesStore = defineStore('articles', {
  state: () => ({
    articles: [] as Article[],
    featuredArticles: [] as Article[],
    latestArticles: [] as Article[],
    currentArticle: null as Article | null,
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
    // جلب جميع الأخبار مع الفلاتر
    async fetchArticles(params: FilterParams = {}) {
      this.loading = true
      this.error = null
      
      // مسح البيانات القديمة إذا كانت صفحة جديدة (وليس append)
      if (!params.page || params.page === 1) {
        this.articles = []
      }
      
      const { apiFetch } = useApi()
      
      try {
        const queryParams = new URLSearchParams()
        if (params.search) queryParams.append('search', params.search)
        if (params.category) queryParams.append('category', params.category.toString())
        if (params.page) queryParams.append('page', params.page.toString())
        if (params.per_page) queryParams.append('per_page', params.per_page.toString())
        
        const response = await apiFetch<any>(
          `/articles?${queryParams.toString()}`
        )

        if (response && response.data) {
          // إذا كانت صفحة > 1، أضف للموجود (append)
          if (params.page && params.page > 1) {
            this.articles = [...this.articles, ...response.data]
          } else {
            this.articles = response.data
          }
          
          if (response.pagination) {
            this.pagination = response.pagination
          }
        }
      } catch (err: any) {
        this.error = err.message || 'فشل في جلب الأخبار'
        console.error('Error fetching articles:', err)
      } finally {
        this.loading = false
      }
    },

    // جلب الأخبار المميزة
    async fetchFeaturedArticles(limit: number = 10) {
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>(`/articles/featured?limit=${limit}`)
        if (response && response.data) {
          this.featuredArticles = response.data
        }
      } catch (err) {
        console.error('Error fetching featured articles:', err)
      }
    },

    // جلب أحدث الأخبار
    async fetchLatestArticles(limit: number = 5) {
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>(`/articles/latest?limit=${limit}`)
        if (response && response.data) {
          this.latestArticles = response.data
        }
      } catch (err) {
        console.error('Error fetching latest articles:', err)
      }
    },

    // جلب مقال واحد بواسطة ID أو Slug
    async fetchArticle(identifier: string | number, forceRefresh: boolean = false) {
      this.loading = true
      this.error = null
      
      const { apiFetch } = useApi()
      
      try {
        const response = await apiFetch<any>(
          `/articles/${identifier}`,
          { noCache: forceRefresh }  // استخدام noCache لإجبار التحديث
        )
        if (response && response.data) {
          this.currentArticle = response.data
          
          // تحديث المقال في قائمة المقالات إذا كان موجوداً
          const index = this.articles.findIndex(a => a.id === response.data.id)
          if (index !== -1) {
            this.articles[index] = response.data
          }
          
          // تحديث في المقالات المميزة
          const featuredIndex = this.featuredArticles.findIndex(a => a.id === response.data.id)
          if (featuredIndex !== -1) {
            this.featuredArticles[featuredIndex] = response.data
          }
          
          // تحديث في أحدث المقالات
          const latestIndex = this.latestArticles.findIndex(a => a.id === response.data.id)
          if (latestIndex !== -1) {
            this.latestArticles[latestIndex] = response.data
          }
        }
      } catch (err: any) {
        this.error = err.message || 'فشل في جلب المقال'
        console.error('Error fetching article:', err)
      } finally {
        this.loading = false
      }
    },

    // زيادة عدد المشاهدات
    async incrementViews(articleId: number) {
      const { apiFetch } = useApi()
      
      try {
        await apiFetch<void>(`/articles/${articleId}/view`, {
          method: 'POST'
        })
      } catch (err) {
        console.error('Error incrementing views:', err)
      }
    },

    // إجبار تحديث جميع البيانات (مفيد بعد تحديث مقال)
    async refreshAllArticles() {
      const { apiFetch } = useApi()
      
      try {
        // تحديث الأخبار المميزة مع منع cache
        const featuredResponse = await apiFetch<any>(
          `/articles/featured?limit=10`,
          { noCache: true }
        )
        if (featuredResponse && featuredResponse.data) {
          this.featuredArticles = featuredResponse.data
        }
        
        // تحديث أحدث الأخبار
        const latestResponse = await apiFetch<any>(
          `/articles/latest?limit=5`,
          { noCache: true }
        )
        if (latestResponse && latestResponse.data) {
          this.latestArticles = latestResponse.data
        }
        
        // تحديث القائمة الرئيسية
        const articlesResponse = await apiFetch<any>(
          `/articles`,
          { noCache: true }
        )
        if (articlesResponse && articlesResponse.data) {
          this.articles = articlesResponse.data
        }
      } catch (err) {
        console.error('Error refreshing articles:', err)
      }
    }
  },

  getters: {
    getArticleBySlug: (state) => (slug: string) => {
      return state.articles.find(article => article.slug === slug)
    },
    
    hasMore: (state) => {
      return state.pagination?.current_page < state.pagination?.last_page
    }
  }
})
