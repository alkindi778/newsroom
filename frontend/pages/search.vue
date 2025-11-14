<template>
  <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 md:py-8">
    <!-- عنوان الصفحة -->
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        نتائج البحث
      </h1>
      <p v-if="searchQuery" class="text-base sm:text-lg text-gray-600">
        البحث عن: <span class="inline-flex items-center gap-2 font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">#{{ searchQuery }}</span>
      </p>
    </div>

    <!-- شريط البحث -->
    <div class="mb-8 max-w-2xl">
      <SearchBar />
    </div>

    <!-- Loading -->
    <LoadingSpinner v-if="loading" type="dots" size="lg" text="جاري البحث..." />

    <!-- النتائج -->
    <div v-else-if="articles.length > 0 || videos.length > 0">
      <p class="text-sm sm:text-base text-gray-600 mb-6">
        تم العثور على <span class="font-bold text-gray-900">{{ totalResults }}</span> نتيجة
      </p>

      <!-- Tabs -->
      <div class="flex gap-4 mb-6 border-b">
        <button
          @click="activeTab = 'articles'"
          :class="[
            'pb-3 px-4 font-semibold transition-colors',
            activeTab === 'articles'
              ? 'text-blue-600 border-b-2 border-blue-600'
              : 'text-gray-600 hover:text-gray-900'
          ]"
        >
          المقالات ({{ articles.length }})
        </button>
        <button
          v-if="videos.length > 0"
          @click="activeTab = 'videos'"
          :class="[
            'pb-3 px-4 font-semibold transition-colors',
            activeTab === 'videos'
              ? 'text-blue-600 border-b-2 border-blue-600'
              : 'text-gray-600 hover:text-gray-900'
          ]"
        >
          الفيديوهات ({{ videos.length }})
        </button>
      </div>

      <!-- نتائج المقالات -->
      <div v-if="activeTab === 'articles'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <SearchResultCard 
          v-for="article in articles" 
          :key="article.id" 
          :article="article"
        />
      </div>

      <!-- نتائج الفيديوهات -->
      <div v-else-if="activeTab === 'videos'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <NuxtLink 
          v-for="video in videos" 
          :key="video.id"
          :to="`/videos/${video.slug}`"
          class="group flex flex-col rounded-lg overflow-hidden"
        >
          <div class="relative w-full h-80 overflow-hidden bg-gray-200">
            <img 
              :src="video.thumbnail" 
              :alt="video.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform"
            />
          </div>
          <div class="flex-1 flex flex-col justify-between p-6">
            <h3 class="text-2xl font-bold text-gray-900 line-clamp-3 leading-snug text-right mb-3">
              {{ video.title }}
            </h3>
            <div class="flex items-center justify-between text-sm text-gray-600">
              <span class="font-semibold">{{ video.category?.name }}</span>
              <time class="text-gray-500">{{ formatDate(video.published_at, 'relative') }}</time>
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-8 flex justify-center gap-2">
        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 rounded font-semibold transition-colors',
            currentPage === page
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <!-- لا توجد نتائج -->
    <div v-else-if="!loading && searchQuery" class="text-center py-12">
      <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">لا توجد نتائج</h2>
      <p class="text-base sm:text-lg text-gray-600 mb-4">
        لم نعثر على أي نتائج لـ <span class="inline-flex items-center gap-1 font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full text-sm">#{{ searchQuery }}</span>
      </p>
      <p class="text-sm sm:text-base text-gray-500">
        جرب استخدام كلمات مختلفة أو أقل تحديداً
      </p>
    </div>

    <!-- حالة فارغة -->
    <div v-else class="text-center py-12">
      <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">ابحث عن الأخبار</h2>
      <p class="text-base sm:text-lg text-gray-600">
        استخدم شريط البحث للعثور على الأخبار التي تهمك
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const route = useRoute()
const { apiFetch } = useApi()
const settingsStore = useSettingsStore()

const searchQuery = ref('')
const articles = ref<Article[]>([])
const videos = ref<any[]>([])
const loading = ref(false)
const totalResults = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 12
const activeTab = ref<'articles' | 'videos'>('articles')

// جلب نتائج البحث
const fetchSearchResults = async () => {
  const query = route.query.q as string
  if (!query) {
    searchQuery.value = ''
    articles.value = []
    videos.value = []
    return
  }

  searchQuery.value = query
  loading.value = true

  try {
    // البحث في المقالات
    const articlesResponse = await apiFetch<any>(
      `/articles/search?q=${encodeURIComponent(query)}&page=${currentPage.value}&per_page=${perPage}`
    )
    
    // البحث في الفيديوهات
    const videosResponse = await apiFetch<any>(
      `/videos/search?q=${encodeURIComponent(query)}&page=${currentPage.value}&per_page=${perPage}`
    )
    
    if (articlesResponse) {
      if (Array.isArray(articlesResponse)) {
        articles.value = articlesResponse
      } else if (articlesResponse.data) {
        articles.value = articlesResponse.data
        totalResults.value = (articlesResponse.total || articlesResponse.data.length)
        totalPages.value = articlesResponse.last_page || 1
      }
    }

    if (videosResponse) {
      if (Array.isArray(videosResponse)) {
        videos.value = videosResponse
      } else if (videosResponse.data) {
        videos.value = videosResponse.data
        totalResults.value = (totalResults.value || 0) + (videosResponse.total || videosResponse.data.length)
      }
    }

    totalResults.value = articles.value.length + videos.value.length
  } catch (error) {
    console.error('Search error:', error)
    articles.value = []
    videos.value = []
    totalResults.value = 0
  } finally {
    loading.value = false
  }
}

// الانتقال لصفحة معينة
const goToPage = (page: number) => {
  currentPage.value = page
  fetchSearchResults()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// SEO Meta Tags - ديناميكي من Backend
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    const title = searchQuery.value 
      ? `البحث عن: ${searchQuery.value}`
      : 'البحث عن الأخبار'
    const description = searchQuery.value 
      ? `نتائج البحث عن "${searchQuery.value}" في ${siteName}`
      : `ابحث عن الأخبار والمقالات في ${siteName}`

    useSeoMeta({
      title,
      description,
      ogTitle: title,
      ogDescription: description,
      robots: 'noindex, follow' // صفحات البحث لا تُفهرس عادة
    })
  }
})

// جلب النتائج عند التحميل
onMounted(() => {
  fetchSearchResults()
})

// إعادة البحث عند تغيير query parameter
watch(() => route.query.q, () => {
  currentPage.value = 1
  fetchSearchResults()
})
</script>
