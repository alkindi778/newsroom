<template>
  <ClientOnly>
    <div class="latest-news-ticker bg-navy text-white py-2 shadow-md relative overflow-hidden border-b border-navy-600">
    <div class="container mx-auto px-2 md:px-4">
      <div class="flex items-center gap-2 md:gap-4">
        <!-- عنوان الشريط -->
        <div class="flex-shrink-0 bg-primary text-navy px-2 md:px-4 py-1.5 md:py-2 rounded font-bold text-xs md:text-sm shadow-lg z-10">
          <span class="flex items-center gap-1 md:gap-2">
            <svg class="w-3 h-3 md:w-4 md:h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
            <span class="hidden sm:inline">آخر الأخبار</span>
            <span class="sm:hidden">عاجل</span>
          </span>
        </div>

        <!-- الشريط المتحرك -->
        <div class="flex-1 overflow-hidden relative">
          <div 
            ref="tickerContent"
            class="ticker-content flex items-center gap-3 md:gap-4 whitespace-nowrap"
            :class="{ 
              'animate-ticker': isClient && articles.length > 0 && !isPaused,
              'paused': isPaused 
            }"
            @mouseenter="pauseTicker"
            @mouseleave="resumeTicker"
          >
            <!-- تكرار الأخبار للحصول على حلقة متصلة -->
            <template v-for="(article, index) in displayArticles" :key="`${article.id}-${index}`">
              <NuxtLink 
                :to="`/news/${article.slug}`"
                class="ticker-item flex items-center gap-1 md:gap-2 hover:text-primary transition-colors duration-200"
              >
                <span class="inline-block w-1 h-1 md:w-1.5 md:h-1.5 bg-primary rounded-full animate-ping"></span>
                <span class="font-medium text-xs md:text-sm">
                  <span v-if="article.subtitle" class="text-primary ml-1">{{ article.subtitle }} ●</span>
                  {{ article.title }}
                </span>
              </NuxtLink>
            </template>
          </div>
        </div>

        <!-- أزرار التحكم -->
        <div class="flex-shrink-0 flex items-center gap-2">
          <button 
            @click="togglePause"
            class="p-1.5 md:p-2 hover:bg-primary/20 rounded transition-colors duration-200"
            :title="isPaused ? 'تشغيل' : 'إيقاف مؤقت'"
          >
            <svg v-if="isPaused" class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
            </svg>
            <svg v-else class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
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

interface Article {
  id: number
  title: string
  subtitle?: string
  slug: string
  published_at: string
}

interface ApiResponse {
  status: string
  data: Article[]
}

const articles = ref<Article[]>([])
const isPaused = ref(false)
const tickerContent = ref<HTMLElement | null>(null)
const isClient = ref(false)

const { apiFetch } = useApi()

// تكرار الأخبار ثلاث مرات للحصول على حلقة سلسة ومتواصلة بدون حدود
const displayArticles = computed(() => {
  if (articles.value.length === 0) return []
  // تكرار الأخبار ثلاث مرات - الأنيميشن سيتحرك بسلاسة بدون انقطاع
  return [...articles.value, ...articles.value, ...articles.value]
})

// جلب آخر الأخبار
const fetchLatestNews = async () => {
  try {
    const response = await apiFetch<ApiResponse>('/articles/latest', {
      params: {
        limit: 50
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
  fetchLatestNews()
  
  // تحديث كل 5 دقائق
  refreshInterval = setInterval(() => {
    fetchLatestNews()
  }, 5 * 60 * 1000)
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>

<style scoped>
@keyframes ticker {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-66.666%);
  }
}

.animate-ticker {
  animation: ticker 30s linear infinite;
}

/* سرعة أسرع على الشاشات الصغيرة */
@media (max-width: 768px) {
  .animate-ticker {
    animation: ticker 20s linear infinite;
  }
}

.paused {
  animation-play-state: paused !important;
}

.ticker-item {
  display: inline-flex;
  padding: 0;
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.1;
  }
  50% {
    opacity: 0.2;
  }
}

.animate-pulse-slow {
  animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* تحسين الظهور على الشاشات الصغيرة */
@media (max-width: 768px) {
  .ticker-item span:first-child {
    display: none;
  }
}

/* منع التفاف النص */
.ticker-content {
  will-change: transform;
}
</style>
