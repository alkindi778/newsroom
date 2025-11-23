<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ locale === 'en' ? 'Latest News' : 'آخر الأخبار' }}</h1>
        <p class="text-gray-600">{{ locale === 'en' ? 'Follow the latest news and updates' : 'تابع آخر الأخبار والمستجدات' }}</p>
      </div>

      <!-- Loading -->
      <LoadingSpinner v-if="loading" type="dots" size="lg" :text="locale === 'en' ? 'Loading news...' : 'جاري تحميل الأخبار...'" />

      <!-- Articles List -->
      <div v-else-if="articles.length > 0" class="space-y-1">
        <template v-for="(article, index) in articles" :key="article.id">
          <article 
            class="bg-white border-b border-gray-200 hover:bg-gray-50 transition-colors"
          >
            <NuxtLink :to="getArticleLink(article)" class="block p-4">
            <div class="flex gap-4">
              <!-- Image -->
              <div v-if="article.image || article.thumbnail" class="flex-shrink-0">
                <img
                  :src="getImageUrl(article.thumbnail || article.image, 'thumbnail')"
                  :alt="getArticleTitle(article)"
                  class="w-40 h-28 object-cover"
                  loading="lazy"
                />
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <!-- Category & Date -->
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                  <span v-if="article.category" class="text-red-600 font-semibold">
                    {{ getCategoryName(article.category) }}
                  </span>
                  <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ formatDate(article.published_at || article.created_at) }}
                  </span>
                </div>

                <!-- Subtitle -->
                <p v-if="article.subtitle" class="text-sm text-blue-600 font-semibold mb-1">
                  {{ article.subtitle }}
                </p>
                
                <!-- Title -->
                <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 leading-tight">
                  {{ getArticleTitle(article) }}
                </h2>

                <!-- Excerpt -->
                <p v-if="article.excerpt" class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                  {{ getArticleExcerpt(article) }}
                </p>

                <!-- Stats -->
                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                  <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ formatViews(article.views) }}
                  </span>
                </div>
              </div>
            </div>
          </NuxtLink>
        </article>

        <!-- Between Articles Advertisement (every 3 articles) -->
        <div v-if="(index + 1) % 3 === 0 && index < articles.length - 1" class="my-6">
          <AdvertisementZone position="between_articles" page="articles" :auto-rotate="false" :show-dots="false" />
        </div>
        </template>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-600 text-lg">{{ locale === 'en' ? 'No news available' : 'لا توجد أخبار حالياً' }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="flex justify-center gap-1.5 sm:gap-2 flex-wrap mt-8">
        <!-- Previous Page -->
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- Page Numbers -->
        <button
          v-for="page in displayedPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all duration-200',
            pagination.current_page === page
              ? 'bg-red-600 text-white shadow-md'
              : 'bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600'
          ]"
        >
          {{ page }}
        </button>

        <!-- Next Page -->
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const articlesStore = useArticlesStore()
const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { getArticleLink } = useArticleLink()
const { locale } = useI18n()

const articles = computed(() => articlesStore.articles)
const loading = computed(() => articlesStore.loading)
const pagination = computed(() => articlesStore.pagination)

// دوال الترجمة
const getArticleTitle = (article: any) => {
  const isEnglish = locale.value === 'en'
  const hasTranslation = !!article.title_en
  const title = isEnglish && hasTranslation ? article.title_en : article.title
  
  console.log('🔤 getArticleTitle:', {
    articleId: article.id,
    locale: locale.value,
    isEnglish,
    hasTranslation,
    title_en: article.title_en,
    title_ar: article.title?.substring(0, 50) + '...',
    returning: title?.substring(0, 50) + '...',
    willUseEnglish: isEnglish && hasTranslation
  })
  
  return title
}

const getArticleExcerpt = (article: any) => {
  // excerpt is not translated in the API, always use the original
  return article.excerpt
}

const getCategoryName = (category: any) => {
  const isEnglish = locale.value === 'en'
  const hasTranslation = !!category.name_en
  const name = isEnglish && hasTranslation ? category.name_en : category.name
  
  console.log('🏷️ getCategoryName:', {
    categoryId: category.id,
    locale: locale.value,
    isEnglish,
    hasTranslation,
    name_en: category.name_en,
    name_ar: category.name,
    returning: name,
    willUseEnglish: isEnglish && hasTranslation
  })
  
  return name
}

// Format views
const formatViews = (views: number): string => {
  if (locale.value === 'en') {
    if (views >= 1000000) return (views / 1000000).toFixed(1) + 'M views'
    if (views >= 1000) return (views / 1000).toFixed(1) + 'K views'
    return views.toLocaleString('en') + ' views'
  }
  if (views >= 1000000) return (views / 1000000).toFixed(1) + ' مليون مشاهدة'
  if (views >= 1000) return (views / 1000).toFixed(1) + ' ألف مشاهدة'
  return views.toLocaleString('ar') + ' مشاهدة'
}

// دوال التعامل مع الصفحات
const goToPage = async (page: number | string) => {
  const pageNumber = typeof page === 'number' ? page : parseInt(page)
  if (!isNaN(pageNumber) && pageNumber >= 1 && pagination.value && pageNumber <= pagination.value.last_page) {
    await articlesStore.fetchArticles({ 
      page: pageNumber,
      per_page: 20
    })
  }
}

// عرض أرقام الصفحات (مع حذف الصفحات الوسيطة عند الحاجة)
const displayedPages = computed(() => {
  if (!pagination.value) return []
  
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const delta = 2 // عدد الصفحات التي تظهر قبل وبعد الصفحة الحالية
  
  let range: number[] = []
  let rangeWithDots: (number | string)[] = []
  let l: number | undefined

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    }
  }

  range.forEach((i) => {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  })

  return rangeWithDots
})

// Fetch articles on mount
onMounted(async () => {
  await articlesStore.fetchArticles({ per_page: 20 })
  
  console.log('📰 Articles loaded on news index:', {
    total: articlesStore.articles.length,
    locale: locale.value,
    firstArticle: articlesStore.articles[0] ? {
      id: articlesStore.articles[0].id,
      title: articlesStore.articles[0].title,
      title_en: articlesStore.articles[0].title_en,
      hasTranslation: !!articlesStore.articles[0].title_en
    } : null
  })
})

const settingsStore = useSettingsStore()

watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    useSeoMeta({
      title: 'آخر الأخبار',
      description: `تابع آخر الأخبار والمستجدات لحظة بلحظة في ${siteName}`,
      ogTitle: `آخر الأخبار - ${siteName}`,
      ogDescription: `تابع آخر الأخبار والمستجدات لحظة بلحظة`
    })
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
