<template>
  <section class="mb-12">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <NuxtLink v-if="categorySlug" :to="localePath('/category/' + categorySlug)" class="flex items-center gap-2">
          <h2 class="text-3xl font-bold text-gray-900">{{ title }}</h2>
          <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </NuxtLink>
        <h2 v-else class="text-3xl font-bold text-gray-900">{{ title }}</h2>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in limit" :key="i" class="animate-pulse">
        <div class="bg-gray-200 h-48 rounded-lg mb-3"></div>
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Articles Grid -->
    <div v-else-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <NewsCard 
        v-for="article in articles" 
        :key="article.id"
        :article="article"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 text-gray-500">
      {{ locale === 'en' ? 'No news available' : 'لا توجد أخبار متاحة' }}
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

interface Props {
  title?: string
  categorySlug?: string
  categoryId?: number
  limit?: number
  type?: string
}

const props = withDefaults(defineProps<Props>(), {
  limit: 8,
  type: 'latest'
})

const { apiFetch } = useApi()
const { getImageUrl } = useImageUrl()
const { locale } = useI18n()
const articles = ref<any[]>([])
const loading = ref(true)

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
