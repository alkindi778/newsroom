<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="bars" size="lg" fullScreen text="جاري تحميل المقال..." color="secondary" />

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h2>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <NuxtLink to="/opinions" class="text-orange-600 hover:text-orange-700 font-semibold">
        العودة لمقالات الرأي
      </NuxtLink>
    </div>

    <!-- Opinion Content -->
    <div v-else-if="opinion" class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
      <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600">
          <li><NuxtLink to="/" class="hover:text-orange-600">الرئيسية</NuxtLink></li>
          <li>/</li>
          <li><NuxtLink to="/opinions" class="hover:text-orange-600">مقالات الرأي</NuxtLink></li>
          <li>/</li>
          <li class="text-gray-900 font-semibold truncate">{{ opinion.title }}</li>
        </ol>
      </nav>

      <!-- Homepage Top Advertisement (will also show here) -->
      <div class="my-8">
        <AdvertisementZone position="homepage_top" page="opinion" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Opinion Top Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_top" page="opinion" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Opinion Header -->
      <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Badge مميز -->
        <div v-if="opinion.is_featured" class="inline-block">
          <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 py-2 rounded-br-lg font-bold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            مقال مميز
          </span>
        </div>

        <!-- العنوان -->
        <div class="p-8">
          <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
            {{ opinion.title }}
          </h1>

          <!-- المقتطف -->
          <p v-if="opinion.excerpt" class="text-xl text-gray-600 mb-6 leading-relaxed">
            {{ opinion.excerpt }}
          </p>

          <!-- معلومات الكاتب -->
          <div class="flex items-center justify-between pb-6 border-b border-gray-200">
            <NuxtLink 
              v-if="opinion.writer" 
              :to="`/writers/${opinion.writer.slug}`"
              class="flex items-center gap-3 group"
            >
              <img 
                :src="getImageUrl(opinion.writer.image)" 
                :alt="opinion.writer.name"
                loading="lazy"
                class="w-16 h-16 rounded-full border-2 border-orange-400 group-hover:border-orange-600 transition-colors"
              />
              <div>
                <p class="font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                  {{ opinion.writer.name }}
                </p>
                <p v-if="opinion.writer.specialty" class="text-sm text-gray-600">
                  {{ opinion.writer.specialty }}
                </p>
                <time class="text-xs text-gray-500">
                  {{ formatDate(opinion.published_at, 'full') }}
                </time>
              </div>
            </NuxtLink>

            <!-- الإحصائيات -->
            <div class="flex items-center gap-4 text-sm text-gray-600">
              <div class="flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>{{ (opinion.views || 0).toLocaleString('ar') }}</span>
              </div>
              <button 
                @click="handleLike"
                :disabled="hasLiked"
                class="flex items-center gap-1 hover:text-red-500 transition-colors disabled:opacity-50"
                :class="{ 'text-red-500': hasLiked }"
              >
                <svg class="w-5 h-5" :class="{ 'fill-current': hasLiked }" :fill="hasLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span>{{ (opinion.likes || 0).toLocaleString('ar') }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- الصورة الرئيسية -->
        <div v-if="opinion.image" class="relative h-96">
          <img 
            :src="getImageUrl(opinion.image)" 
            :alt="opinion.title"
            loading="eager"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- المحتوى -->
        <div class="p-4 sm:p-6 md:p-8" :class="{ 'pt-12': !opinion.image }">
          <div 
            class="article-content"
            style="font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; white-space: pre-line;"
            v-html="opinion.content"
          ></div>

          <!-- Opinion Middle Advertisement -->
          <div class="my-12">
            <AdvertisementZone position="article_middle" page="opinion" :auto-rotate="false" :show-dots="false" />
          </div>

          <!-- الكلمات الدلالية -->
          <div v-if="opinion.keywords" class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-3">الكلمات الدلالية:</h3>
            <div class="flex flex-wrap gap-2">
              <NuxtLink 
                v-for="keyword in keywords" 
                :key="keyword"
                :to="`/search?q=${encodeURIComponent(keyword)}`"
                class="px-3 py-1 bg-orange-100 hover:bg-orange-600 hover:text-white rounded-full text-sm text-orange-700 transition-all duration-200 cursor-pointer inline-block"
              >
                #{{ keyword }}
              </NuxtLink>
            </div>
          </div>

          <!-- أزرار المشاركة -->
          <div class="mt-8 pt-8 border-t border-gray-200">
            <ShareButtons :url="currentUrl" :title="opinion.title" />
          </div>
        </div>
      </article>

      <!-- معلومات الكاتب -->
      <div v-if="opinion.writer" class="mt-8 bg-gradient-to-br from-orange-50 to-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">عن الكاتب</h3>
        <div class="flex items-start gap-4">
          <NuxtLink :to="`/writers/${opinion.writer.slug}`">
            <img 
              :src="getImageUrl(opinion.writer.image)" 
              :alt="opinion.writer.name"
              loading="lazy"
              class="w-20 h-20 rounded-full border-2 border-orange-400"
            />
          </NuxtLink>
          <div class="flex-1">
            <NuxtLink :to="`/writers/${opinion.writer.slug}`">
              <h4 class="text-lg font-bold text-gray-900 hover:text-orange-600 transition-colors">
                {{ opinion.writer.name }}
              </h4>
            </NuxtLink>
            <p v-if="opinion.writer.specialty" class="text-sm text-gray-600 mb-2">
              {{ opinion.writer.specialty }}
            </p>
            <p v-if="opinion.writer.bio" class="text-gray-700 text-sm">
              {{ opinion.writer.bio }}
            </p>
            <NuxtLink 
              :to="`/writers/${opinion.writer.slug}`"
              class="inline-block mt-3 text-orange-600 hover:text-orange-700 font-semibold text-sm"
            >
              عرض جميع مقالات الكاتب ←
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Opinion Bottom Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_bottom" page="opinion" :auto-rotate="false" :show-dots="false" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const opinionsStore = useOpinionsStore()
const config = useRuntimeConfig()
const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { setOpinionSeoMeta } = useAppSeoMeta()

const slug = computed(() => route.params.slug as string)
const hasLiked = ref(false)

// جلب مقال الرأي مع SSR
const { data: opinionData, error: fetchError } = await useAsyncData(
  `opinion-${slug.value}`,
  async () => {
    const response = await $fetch<any>(`${apiBase}/opinions/${slug.value}`)
    return response?.data || null
  },
  {
    watch: [slug]
  }
)

const opinion = computed(() => opinionData.value)
const loading = ref(false)
const error = computed(() => fetchError.value?.message || null)

// الكلمات الدلالية
const keywords = computed(() => {
  if (!opinion.value?.keywords) return []
  return opinion.value.keywords.split(',').map((k: string) => k.trim())
})

// الرابط الحالي
const currentUrl = computed(() => {
  return `${(config as any).public.siteUrl}${route.fullPath}`
})

// الإعجاب بالمقال
const handleLike = async () => {
  if (opinion.value && !hasLiked.value) {
    await opinionsStore.likeOpinion(opinion.value.id)
    hasLiked.value = true
  }
}

// SEO Meta Tags - يتم تحديثها عند تغيير مقال الرأي
watchEffect(() => {
  if (opinion.value) {
    setOpinionSeoMeta(opinion.value)
  }
})

// إعادة تعيين الإعجاب عند تغيير الصفحة
watch(slug, () => {
  hasLiked.value = false
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>

<style scoped>
/* تنسيق المحتوى */
.article-content {
  color: #1f2937;
  line-height: 2;
  text-align: justify;
  direction: rtl;
  font-size: 1.125rem;
  max-width: 100%;
  word-wrap: break-word;
}

:deep(.article-content h1) {
  font-size: 1.875rem;
  font-weight: 800;
  color: #111827;
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
  line-height: 1.3;
}

:deep(.article-content h2) {
  font-size: 1.625rem;
  font-weight: 700;
  color: #111827;
  margin-top: 2rem;
  margin-bottom: 1rem;
  line-height: 1.4;
}

:deep(.article-content h3) {
  font-size: 1.375rem;
  font-weight: 700;
  color: #374151;
  margin-top: 1.75rem;
  margin-bottom: 0.875rem;
  line-height: 1.5;
}

:deep(.article-content p) {
  margin-bottom: 1.5rem;
  font-size: 1.125rem;
  line-height: 2;
  color: #1f2937;
  text-align: justify;
}

:deep(.article-content strong) {
  font-weight: 700;
  color: #111827;
}

:deep(.article-content em) {
  font-style: italic;
  color: #374151;
}

:deep(.article-content a) {
  color: #ea580c;
  text-decoration: underline;
  transition: color 0.2s;
}

:deep(.article-content a:hover) {
  color: #c2410c;
}

:deep(.article-content ul),
:deep(.article-content ol) {
  margin-right: 1.5rem;
  margin-bottom: 1.5rem;
  line-height: 2;
}

:deep(.article-content ul) {
  list-style-type: disc;
}

:deep(.article-content ol) {
  list-style-type: decimal;
}

:deep(.article-content li) {
  margin-bottom: 0.5rem;
  padding-right: 0.5rem;
}

:deep(.article-content blockquote) {
  border-right: 4px solid #ea580c;
  padding-right: 1.5rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  margin: 2rem 0;
  background: linear-gradient(to left, #fff7ed, transparent);
  font-style: italic;
  font-size: 1.125rem;
  color: #4b5563;
}

:deep(.article-content img) {
  border-radius: 0.75rem;
  margin: 2rem auto;
  max-width: 100%;
  height: auto;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

:deep(.article-content pre) {
  background-color: #1f2937;
  color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1.5rem 0;
}

:deep(.article-content code) {
  background-color: #f3f4f6;
  color: #ea580c;
  padding: 0.2rem 0.4rem;
  border-radius: 0.25rem;
  font-size: 0.9em;
}

:deep(.article-content table) {
  width: 100%;
  border-collapse: collapse;
  margin: 2rem 0;
}

:deep(.article-content th),
:deep(.article-content td) {
  border: 1px solid #e5e7eb;
  padding: 0.75rem;
  text-align: right;
}

:deep(.article-content th) {
  background-color: #f9fafb;
  font-weight: 700;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  :deep(.article-content) {
    line-height: 1.9;
  }

  :deep(.article-content h1) {
    font-size: 1.5rem;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
  }

  :deep(.article-content h2) {
    font-size: 1.375rem;
    margin-top: 1.5rem;
    margin-bottom: 0.875rem;
  }

  :deep(.article-content h3) {
    font-size: 1.25rem;
    margin-top: 1.25rem;
    margin-bottom: 0.75rem;
  }

  :deep(.article-content p) {
    font-size: 1rem;
    margin-bottom: 1.25rem;
    line-height: 1.9;
  }

  :deep(.article-content blockquote) {
    padding-right: 1rem;
    font-size: 1rem;
    margin: 1.5rem 0;
  }

  :deep(.article-content ul),
  :deep(.article-content ol) {
    margin-right: 1rem;
  }

  :deep(.article-content img) {
    margin: 1.5rem auto;
  }
}
</style>
