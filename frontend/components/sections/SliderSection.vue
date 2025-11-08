<template>
  <section v-if="sliderArticles.length > 0" class="mb-12">
    <HeroSlider 
      :articles="sliderArticles" 
      :sideArticles="sidebarArticles"
    />
  </section>
</template>

<script setup lang="ts">
interface Props {
  title?: string | null
  subtitle?: string | null
  limit?: number
  settings?: Record<string, any> | null
}

const props = withDefaults(defineProps<Props>(), {
  limit: 10
})

const articlesStore = useArticlesStore()

const featuredArticles = computed(() => articlesStore.featuredArticles)

// السلايدر والأخبار الجانبية
const sliderArticles = computed(() => featuredArticles.value.slice(0, Math.min(6, props.limit)))

const sidebarArticles = computed(() => {
  // إذا كان هناك 10 أخبار أو أكثر، استخدم 6-10
  if (featuredArticles.value.length >= 10) {
    return featuredArticles.value.slice(6, 10)
  }
  // إذا كان أقل، استخدم الأخبار الموجودة
  const available = featuredArticles.value.slice(6)
  if (available.length >= 4) return available.slice(0, 4)
  
  // املأ بأحدث الأخبار إذا لم يكن هناك ما يكفي
  const combined = [...available, ...articlesStore.articles]
  return combined.slice(0, 4)
})

// Fetch featured articles on mount
onMounted(async () => {
  if (featuredArticles.value.length === 0) {
    await articlesStore.fetchFeaturedArticles(props.limit)
  }
})
</script>
