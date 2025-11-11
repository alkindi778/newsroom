<template>
  <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 md:py-8">
    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="bars" size="lg" fullScreen text="جاري تحميل الخبر..." />

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h2>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <NuxtLink to="/" class="text-blue-600 hover:text-blue-700 font-semibold">
        العودة للرئيسية
      </NuxtLink>
    </div>

    <!-- Article Content -->
    <div v-else-if="article" class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
        <nav class="mb-4 md:mb-6 text-xs sm:text-sm px-2 sm:px-0">
        <ol class="flex items-center gap-1 sm:gap-2 text-gray-600">
          <li><NuxtLink to="/" class="hover:text-blue-600">الرئيسية</NuxtLink></li>
          <li>/</li>
          <li v-if="article.category">
            <NuxtLink :to="`/category/${article.category.slug}`" class="hover:text-blue-600">
              {{ article.category.name }}
            </NuxtLink>
          </li>
          <li v-if="article.category">/</li>
          <li class="text-gray-900 font-semibold truncate">{{ article.title }}</li>
        </ol>
      </nav>

      <!-- Homepage Top Advertisement (will also show here) -->
      <div class="my-8">
        <AdvertisementZone position="homepage_top" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Article Top Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_top" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Article Header -->
      <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- القسم -->
        <NuxtLink 
          v-if="article.category" 
          :to="`/category/${article.category.slug}`"
          class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-br-lg font-semibold text-sm transition-colors"
        >
          {{ article.category.name }}
        </NuxtLink>

        <!-- العنوان -->
        <div class="p-4 sm:p-6 md:p-12">
          <!-- العنوان الفرعي -->
          <h2 v-if="article.subtitle" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl text-blue-600 mb-3 md:mb-4 leading-relaxed font-semibold">
            {{ article.subtitle }}
          </h2>

          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4 md:mb-6 leading-tight">
            {{ article.title }}
          </h1>

          <!-- المقتطف -->
          <p v-if="article.excerpt" class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-gray-700 mb-6 md:mb-8 leading-relaxed font-medium">
            {{ article.excerpt }}
          </p>

          <!-- معلومات الخبر -->
          <div class="flex flex-wrap items-center gap-4 text-base text-gray-600 pb-6 border-b border-gray-200">
            <!-- التاريخ -->
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ formatDate(article.published_at, 'full') }}</span>
            </div>
          </div>
        </div>

        <!-- الصورة الرئيسية -->
        <div class="h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px]">
          <img 
            :src="getImageUrl(article.image)" 
            :alt="article.title"
            loading="eager"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- المحتوى -->
        <div class="p-4 sm:p-6 md:p-12 lg:p-16">
          <!-- مصدر الخبر -->
          <div v-if="article.source" class="mb-8 pb-4 border-b border-gray-200">
            <p class="text-lg font-medium text-cyan-600">{{ article.source }}</p>
          </div>

          <div 
            class="prose prose-sm sm:prose-base md:prose-lg lg:prose-xl max-w-none text-right leading-loose article-content"
            style="font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            v-html="article.content"
          ></div>

          <!-- Article Middle Advertisement -->
          <div class="my-12">
            <AdvertisementZone position="article_middle" page="article" :auto-rotate="false" :show-dots="false" />
          </div>

          <!-- الكلمات الدلالية -->
          <div v-if="article.keywords" class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-4">الكلمات الدلالية:</h3>
            <div class="flex flex-wrap gap-3">
              <NuxtLink 
                v-for="keyword in keywords" 
                :key="keyword"
                :to="`/search?q=${encodeURIComponent(keyword)}`"
                class="px-4 py-2 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-full text-base text-gray-700 transition-all duration-200 cursor-pointer inline-block"
              >
                #{{ keyword }}
              </NuxtLink>
            </div>
          </div>
        </div>
      </article>

      <!-- Article Bottom Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_bottom" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- أخبار ذات صلة -->
      <section v-if="relatedArticles.length > 0" class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">أخبار ذات صلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NewsCard 
            v-for="relatedArticle in relatedArticles" 
            :key="relatedArticle.id" 
            :article="relatedArticle" 
          />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const route = useRoute()
const articlesStore = useArticlesStore()
const config = useRuntimeConfig()
const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { setArticleSeoMeta } = useAppSeoMeta()

const slug = computed(() => route.params.slug as string)

// جلب الخبر مع SSR
const { data: articleData, error: fetchError } = await useAsyncData(
  `article-${slug.value}`,
  async () => {
    const response = await $fetch<any>(`${apiBase}/articles/${slug.value}`)
    return response?.data || null
  },
  {
    watch: [slug]
  }
)

const article = computed(() => articleData.value)
const loading = ref(false)
const error = computed(() => fetchError.value?.message || null)
const relatedArticles = computed(() => articlesStore.articles.slice(0, 3))

// الكلمات الدلالية
const keywords = computed(() => {
  if (!article.value?.keywords) return []
  // Check if keywords is already an array
  if (Array.isArray(article.value.keywords)) {
    return article.value.keywords
  }
  // If string, split it
  return article.value.keywords.split(',').map((k: string) => k.trim())
})

// SEO Meta Tags - يتم تحديثها عند تغيير المقال
watchEffect(() => {
  if (article.value) {
    setArticleSeoMeta(article.value)
  }
})

// زيادة عدد المشاهدات وجلب أخبار ذات صلة بعد التحميل
onMounted(async () => {
  if (article.value) {
    // زيادة عدد المشاهدات
    articlesStore.incrementViews(article.value.id)
    
    // جلب أخبار ذات صلة
    if (article.value.category_id) {
      await articlesStore.fetchArticles({
        category: article.value.category_id,
        per_page: 3
      })
    }
  }
})

// Scroll to top عند تغيير الصفحة
watch(slug, () => {
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>

<style scoped>
/* تنسيق المحتوى */
:deep(.prose) {
  color: #1f2937;
  line-height: 2;
  direction: rtl;
  text-align: right;
  font-size: 1.375rem;
}

:deep(.prose h1) {
  font-size: 2.75rem;
  font-weight: 800;
  color: #111827;
  margin-top: 3rem;
  margin-bottom: 2rem;
  line-height: 1.3;
  letter-spacing: -0.025em;
}

:deep(.prose h2) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #111827;
  margin-top: 3rem;
  margin-bottom: 1.5rem;
  line-height: 1.35;
  letter-spacing: -0.02em;
}

:deep(.prose h3) {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
  line-height: 1.4;
}

:deep(.prose h4) {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-top: 2rem;
  margin-bottom: 1rem;
  line-height: 1.45;
}

:deep(.prose p) {
  margin-bottom: 1.75rem;
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2.1;
  text-align: justify;
  word-spacing: 0.05em;
  letter-spacing: 0.01em;
  white-space: pre-line; /* للحفاظ على الفواصل في النصوص القديمة */
}

:deep(.prose p:first-of-type) {
  margin-top: 0;
  font-size: 1.5rem;
  line-height: 2;
  color: #111827;
}

:deep(.prose p + p) {
  margin-top: 0;
}

:deep(.prose strong) {
  font-weight: 700;
  color: #111827;
}

:deep(.prose em) {
  font-style: italic;
}

:deep(.prose a) {
  color: #2563eb;
  text-decoration: underline;
  transition: color 0.2s;
}

:deep(.prose a:hover) {
  color: #1d4ed8;
}

:deep(.prose ul) {
  list-style-type: disc;
  margin-right: 2rem;
  margin-bottom: 1.5rem;
}

:deep(.prose ul li) {
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2;
  margin-top: 0.75rem;
  padding-right: 0.5rem;
}

:deep(.prose ol) {
  list-style-type: decimal;
  margin-right: 2rem;
  margin-bottom: 2rem;
}

:deep(.prose ol li) {
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2;
  margin-top: 0.75rem;
  padding-right: 0.5rem;
}

:deep(.prose blockquote) {
  border-right: 4px solid #2563eb;
  padding-right: 1.5rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
  background-color: #eff6ff;
  border-radius: 0.5rem;
}

:deep(.prose blockquote p) {
  font-size: 1.5rem;
  color: #374151;
  font-style: italic;
  font-weight: 500;
  line-height: 2;
}

:deep(.prose img) {
  border-radius: 0.5rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  width: 100%;
}

:deep(.prose hr) {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
  border-color: #d1d5db;
}

:deep(.prose table) {
  width: 100%;
  margin-top: 2rem;
  margin-bottom: 2rem;
  border-collapse: collapse;
}

:deep(.prose th) {
  background-color: #f3f4f6;
  padding: 0.75rem 1rem;
  text-align: right;
  font-weight: 700;
  border: 1px solid #d1d5db;
}

:deep(.prose td) {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
}

:deep(.prose pre) {
  background-color: #111827;
  color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

:deep(.prose code) {
  background-color: #f3f4f6;
  color: #dc2626;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 1.125rem;
}
</style>
