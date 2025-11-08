<template>
  <div>
    <!-- Header Title -->
    <div class="bg-primary text-white px-4 py-2 font-bold text-lg flex items-center gap-2 inline-flex">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
      </svg>
      الأكثر قراءة
    </div>
    
    <!-- Description Box -->
    <div class="bg-gray-100 px-4 py-3 border-b border-gray-300 flex items-center justify-between">
      <p class="text-gray-700 text-sm">
        تم اختيار مواضيع <span class="font-bold">"{{ siteName }}"</span> الأكثر قراءة بناءً على إجمالي عدد المشاهدات اليومية. اقرأ المواضيع الأكثر شعبية كل يوم من هنا.
      </p>
      
      <!-- Tabs Inline -->
      <div class="flex items-center gap-2">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="[
            'px-4 py-1 text-sm transition-all',
            activeTab === tab.value
              ? 'bg-gray-700 text-white font-bold'
              : 'bg-white text-gray-600 hover:bg-gray-200'
          ]"
        >
          <span v-if="activeTab === tab.value">●</span> {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-8 text-center">
      <LoadingSpinner type="dots" size="sm" text="جاري التحميل..." color="primary" />
    </div>

    <!-- Articles Grid -->
    <div v-else-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-300">
      <NuxtLink
        v-for="(article, index) in articles"
        :key="article.id"
        :to="`/news/${article.slug}`"
        class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group border border-gray-100"
      >
        <!-- Number Badge -->
        <div 
          class="flex-shrink-0 flex items-end justify-center font-black text-primary text-6xl pb-4"
        >
          {{ index + 1 }}
        </div>

        <!-- Thumbnail -->
        <div v-if="article.image || article.thumbnail" class="flex-shrink-0">
          <img
            :src="getImageUrl(article.thumbnail || article.image, 'thumbnail')"
            :alt="article.title"
            loading="lazy"
            class="w-32 h-24 object-cover rounded"
          />
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
          <!-- العنوان الفرعي -->
          <p v-if="article.subtitle" class="text-sm text-blue-600 font-semibold mb-1 line-clamp-1">
            {{ article.subtitle }}
          </p>
          <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug mb-4">
            {{ article.title }}
          </h3>
          <div class="flex items-center justify-between text-sm text-gray-600 mt-auto pt-2">
            <span v-if="article.category" class="font-semibold">
              {{ article.category.name }}
            </span>
            <span class="text-gray-500">{{ formatViews(article.views) }} مشاهدة</span>
          </div>
        </div>
      </NuxtLink>
    </div>

    <!-- Empty State -->
    <div v-else class="p-8 text-center text-gray-500">
      <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p>لا توجد أخبار متاحة</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

interface Props {
  title?: string | null
  subtitle?: string | null
  limit?: number
  settings?: Record<string, any> | null
}

const props = withDefaults(defineProps<Props>(), {
  limit: 6
})

interface Tab {
  label: string
  value: 'today' | 'week' | 'month'
}

const tabs: Tab[] = [
  { label: 'اليوم', value: 'today' },
  { label: 'الأسبوع', value: 'week' },
  { label: 'الشهر', value: 'month' }
]

const activeTab = ref<'today' | 'week' | 'month'>('month')
const articles = ref<Article[]>([])
const loading = ref(false)

const { apiFetch } = useApi()
const { getImageUrl } = useImageUrl()
const settingsStore = useSettingsStore()

// Get site name from settings
const siteName = computed(() => settingsStore.getSetting('site_name', 'غرفة الأخبار'))

// Format views number
const formatViews = (views: number): string => {
  if (views >= 1000000) return (views / 1000000).toFixed(1) + ' مليون'
  if (views >= 1000) return (views / 1000).toFixed(1) + ' ألف'
  return views.toLocaleString('ar')
}

// Fetch trending articles
const fetchTrendingArticles = async () => {
  loading.value = true
  try {
    const response = await apiFetch<any>('/articles/popular', {
      params: {
        limit: props.limit,
        period: activeTab.value
      }
    })
    
    if (response?.data) {
      articles.value = response.data
    }
  } catch (err) {
    console.error('Error fetching trending articles:', err)
  } finally {
    loading.value = false
  }
}

// Watch tab changes
watch(activeTab, () => {
  fetchTrendingArticles()
})

// Load on mount
onMounted(() => {
  fetchTrendingArticles()
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
