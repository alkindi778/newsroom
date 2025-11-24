<template>
  <section class="mb-12">
    <!-- Header -->
    <div class="mb-6">
      <NuxtLink v-if="categorySlug" :to="localePath('/category/' + categorySlug)" class="flex items-center gap-2">
        <h2 class="text-3xl font-bold text-gray-900">{{ title }}</h2>
        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </NuxtLink>
      <h2 v-else class="text-3xl font-bold text-gray-900">{{ title }}</h2>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="animate-pulse grid grid-cols-12 gap-6">
      <div class="col-span-8">
        <div class="bg-gray-200 h-96 rounded-lg mb-4"></div>
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-gray-200 h-32 rounded-lg"></div>
          <div class="bg-gray-200 h-32 rounded-lg"></div>
        </div>
      </div>
      <div class="col-span-4 space-y-4">
        <div class="bg-gray-200 h-32 rounded-lg"></div>
        <div class="bg-gray-200 h-32 rounded-lg"></div>
        <div class="bg-gray-200 h-32 rounded-lg"></div>
      </div>
    </div>

    <!-- Magazine Layout -->
    <div v-else-if="articles.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Left Column: Main + Secondary -->
      <div class="lg:col-span-8 space-y-6">
        <!-- Main Article -->
        <NuxtLink v-if="mainArticle" :to="getArticleLink(mainArticle)" class="group block">
          <div class="relative h-96 rounded-lg overflow-hidden">
            <img 
              :src="getImageUrl(mainArticle.image, 'large')" 
              :alt="mainArticle.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            
            <!-- Category Badge -->
            <div v-if="mainArticle.category" class="absolute top-4 right-4">
              <span class="px-3 py-1 bg-red-600 text-white text-sm font-bold rounded">
                {{ getCategoryName(mainArticle.category) }}
              </span>
            </div>

            <!-- Content -->
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
              <h3 class="text-3xl font-bold mb-3 line-clamp-2">
                {{ getArticleTitle(mainArticle) }}
              </h3>
              <p v-if="mainArticle.excerpt || mainArticle.excerpt_en" class="text-gray-200 mb-4 line-clamp-2">
                {{ getArticleExcerpt(mainArticle) }}
              </p>
              <div class="flex items-center gap-4 text-sm">
                <span>{{ formatDate(mainArticle.published_at) }}</span>
                <span>•</span>
                <span>{{ formatNumber(mainArticle.views || 0) }} {{ $t('article.views') }}</span>
              </div>
            </div>
          </div>
        </NuxtLink>

        <!-- Secondary Articles (2 columns) -->
        <div v-if="secondaryArticles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <NuxtLink 
            v-for="article in secondaryArticles" 
            :key="article.id"
            :to="getArticleLink(article)"
            class="group"
          >
            <div class="relative h-48 rounded-lg overflow-hidden mb-3">
              <img 
                :src="getImageUrl(article.image, 'medium')" 
                :alt="article.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              
              <!-- Category -->
              <div v-if="article.category" class="absolute top-3 right-3">
                <span class="px-2 py-1 bg-blue-600 text-white text-xs font-medium rounded">
                  {{ getCategoryName(article.category) }}
                </span>
              </div>
            </div>
            <h4 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
              {{ getArticleTitle(article) }}
            </h4>
            <div class="text-sm text-gray-500">
              {{ formatDate(article.published_at) }}
            </div>
          </NuxtLink>
        </div>
      </div>

      <!-- Right Column: Side Articles -->
      <div v-if="sideArticles.length > 0" class="lg:col-span-4 space-y-4">
        <div 
          v-for="(article, index) in sideArticles" 
          :key="article.id"
          class="group"
        >
          <NuxtLink :to="getArticleLink(article)" class="flex gap-3">
            <!-- Number Badge -->
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white font-bold text-xl flex items-center justify-center rounded">
              {{ index + 1 }}
            </div>

            <!-- Image (optional) -->
            <img 
              v-if="article.image"
              :src="getImageUrl(article.image, 'thumbnail')" 
              :alt="article.title"
              class="w-20 h-20 object-cover rounded flex-shrink-0"
            />

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <h5 class="font-bold text-sm line-clamp-3 group-hover:text-blue-600 transition-colors mb-2">
                {{ getArticleTitle(article) }}
              </h5>
              <div class="text-xs text-gray-500">
                {{ formatDate(article.published_at) }}
              </div>
            </div>
          </NuxtLink>

          <!-- Divider -->
          <div v-if="index < sideArticles.length - 1" class="mt-4 border-t border-gray-200"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 text-gray-500">
      {{ locale === 'en' ? 'No articles available' : 'لا توجد مقالات متاحة' }}
    </div>

    <!-- زر المزيد -->
    <div v-if="articles.length >= limit" class="mt-8">
      <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-gray-300"></div>
        <NuxtLink
          :to="categorySlug ? `/category/${categorySlug}` : '/news'"
          class="inline-flex items-center gap-2 px-6 py-2 border border-gray-900 text-gray-900 font-semibold whitespace-nowrap rounded-md"
        >
          <span>{{ locale === 'en' ? 'More' : 'المزيد' }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const localePath = useLocalePath()
const { getCategoryName } = useLocalizedContent()

interface Props {
  title?: string
  categorySlug?: string
  categoryId?: number
  limit?: number
  type?: string
}

const props = withDefaults(defineProps<Props>(), {
  limit: 9,
  type: 'latest'
})

import type { Article } from '~/types'

const { apiFetch } = useApi()
const { getImageUrl } = useImageUrl()
const { getArticleLink } = useArticleLink()
const { locale } = useI18n()
const articles = ref<Article[]>([])
const loading = ref(true)

// دالة للحصول على عنوان المقال المترجم
const getArticleTitle = (article: Article) => {
  const isEnglish = locale.value === 'en'
  const hasTranslation = !!article.title_en
  const title = isEnglish && hasTranslation ? article.title_en : article.title
  
  console.log('📰 MagazineTemplate - getArticleTitle:', {
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

// دالة للحصول على ملخص المقال المترجم
const getArticleExcerpt = (article: any) => {
  const isEnglish = locale.value === 'en'
  const hasTranslation = !!article.excerpt_en
  return isEnglish && hasTranslation ? article.excerpt_en : article.excerpt
}

const mainArticle = computed(() => articles.value[0])
const secondaryArticles = computed(() => articles.value.slice(1, 3))
const sideArticles = computed(() => articles.value.slice(3, 9))

const formatDate = (date?: string) => {
  if (!date) return ''
  const localeCode = locale.value === 'en' ? 'en-US' : 'ar-SA'
  return new Date(date).toLocaleDateString(localeCode, { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const formatNumber = (num: number) => {
  return num.toLocaleString('ar')
}

const fetchArticles = async () => {
  loading.value = true
  try {
    let endpoint = '/articles'
    const params: any = { per_page: props.limit }

    if (props.categorySlug) {
      endpoint = `/categories/${props.categorySlug}/articles`
    } else if (props.type === 'latest') {
      endpoint = '/articles/latest'
    } else if (props.type === 'popular') {
      endpoint = '/articles/popular'
    }

    const response = await apiFetch<any>(endpoint, { params })
    
    if (response?.data) {
      articles.value = response.data
    }
  } catch (error) {
    console.error('Error fetching articles:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchArticles()
})
</script>
