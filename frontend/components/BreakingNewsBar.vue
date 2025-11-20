<template>
  <div
    v-if="breakingNews && breakingNews.length > 0"
    class="fixed bottom-0 left-0 right-0 w-full bg-red-600 text-white shadow-2xl border-t-4 border-red-800 animate-slide-up"
    style="z-index: 9999 !important;"
  >
    <div class="container mx-auto px-2 md:px-4">
      <div class="flex items-center py-2 md:py-5">
        <!-- أيقونة عاجل -->
        <div class="flex-shrink-0 ml-2 md:ml-6">
          <span class="bg-white text-red-600 px-3 py-1.5 md:px-6 md:py-3 rounded-lg font-bold text-sm md:text-3xl animate-pulse">
            عاجل
          </span>
        </div>
        
        <!-- عرض الأخبار العاجلة بشكل متحرك -->
        <div class="flex-1 overflow-hidden relative">
          <Transition name="slide-fade" mode="out-in">
            <div v-if="currentNews" :key="currentIndex" class="flex items-center">
              <NuxtLink
                :to="getArticleLink(currentNews as any)"
                class="text-white hover:text-gray-200 transition-colors duration-200 font-bold text-sm md:text-3xl truncate"
              >
                {{ currentNews.title }}
              </NuxtLink>
            </div>
            <div v-else class="text-white text-sm md:text-3xl">جاري التحميل...</div>
          </Transition>
        </div>
        
        <!-- أزرار التحكم -->
        <div class="flex-shrink-0 flex items-center gap-1 md:gap-2 mr-2 md:mr-4" v-if="breakingNews && breakingNews.length > 1">
          <button
            @click="previousNews"
            class="hover:bg-red-700 p-1 rounded transition-colors duration-200"
            title="السابق"
          >
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <span class="text-xs md:text-sm">{{ currentIndex + 1 }} / {{ breakingNews.length }}</span>
          <button
            @click="nextNews"
            class="hover:bg-red-700 p-1 rounded transition-colors duration-200"
            title="التالي"
          >
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
        
        <!-- زر الإغلاق -->
        <button
          @click="closeBar"
          class="flex-shrink-0 hover:bg-red-700 p-1 rounded transition-colors duration-200 mr-1 md:mr-2"
          title="إغلاق"
        >
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { getArticleLink } = useArticleLink()
const config = useRuntimeConfig()
const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

// استخدام ref بدلاً من await لتجنب مشاكل SSR
const breakingNews = ref<any[]>([])
const isLoading = ref(true)

const currentIndex = ref(0)
const isClosed = ref(false)
let autoPlayInterval: NodeJS.Timeout | null = null

// جلب الأخبار العاجلة على الـ client side فقط
onMounted(async () => {
  try {
    const response: any = await $fetch(`${apiBase}/articles/breaking-news`, {
      params: { limit: 5 }
    })
    
    if (response && response.data && response.data.length > 0) {
      breakingNews.value = response.data
    }
  } catch (error) {
    console.error('Error fetching breaking news:', error)
  } finally {
    isLoading.value = false
    startAutoPlay()
  }
})

// الخبر الحالي
const currentNews = computed(() => {
  if (!breakingNews.value || breakingNews.value.length === 0) {
    return null
  }
  return breakingNews.value[currentIndex.value] || null
})

// التنقل للخبر التالي
const nextNews = () => {
  if (!breakingNews.value || breakingNews.value.length === 0) return
  
  if (currentIndex.value < breakingNews.value.length - 1) {
    currentIndex.value++
  } else {
    currentIndex.value = 0
  }
  resetAutoPlay()
}

// التنقل للخبر السابق
const previousNews = () => {
  if (!breakingNews.value || breakingNews.value.length === 0) return
  
  if (currentIndex.value > 0) {
    currentIndex.value--
  } else {
    currentIndex.value = breakingNews.value.length - 1
  }
  resetAutoPlay()
}

// إغلاق الشريط
const closeBar = () => {
  isClosed.value = true
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
  }
}

// تشغيل التبديل التلقائي
const startAutoPlay = () => {
  if (!breakingNews.value || breakingNews.value.length <= 1) return
  
  autoPlayInterval = setInterval(() => {
    nextNews()
  }, 5000) // تبديل كل 5 ثوانٍ
}

// إعادة تشغيل التبديل التلقائي
const resetAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
  }
  startAutoPlay()
}

// تنظيف الـ interval عند unmount
onUnmounted(() => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
  }
})

// إخفاء الشريط إذا تم إغلاقه
watch(isClosed, (newVal) => {
  if (newVal && breakingNews.value.length > 0) {
    breakingNews.value = []
  }
})
</script>

<style scoped>
@keyframes slide-up {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slide-up 0.5s ease-out;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
  transform: translateX(20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}
</style>
