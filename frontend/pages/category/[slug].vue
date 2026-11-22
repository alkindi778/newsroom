<template>
  <div>
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
          {{ getCategoryName }}
        </h1>
        <p class="text-gray-600">
          تصفح آخر الأخبار في قسم {{ getCategoryName }}
        </p>
      </div>

      <!-- Category Top Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="homepage_top" page="category" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading && articles.length === 0" type="dots" size="lg" text="جاري تحميل الأخبار..." />

    <!-- Grid الأخبار -->
    <div v-else-if="articles.length > 0">
      <!-- Hero Article - الخبر الأول -->
      <NuxtLink 
        v-if="articles[0]" 
        :to="`/news/${articles[0].slug}`" 
        class="hero-article block mb-8 overflow-hidden"
      >
        <!-- Desktop: تصميم أفقي -->
        <div class="hidden md:block relative h-[400px]">
          <!-- الصورة -->
          <img 
            :src="articles[0].image" 
            :alt="articles[0].title"
            class="w-full h-full object-cover rounded"
          />
          
          <!-- Gradient Overlay -->
          <div class="hero-overlay absolute inset-0 rounded"></div>
          
          <!-- المحتوى -->
          <div class="absolute inset-0 flex items-center">
            <div class="w-1/2 pr-8 md:pr-12 lg:pr-16">
              <!-- العنوان الفرعي -->
              <p v-if="articles[0].subtitle" class="text-white text-xl md:text-2xl lg:text-3xl font-bold mb-3 drop-shadow-lg" style="text-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);">
                {{ articles[0].subtitle }}
              </p>
              <!-- العنوان -->
              <h2 class="hero-title text-white font-bold">
                {{ getArticleTitle(articles[0]) }}
              </h2>
            </div>
          </div>
        </div>

        <!-- Mobile: تصميم عمودي -->
        <div class="md:hidden">
          <!-- الصورة -->
          <div class="w-full aspect-video rounded-t overflow-hidden">
            <img 
              :src="articles[0].image" 
              :alt="articles[0].title"
              class="w-full h-full object-cover"
            />
          </div>
          
          <!-- العنوان في مستطيل أزرق -->
          <div class="hero-mobile-title bg-blue-800 p-6 rounded-b">
            <p v-if="articles[0].subtitle" class="text-white text-lg md:text-xl font-bold mb-3 pr-4" style="text-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);">
              {{ articles[0].subtitle }}
            </p>
            <h2 class="text-white font-bold text-3xl leading-tight pr-4 border-r-4 border-white">
              {{ getArticleTitle(articles[0]) }}
            </h2>
          </div>
        </div>
      </NuxtLink>

      <!-- باقي الأخبار بالتصميم الحالي -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <TransitionGroup name="fade-slide">
          <NewsCard 
            v-for="article in articles.slice(1)" 
            :key="article.id" 
            :article="article" 
          />
        </TransitionGroup>
      </div>
    </div>
    </div>

    <!-- شريط المعلومات - خارج الـ container -->
    <InfoBar v-if="articles.length > 0" />

    <div v-if="articles.length > 0" class="container mx-auto px-4">
      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="flex justify-center gap-1.5 sm:gap-2 flex-wrap mt-8">
        <!-- Previous Page -->
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
              ? 'bg-blue-600 text-white shadow-md'
              : 'bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600'
          ]"
        >
          {{ page }}
        </button>

        <!-- Next Page -->
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- حالة فارغة -->
    <div v-else-if="!loading && articles.length === 0" class="container mx-auto px-4">
      <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-600 text-lg">لا توجد أخبار في هذا القسم حالياً</p>
        <NuxtLink to="/" class="inline-block mt-4 text-blue-600 hover:text-blue-700 font-semibold">
          العودة للرئيسية
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const route = useRoute()
const { apiFetch } = useApi()

const { setCategorySeoMeta } = useAppSeoMeta()
const { locale } = useI18n()

// دالة للحصول على اسم القسم المترجم
const getCategoryName = computed(() => {
  if (!category.value) return 'الأخبار'
  return locale.value === 'en' && category.value.name_en ? category.value.name_en : category.value.name
})

// دالة للحصول على عنوان المقال المترجم
const getArticleTitle = (article: any) => {
  if (!article) return ''
  
  const isEnglish = locale.value === 'en'
  const hasTranslation = !!article.title_en
  const title = isEnglish && hasTranslation ? article.title_en : article.title
  
  console.log('📁 Category Page - getArticleTitle:', {
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

const currentPage = ref(1)
const slug = computed(() => route.params.slug as string)

// جلب معلومات القسم مع SSR
const { data: category } = await useAsyncData(
  `category-${slug.value}`,
  async () => {
    const response = await apiFetch<any>(`/categories/${slug.value}`)
    return response?.data || null
  },
  {
    watch: [slug]
  }
)

if (!category.value) {
  throw createError({
    statusCode: 404,
    statusMessage: 'الصفحة غير موجودة',
    message: 'عذراً، القسم الذي تبحث عنه غير موجود.',
    fatal: true
  })
}

// جلب الأخبار مع SSR
const { data: articlesData, refresh: refreshArticles } = await useAsyncData(
  () => `articles-category-${slug.value}-${currentPage.value}`,
  async () => {
    if (!category.value) return { data: [], meta: null }
    
    const response = await apiFetch<any>('/articles', {
      params: {
        category: category.value.id,
        per_page: 9,
        page: currentPage.value
      }
    })
    return {
      data: response?.data || [],
      meta: response?.pagination || null
    }
  },
  {
    watch: [slug, currentPage]
  }
)

const articles = ref(articlesData.value?.data || [])
const pagination = ref(articlesData.value?.meta || null)
const loading = ref(false)

// تحديث المقالات عند تغيير البيانات
watch(articlesData, (newData) => {
  if (newData) {
    articles.value = newData.data
    pagination.value = newData.meta
  }
})

// دوال التعامل مع الصفحات
const goToPage = (page: number | string) => {
  const pageNumber = typeof page === 'number' ? page : parseInt(page)
  if (!isNaN(pageNumber) && pageNumber >= 1 && pagination.value && pageNumber <= pagination.value.last_page) {
    currentPage.value = pageNumber
    refreshArticles()
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

// SEO Meta Tags
watchEffect(() => {
  if (category.value) {
    setCategorySeoMeta(category.value)
  }
})

// إعادة تعيين عند تغيير القسم
watch(slug, () => {
  currentPage.value = 1
  articles.value = []
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>

<style scoped>
/* Hero Article Styles - مطابق لتصميم Figma */
.hero-article {
  border-radius: 6px;
  box-shadow: 
    0px 2px 6px 0px rgba(0, 0, 0, 0.1), 
    0px 0px 2px 0px rgba(0, 0, 0, 0.08), 
    0px 0px 0px 1px rgba(0, 0, 0, 0.2);
}

/* Mobile Hero Title */
.hero-mobile-title {
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
}

.hero-article .rounded {
  border-radius: 4px;
}

.hero-overlay {
  background: linear-gradient(
    to left, 
    rgba(37, 99, 235, 1) 0%,
    rgba(59, 130, 246, 1) 30%, 
    rgba(96, 165, 250, 0) 50%,
    rgba(96, 165, 250, 0) 100%
  );
}

.hero-title {
  font-size: clamp(1.75rem, 4vw, 3.5rem);
  line-height: 1.132;
  letter-spacing: -0.011em;
  text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  font-weight: 600;
  text-align: right;
}

/* Animation Styles */
.fade-slide-enter-active {
  transition: all 0.3s ease-out;
}

.fade-slide-leave-active {
  transition: all 0.2s ease-in;
}

.fade-slide-enter-from {
  transform: translateY(20px);
  opacity: 0;
}

.fade-slide-leave-to {
  opacity: 0;
}

.fade-slide-move {
  transition: transform 0.3s ease;
}
</style>
