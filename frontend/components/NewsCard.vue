<template>
  <NuxtLink 
    :to="getArticleLink(article)"
    class="group flex flex-col rounded-lg overflow-hidden"
  >
    <!-- الصورة في الأعلى -->
    <div class="relative w-full h-64 overflow-hidden">
      <img 
        :src="getImageUrl(article.thumbnail || article.image)" 
        :alt="article.title"
        class="w-full h-full object-fill"
        loading="lazy"
      />
    </div>

    <!-- المحتوى في الأسفل -->
    <div class="flex-1 flex flex-col justify-between p-4">
      <!-- العنوان الفرعي -->
      <p v-if="article.subtitle" class="text-sm text-blue-600 font-semibold mb-2 line-clamp-1">
        {{ decodeHtmlEntities(article.subtitle) }}
      </p>
      <!-- العنوان -->
      <h3 class="text-xl font-bold text-gray-900 line-clamp-3 leading-snug text-right mb-3">
        {{ decodeHtmlEntities(article.title) }}
      </h3>

      <!-- التاريخ والقسم -->
      <div class="flex items-center justify-between text-sm text-gray-600">
        <span v-if="article.category" class="font-semibold">{{ getCategoryName(article.category) }}</span>
        <time class="text-gray-500">{{ formatDate(article.published_at, 'relative') }}</time>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const props = defineProps<{
  article: Article
}>()

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { getArticleLink } = useArticleLink()
const { getCategoryName } = useLocalizedContent()
const { decodeHtmlEntities } = useHtmlEntities()

// Debug: تحقق من اسم القسم
watchEffect(() => {
  if (props.article.category) {
    console.log('📰 NewsCard - Article:', props.article.title?.substring(0, 30))
    console.log('📰 NewsCard - Category:', props.article.category)
    console.log('📰 NewsCard - Category Name (getCategoryName):', getCategoryName(props.article.category))
  }
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
