<template>
  <section class="mb-12" v-if="articles.length > 0">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">
          {{ title || category?.name || 'أخبار القسم' }}
        </h2>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-600">{{ subtitle }}</p>
      </div>
      <NuxtLink 
        v-if="category?.slug"
        :to="localePath('/category/' + category.slug)" 
        class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1 transition-colors">
        المزيد
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </NuxtLink>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in limit" :key="i" class="animate-pulse">
        <div class="bg-gray-200 h-48 rounded-lg mb-3"></div>
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Articles Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <ArticleCard 
        v-for="article in articles" 
        :key="article.id"
        :article="article"
        :show-category="false"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
const localePath = useLocalePath()

interface Props {
  title?: string | null
  subtitle?: string | null
  categorySlug?: string
  category?: {
    id: number
    name: string
    slug: string
  } | null
  limit?: number
  settings?: Record<string, any> | null
}

const props = withDefaults(defineProps<Props>(), {
  limit: 8
})

const { apiFetch } = useApi()
const articles = ref<any[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

// Fetch articles for this category
const fetchArticles = async () => {
  if (!props.categorySlug && !props.category?.slug) {
    console.warn('No category slug provided for CategoryNewsSection')
    return
  }

  loading.value = true
  error.value = null

  try {
    const slug = props.categorySlug || props.category?.slug
    const response = await apiFetch<any>(`/categories/${slug}/articles`, {
      params: {
        per_page: props.limit,
        sort: 'latest'
      }
    })

    if (response?.data) {
      articles.value = response.data
    }
  } catch (err: any) {
    error.value = err.message || 'حدث خطأ في جلب الأخبار'
    console.error('Error fetching category articles:', err)
  } finally {
    loading.value = false
  }
}

// Fetch on mount and when category changes
onMounted(() => {
  fetchArticles()
})

watch(() => props.categorySlug || props.category?.slug, () => {
  fetchArticles()
})
</script>
