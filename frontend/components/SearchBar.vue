<template>
  <div class="relative">
    <form @submit.prevent="handleSearch" class="relative">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="ابحث عن الأخبار..."
        class="w-full px-5 py-3 pr-12 rounded-full border-2 border-gray-200 focus:border-primary focus:outline-none transition-colors text-right"
      />
      <button
        type="submit"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </button>
    </form>

    <!-- نتائج البحث السريع -->
    <div 
      v-if="showResults && results.length > 0"
      class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto z-50"
    >
      <NuxtLink
        v-for="result in results"
        :key="result.id"
        :to="`/news/${result.slug}`"
        class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0"
        @click="closeResults"
      >
        <img
          :src="getImageUrl(result.thumbnail || result.image)"
          :alt="result.title"
          class="w-16 h-16 object-cover rounded"
        />
        <div class="flex-1">
          <h4 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ result.title }}</h4>
          <p class="text-xs text-gray-500 mt-1">{{ result.category?.name }}</p>
        </div>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const searchQuery = ref('')
const results = ref<Article[]>([])
const showResults = ref(false)
let searchTimeout: NodeJS.Timeout | null = null

const { getImageUrl } = useImageUrl()
const router = useRouter()

// البحث السريع مع debounce
const searchArticles = async () => {
  if (searchQuery.value.length < 2) {
    results.value = []
    showResults.value = false
    return
  }

  const { apiFetch } = useApi()
  try {
    const data = await apiFetch<Article[]>(`/articles/search?q=${searchQuery.value}&limit=5`)
    if (data) {
      results.value = data
      showResults.value = true
    }
  } catch (err) {
    console.error('Search error:', err)
  }
}

// مراقبة التغييرات مع debounce
watch(searchQuery, () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchArticles()
  }, 300)
})

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/search?q=${encodeURIComponent(searchQuery.value)}`)
    closeResults()
  }
}

const closeResults = () => {
  showResults.value = false
}

// إغلاق النتائج عند الضغط خارجها
onMounted(() => {
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement
    if (!target.closest('.relative')) {
      closeResults()
    }
  })
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
