<template>
  <ClientOnly>
    <div class="latest-news-ticker bg-navy text-white py-2 shadow-md relative overflow-hidden border-b border-navy-600">
    <div class="container mx-auto px-2 md:px-4">
      <div class="flex items-center gap-2 md:gap-4 h-10"> <!-- Fixed height -->
        <!-- عنوان الشريط -->
        <div class="flex-shrink-0 bg-primary text-navy px-2 md:px-4 py-1.5 md:py-2 rounded font-bold text-xs md:text-sm shadow-lg z-10">
          <span class="flex items-center gap-1 md:gap-2">
            <svg class="w-3 h-3 md:w-4 md:h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
            <span class="hidden sm:inline">آخر الأخبار</span>
            <span class="sm:hidden">آخر الأخبار</span>
          </span>
        </div>

        <!-- الشريط المتحرك (عمودي) -->
        <div class="flex-1 overflow-hidden relative h-full group" @mouseenter="pauseTicker" @mouseleave="resumeTicker">
          <Transition name="slide-vertical" mode="out-in">
            <div 
              v-if="currentArticle"
              :key="currentIndex"
              class="absolute w-full h-full flex items-center"
            >
              <NuxtLink 
                :to="getArticleLink(currentArticle as any)"
                class="flex items-center gap-2 hover:text-primary transition-colors duration-200 w-full truncate"
              >
                <span class="inline-block w-1.5 h-1.5 bg-primary rounded-full animate-ping flex-shrink-0"></span>
                <span class="font-medium text-sm md:text-base truncate">
                  <span v-if="currentArticle.subtitle" class="text-primary ml-1">{{ currentArticle.subtitle }} ●</span>
                  {{ getArticleTitle(currentArticle) }}
                </span>
                <span class="text-xs text-gray-400 mr-2 flex-shrink-0 hidden md:inline-block">
                  {{ formatDate(currentArticle.published_at) }}
                </span>
              </NuxtLink>
            </div>
          </Transition>
        </div>

        <!-- أزرار التحكم -->
        <div class="flex-shrink-0 flex items-center gap-1 md:gap-2">
          <!-- السابق -->
          <button 
            @click="prevNews"
            class="p-1.5 hover:bg-primary/20 rounded transition-colors duration-200 text-gray-300 hover:text-white"
            title="السابق"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
          </button>
          
          <!-- التالي -->
          <button 
            @click="nextNews"
            class="p-1.5 hover:bg-primary/20 rounded transition-colors duration-200 text-gray-300 hover:text-white"
            title="التالي"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <!-- إيقاف/تشغيل -->
          <button 
            @click="togglePause"
            class="p-1.5 hover:bg-primary/20 rounded transition-colors duration-200 text-gray-300 hover:text-white hidden sm:block"
            :title="isPaused ? 'تشغيل' : 'إيقاف مؤقت'"
          >
            <svg v-if="isPaused" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M5 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V4zM13 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2V4z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- خلفية متحركة ديكور -->
    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
      <div class="animate-pulse-slow absolute top-0 right-0 w-32 h-32 bg-white rounded-full blur-3xl"></div>
      <div class="animate-pulse-slow absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full blur-3xl" style="animation-delay: 1s;"></div>
    </div>
  </div>
  </ClientOnly>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'

const { getArticleLink } = useArticleLink()
const { locale } = useI18n()

interface TickerArticle {
  id: number
  title: string
  title_en?: string
  subtitle?: string
  slug: string
  published_at: string
}

interface ApiResponse {
  status: string
  data: TickerArticle[]
}

const articles = ref<TickerArticle[]>([])
const isPaused = ref(false)
const isClient = ref(false)
const currentIndex = ref(0)

const { apiFetch } = useApi()

// دالة ترجمة العنوان
const getArticleTitle = (article: TickerArticle) => {
  const isEnglish = locale.value === 'en'
  const hasTranslation = article.title_en && article.title_en.trim() !== ''
  return (isEnglish && hasTranslation) ? article.title_en : article.title
}

// تنسيق التاريخ
const formatDate = (date: string) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString(locale.value === 'ar' ? 'ar-SA' : 'en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// الخبر الحالي
const currentArticle = computed(() => {
  if (articles.value.length === 0) return null
  return articles.value[currentIndex.value]
})

// جلب آخر الأخبار
const fetchLatestNews = async () => {
  try {
    const response = await apiFetch<ApiResponse>('/articles/latest', {
      params: {
        limit: 100 // زيادة العدد لعرض جميع الأخبار
      }
    })
    
    if (response && response.status === 'success') {
      articles.value = response.data
      console.log('عدد الأخبار المستلمة:', response.data.length)
    }
  } catch (error) {
    console.error('Error fetching latest news:', error)
  }
}

// التنقل
const nextNews = () => {
  if (articles.value.length === 0) return
  currentIndex.value = (currentIndex.value + 1) % articles.value.length
}

const prevNews = () => {
  if (articles.value.length === 0) return
  currentIndex.value = (currentIndex.value - 1 + articles.value.length) % articles.value.length
}

// المؤقت
let tickerInterval: NodeJS.Timeout | null = null

const startTicker = () => {
  if (tickerInterval) clearInterval(tickerInterval)
  tickerInterval = setInterval(() => {
    if (!isPaused.value) {
      nextNews()
    }
  }, 4000) // كل 4 ثواني
}

// إيقاف/تشغيل الشريط
const togglePause = () => {
  isPaused.value = !isPaused.value
}

const pauseTicker = () => {
  isPaused.value = true
}

const resumeTicker = () => {
  isPaused.value = false
}

// تحديث الأخبار كل 5 دقائق
let refreshInterval: NodeJS.Timeout | null = null

onMounted(() => {
  isClient.value = true
  fetchLatestNews().then(() => {
    startTicker()
  })
  
  // تحديث كل 5 دقائق
  refreshInterval = setInterval(() => {
    fetchLatestNews()
  }, 5 * 60 * 1000)
})

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
  if (tickerInterval) clearInterval(tickerInterval)
})
</script>

<style scoped>
.slide-vertical-enter-active,
.slide-vertical-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-vertical-enter-from {
  transform: translateY(20px); /* يأتي من الأسفل */
  opacity: 0;
}

.slide-vertical-leave-to {
  transform: translateY(-20px); /* يذهب للأعلى */
  opacity: 0;
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.1; }
  50% { opacity: 0.2; }
}

.animate-pulse-slow {
  animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
