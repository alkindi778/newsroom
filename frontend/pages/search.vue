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
    <div v-else-if="articles.length > 0">
      <p class="text-sm sm:text-base text-gray-600 mb-6">
        تم العثور على <span class="font-bold text-gray-900">{{ totalResults }}</span> نتيجة
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <NewsCard 
          v-for="article in articles" 
          :key="article.id" 
          :article="article"
        />
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
const loading = ref(false)
const totalResults = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 12

// جلب نتائج البحث
const fetchSearchResults = async () => {
  const query = route.query.q as string
  if (!query) {
    searchQuery.value = ''
    articles.value = []
    return
  }

  searchQuery.value = query
  loading.value = true

  try {
    const response = await apiFetch<any>(
      `/articles/search?q=${encodeURIComponent(query)}&page=${currentPage.value}&per_page=${perPage}`
    )
    
    if (response) {
      // إذا كانت الاستجابة array مباشرة
      if (Array.isArray(response)) {
        articles.value = response
        totalResults.value = response.length
        totalPages.value = 1
      } 
      // إذا كانت object مع pagination
      else if (response.data) {
        articles.value = response.data
        totalResults.value = response.total || response.data.length
        totalPages.value = response.last_page || 1
        currentPage.value = response.current_page || 1
      }
    }
  } catch (error) {
    console.error('Search error:', error)
    articles.value = []
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
