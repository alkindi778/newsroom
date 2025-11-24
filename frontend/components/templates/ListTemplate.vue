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
    <div v-if="loading" class="space-y-6 animate-pulse">
      <!-- First Row Skeleton -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Two Small Cards -->
        <div class="grid grid-cols-1 gap-6">
          <div>
            <div class="bg-gray-200 h-48 rounded-lg mb-3"></div>
            <div class="bg-gray-200 h-4 rounded w-3/4"></div>
          </div>
          <div>
            <div class="bg-gray-200 h-48 rounded-lg mb-3"></div>
            <div class="bg-gray-200 h-4 rounded w-3/4"></div>
          </div>
        </div>
        <!-- Large Card -->
        <div class="lg:col-span-2">
          <div class="bg-gray-200 h-96 rounded-lg mb-4"></div>
          <div class="bg-gray-200 h-6 rounded w-2/3 mb-2"></div>
          <div class="bg-gray-200 h-4 rounded w-full"></div>
        </div>
      </div>
      <!-- Second Row Skeleton -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="i in 4" :key="i">
          <div class="bg-gray-200 h-48 rounded-lg mb-3"></div>
          <div class="bg-gray-200 h-4 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Magazine Layout - Titles Below Images -->
    <div v-else-if="articles.length > 0" class="space-y-6">
      <!-- First Row: 1 Large (Full width on mobile, Right + 2 Small on desktop) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Large Featured Card (Full width on mobile, Right Side in RTL on desktop) -->
        <article v-if="articles[2]" class="lg:col-span-2 group">
          <NuxtLink :to="getArticleLink(articles[2])" class="block">
            <div class="relative h-64 md:h-96 rounded-lg overflow-hidden mb-4">
              <img 
                :src="getImageUrl(articles[2].image, 'large')" 
                :alt="articles[2].title"
                class="w-full h-full object-cover"
              />
              <div v-if="articles[2].category" 
                   class="absolute top-4 right-4 px-3 py-1.5 bg-red-600 text-white font-bold rounded">
                {{ getCategoryName(articles[2].category) }}
              </div>
            </div>
            <div class="space-y-3">
              <p v-if="articles[2].subtitle" class="text-sm md:text-base text-blue-600 font-semibold">
                {{ articles[2].subtitle }}
              </p>
              <h2 class="text-lg md:text-2xl font-bold text-gray-900 line-clamp-2">
                {{ getArticleTitle(articles[2]) }}
              </h2>
              <p class="hidden md:block text-base text-gray-600 leading-relaxed line-clamp-5">
                {{ getExcerpt(articles[2]) }}
              </p>
            </div>
          </NuxtLink>
        </article>

        <!-- Two Small Cards (Hidden on mobile, Left Side in RTL on desktop) -->
        <div class="hidden lg:grid grid-cols-1 gap-6">
          <article 
            v-for="article in articles.slice(0, 2)" 
            :key="article.id"
            class="group"
          >
            <NuxtLink :to="getArticleLink(article)" class="block">
              <div class="relative h-48 rounded-lg overflow-hidden mb-3">
                <img 
                  :src="getImageUrl(article.image, 'medium')" 
                  :alt="article.title"
                  class="w-full h-full object-cover"
                />
                <div v-if="article.category" 
                     class="absolute top-3 right-3 px-2 py-1 bg-red-600 text-white text-xs font-bold rounded">
                  {{ getCategoryName(article.category) }}
                </div>
              </div>
              <p v-if="article.subtitle" class="text-sm text-blue-600 font-semibold mb-1">
                {{ article.subtitle }}
              </p>
              <h3 class="text-lg font-bold text-gray-900 line-clamp-2">
                {{ getArticleTitle(article) }}
              </h3>
            </NuxtLink>
          </article>
        </div>
      </div>

      <!-- Second Row: 2 Equal Cards on Mobile, 4 on Desktop -->
      <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <article 
          v-for="article in articles.slice(3, 7)" 
          :key="article.id"
          class="group"
        >
          <NuxtLink :to="getArticleLink(article)" class="block">
            <div class="relative w-full aspect-square rounded-lg overflow-hidden mb-2 md:mb-3">
              <img 
                :src="getImageUrl(article.image, 'medium')" 
                :alt="article.title"
                class="w-full h-full object-cover"
              />
              <div v-if="article.category" 
                   class="absolute top-2 right-2 px-2 py-1 bg-red-600 text-white text-xs font-bold rounded">
                {{ getCategoryName(article.category) }}
              </div>
            </div>
            <p v-if="article.subtitle" class="text-xs text-blue-600 font-semibold mb-1 line-clamp-1">
              {{ article.subtitle }}
            </p>
            <h3 class="text-sm md:text-base font-bold text-gray-900 line-clamp-2">
              {{ getArticleTitle(article) }}
            </h3>
          </NuxtLink>
        </article>
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
import type { Article } from '~/types'

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
  limit: 7,
  type: 'latest'
})

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
  return title
}

const formatDate = (date: string) => {
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

const getExcerpt = (article: any) => {
  if (article.excerpt) return article.excerpt
  if (article.content) {
    // استخراج أول 200 حرف من المحتوى
    const textContent = article.content.replace(/<[^>]*>/g, '').trim()
    return textContent.substring(0, 200) + '...'
  }
  return locale.value === 'en' ? 'Click to read more details about this important news...' : 'اضغط لقراءة المزيد من التفاصيل حول هذا الخبر المهم...'
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
