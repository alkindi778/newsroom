import { ref, computed } from 'vue'
import type { Ref } from 'vue'

export interface Article {
  id: number
  title: string
  slug: string
  content: string
  excerpt: string
  image_url: string
  category: {
    id: number
    name: string
    slug: string
  }
  author: {
    id: number
    name: string
  }
  published_at: string
  views: number
  featured: boolean
}

export const useArticles = () => {
  const config = useRuntimeConfig()
  const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

  /**
   * ✨ Nuxt 4.2: استخدام useAsyncData مع Abort Control
   */
  const fetchLatestArticles = (limit: number = 10) => {
    return useAsyncData(
      `articles-latest-${limit}`,
      async (_nuxtApp, { signal }) => {
        const data: any = await $fetch(`${apiBase}/articles`, {
          params: { limit, sort: 'latest' },
          signal: signal as AbortSignal, // ✨ Abort control تلقائي
        })
        return data?.data || []
      },
      {
        // ✨ Nuxt 4.2: تنظيف تلقائي للذاكرة
        lazy: true,
        server: true,
        // Cache للأداء
        getCachedData: (key, nuxtApp, ctx) => {
          // لا تستخدم cache عند manual refresh
          if (ctx.cause === 'refresh:manual') return undefined
          return nuxtApp.payload.data[key]
        }
      }
    )
  }

  /**
   * جلب مقال واحد بالـ slug
   */
  const fetchArticleBySlug = (slug: Ref<string> | string) => {
    // ✨ Nuxt 4.2: reactive key support
    return useAsyncData(
      () => `article-${unref(slug)}`,
      async (_nuxtApp, { signal }) => {
        const data: any = await $fetch(`${apiBase}/articles/${unref(slug)}`, {
          signal: signal as AbortSignal,
        })
        return data?.data || null
      },
      {
        watch: [() => unref(slug)], // إعادة fetch عند تغيير slug
      }
    )
  }

  /**
   * جلب مقالات حسب القسم
   */
  const fetchArticlesByCategory = (categorySlug: Ref<string> | string, page: Ref<number> = ref(1)) => {
    return useAsyncData(
      () => `articles-category-${unref(categorySlug)}-${unref(page)}`,
      async (_nuxtApp, { signal }) => {
        const data: any = await $fetch(`${apiBase}/categories/${unref(categorySlug)}/articles`, {
          params: { page: unref(page) },
          signal: signal as AbortSignal,
        })
        return data?.data || { data: [], meta: {} }
      },
      {
        watch: [() => unref(categorySlug), () => unref(page)],
      }
    )
  }

  /**
   * البحث في المقالات
   */
  const searchArticles = (query: Ref<string> | string) => {
    return useAsyncData(
      () => `articles-search-${unref(query)}`,
      async (_nuxtApp, { signal }) => {
        if (!unref(query)) return []
        
        const data: any = await $fetch(`${apiBase}/articles/search`, {
          params: { q: unref(query) },
          signal: signal as AbortSignal,
        })
        return data?.data || []
      },
      {
        watch: [() => unref(query)],
        // ✨ سيتم تحديث البيانات تلقائياً عند تغيير query
      }
    )
  }

  /**
   * جلب الأخبار العاجلة
   */
  const fetchBreakingNews = (limit: number = 5) => {
    return useAsyncData(
      `articles-breaking-${limit}`,
      async (_nuxtApp, { signal }) => {
        const data: any = await $fetch(`${apiBase}/articles/breaking-news`, {
          params: { limit },
          signal: signal as AbortSignal,
        })
        return data?.data || []
      },
      {
        lazy: true,
        server: true,
        // تحديث كل دقيقة للأخبار العاجلة
        getCachedData: (key, nuxtApp, ctx) => {
          if (ctx.cause === 'refresh:manual') return undefined
          return nuxtApp.payload.data[key]
        }
      }
    )
  }

  return {
    fetchLatestArticles,
    fetchArticleBySlug,
    fetchArticlesByCategory,
    searchArticles,
    fetchBreakingNews,
  }
}
