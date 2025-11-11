<template>
  <div class="hero-slider rounded-lg overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
      <!-- السلايدر الرئيسي - 2/3 من المساحة -->
      <div 
        class="lg:col-span-2 relative h-[400px] lg:h-[520px] overflow-hidden"
        @touchstart="handleTouchStart"
        @touchend="handleTouchEnd"
      >
        <!-- الخبر النشط -->
        <TransitionGroup name="slide-fade">
          <div
            v-for="(article, index) in articles"
            :key="article.id"
            v-show="index === activeIndex"
            class="absolute inset-0"
          >
            <!-- الصورة -->
            <img
              :src="getImageUrl(article.image)"
              :alt="article.title"
              loading="lazy"
              class="w-full h-full object-cover"
            />

            <!-- التدرج -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
          </div>
        </TransitionGroup>

        <!-- المحتوى في الأسفل - مع خلفية شفافة -->
        <div class="absolute bottom-0 left-0 right-0 bg-gray-700/40 backdrop-blur-md z-10">
          <!-- المحتوى: العنوان + الفاصل + الأزرار -->
          <div class="px-4 py-5 sm:px-6 lg:p-8 flex items-center justify-between gap-6">
            <!-- العنوان على اليسار - متحرك -->
            <div class="flex-1 relative min-h-[3.5rem] lg:min-h-[4rem]">
              <TransitionGroup name="title-fade">
                <div
                  v-for="(article, index) in articles"
                  :key="article.id"
                  v-show="index === activeIndex"
                  class="absolute inset-0 flex flex-col justify-center"
                >
                  <NuxtLink :to="`/news/${article.slug}`">
                    <!-- العنوان الفرعي -->
                    <p v-if="article.subtitle" class="text-primary text-sm sm:text-base lg:text-lg font-semibold mb-0.5 lg:mb-1">
                      {{ article.subtitle }}
                    </p>
                    <h2 class="text-white text-lg sm:text-xl lg:text-2xl font-bold leading-snug lg:leading-tight hover:text-gray-200 transition-colors line-clamp-2 lg:line-clamp-none">
                      {{ article.title }}
                    </h2>
                  </NuxtLink>
                </div>
              </TransitionGroup>
            </div>

            <!-- الفاصل الذهبي العمودي في المنتصف - ثابت (مخفي في الهاتف) -->
            <div class="hidden lg:block h-16 w-1 bg-primary flex-shrink-0"></div>
            
            <!-- الأزرار على اليمين - ثابتة (3 أزرار فقط) - مخفية في الهاتف -->
            <div class="hidden lg:flex items-center gap-2 flex-shrink-0">
              <TransitionGroup name="button-fade">
                <button
                  v-for="idx in visibleButtons"
                  :key="idx"
                  @click="goToSlide(idx)"
                  :class="[
                    'w-12 h-12 rounded flex items-center justify-center text-lg font-bold transition-all',
                    idx === activeIndex
                      ? 'bg-white text-gray-900'
                      : 'bg-gray-800/70 text-gray-300 hover:bg-gray-700 hover:text-white'
                  ]"
                >
                  {{ idx + 1 }}
                </button>
              </TransitionGroup>
            </div>
          </div>
        </div>

        <!-- أزرار التنقل - تظهر فقط إذا كان هناك أكثر من خبر واحد -->
        <template v-if="articles.length > 1">
          <button
            @click="nextSlide"
            class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all z-10"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <button
            @click="prevSlide"
            class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all z-10"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </template>
      </div>

      <!-- الأخبار الجانبية - 1/3 من المساحة -->
      <SideNews :articles="sideArticles" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const props = defineProps<{
  articles: Article[]
  sideArticles: Article[]
}>()

const { getImageUrl } = useImageUrl()

const activeIndex = ref(0)
let autoPlayInterval: NodeJS.Timeout | null = null

// Touch support للهاتف
let touchStartX = 0
let touchEndX = 0

const handleTouchStart = (e: TouchEvent) => {
  const touch = e.changedTouches[0]
  if (touch) {
    touchStartX = touch.screenX
  }
}

const handleTouchEnd = (e: TouchEvent) => {
  const touch = e.changedTouches[0]
  if (touch) {
    touchEndX = touch.screenX
    handleSwipe()
  }
}

const handleSwipe = () => {
  const swipeDistance = touchStartX - touchEndX
  const minSwipeDistance = 50 // الحد الأدنى للسحب
  
  if (Math.abs(swipeDistance) > minSwipeDistance) {
    if (swipeDistance > 0) {
      // السحب لليسار = التالي
      nextSlide()
    } else {
      // السحب لليمين = السابق
      prevSlide()
    }
    resetAutoPlay()
  }
}

// حساب الأزرار المرئية (3 أزرار فقط)
const visibleButtons = computed(() => {
  const total = props.articles.length
  if (total <= 3) {
    return Array.from({ length: total }, (_, i) => i)
  }
  
  const current = activeIndex.value
  let buttons: number[] = []
  
  if (current === 0) {
    buttons = [0, 1, 2]
  } else if (current === total - 1) {
    buttons = [total - 3, total - 2, total - 1]
  } else {
    buttons = [current - 1, current, current + 1]
  }
  
  return buttons
})

// الانتقال للسلايد التالي
const nextSlide = () => {
  activeIndex.value = (activeIndex.value + 1) % props.articles.length
}

// الانتقال للسلايد السابق
const prevSlide = () => {
  activeIndex.value = activeIndex.value === 0 ? props.articles.length - 1 : activeIndex.value - 1
}

// الانتقال لسلايد محدد
const goToSlide = (index: number) => {
  activeIndex.value = index
  resetAutoPlay()
}

// التشغيل التلقائي
const startAutoPlay = () => {
  autoPlayInterval = setInterval(() => {
    nextSlide()
  }, 5000) // كل 5 ثواني
}

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
  }
}

const resetAutoPlay = () => {
  stopAutoPlay()
  startAutoPlay()
}

// بدء التشغيل التلقائي عند التحميل
onMounted(() => {
  startAutoPlay()
})

// إيقاف التشغيل عند الخروج
onUnmounted(() => {
  stopAutoPlay()
})
</script>

<style scoped>
/* الانتقال السلس بين السلايدات */
.slide-fade-enter-active {
  transition: all 0.6s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.4s ease-in;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* الانتقال السلس للعناوين */
.title-fade-enter-active {
  transition: opacity 0.4s ease;
}

.title-fade-leave-active {
  transition: opacity 0.3s ease;
}

.title-fade-enter-from {
  opacity: 0;
}

.title-fade-leave-to {
  opacity: 0;
}

/* الانتقال السلس للأزرار */
.button-fade-enter-active,
.button-fade-leave-active {
  transition: all 0.3s ease;
}

.button-fade-enter-from,
.button-fade-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

.button-fade-move {
  transition: transform 0.3s ease;
}
</style>
