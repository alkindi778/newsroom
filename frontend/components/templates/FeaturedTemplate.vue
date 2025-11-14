<template>
  <section class="mb-12">
    <!-- Header -->
    <div class="mb-6">
      <NuxtLink v-if="categorySlug" :to="`/category/${categorySlug}`" class="flex items-center gap-2">
        <h2 class="text-3xl font-bold text-gray-900">{{ title }}</h2>
        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </NuxtLink>
      <h2 v-else class="text-3xl font-bold text-gray-900">{{ title }}</h2>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="animate-pulse">
      <div class="bg-gray-200 h-96 rounded-lg mb-4"></div>
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-gray-200 h-32 rounded-lg"></div>
        <div class="bg-gray-200 h-32 rounded-lg"></div>
        <div class="bg-gray-200 h-32 rounded-lg"></div>
      </div>
    </div>

    <!-- Featured Article + Side Articles -->
    <div v-else-if="articles.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- خبر رئيسي كبير -->
      <div v-if="mainArticle" class="lg:col-span-2">
        <NuxtLink :to="getArticleLink(mainArticle)" class="group block">
          <div class="relative h-96 rounded-lg overflow-hidden mb-4">
            <img 
              :src="getImageUrl(mainArticle.image, 'large')" 
              :alt="mainArticle.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            
            <!-- Category Badge -->
            <div v-if="mainArticle.category" class="absolute top-4 right-4">
              <span class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded">
                {{ mainArticle.category.name }}
              </span>
            </div>

            <!-- Content -->
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
              <h3 class="text-3xl font-bold mb-2 line-clamp-2 group-hover:text-blue-300 transition-colors">
                {{ mainArticle.title }}
              </h3>
              <p v-if="mainArticle.excerpt" class="text-gray-200 text-sm line-clamp-2 mb-3">
                {{ mainArticle.excerpt }}
              </p>
              <div class="flex items-center gap-4 text-sm text-gray-300">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ formatDate(mainArticle.published_at) }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  {{ formatNumber(mainArticle.views || 0) }}
                </span>
              </div>
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- أخبار جانبية -->
      <div v-if="sideArticles.length > 0" class="space-y-4">
        <NuxtLink 
          v-for="article in sideArticles" 
          :key="article.id"
          :to="getArticleLink(article)"
          class="group flex gap-3 bg-white hover:bg-gray-50 rounded-lg overflow-hidden border border-gray-200 transition-all"
        >
          <img 
            v-if="article.image"
            :src="getImageUrl(article.image, 'thumbnail')" 
            :alt="article.title"
            class="w-24 h-24 object-cover flex-shrink-0"
          />
          <div class="flex-1 p-3">
            <h4 class="font-bold text-sm line-clamp-2 group-hover:text-blue-600 transition-colors mb-2">
              {{ article.title }}
            </h4>
            <div class="flex items-center gap-2 text-xs text-gray-500">
              <span>{{ formatDate(article.published_at) }}</span>
            </div>
          </div>
        </NuxtLink>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 text-gray-500">
      لا توجد مقالات متاحة
    </div>

    <!-- زر المزيد -->
    <div v-if="articles.length >= limit" class="mt-8">
      <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-gray-300"></div>
        <NuxtLink
          :to="categorySlug ? `/category/${categorySlug}` : '/news'"
          class="inline-flex items-center gap-2 px-6 py-2 border border-gray-900 text-gray-900 font-semibold whitespace-nowrap rounded-md"
        >
          <span>المزيد</span>
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
const articles = ref<Article[]>([])
const loading = ref(true)

const mainArticle = computed(() => articles.value[0])
const sideArticles = computed(() => articles.value.slice(1, 7))

const formatDate = (date?: string) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('ar-SA', { 
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
