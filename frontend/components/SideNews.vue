<template>
  <div class="grid grid-cols-2 gap-2 lg:flex lg:flex-col lg:h-[520px] p-3 lg:p-0">
    <NuxtLink
      v-for="article in articles"
      :key="article.id"
      :to="`/news/${article.slug}`"
      class="flex flex-row items-start gap-2 lg:gap-3 p-2 lg:p-3 hover:bg-gray-50 transition-colors rounded-lg lg:min-h-[120px]"
    >
      <!-- الصورة على اليمين -->
      <div class="w-20 h-20 lg:w-24 lg:h-24 flex-shrink-0 rounded overflow-hidden bg-gray-900">
        <img
          :src="getImageUrl(article.thumbnail || article.image)"
          :alt="article.title"
          loading="lazy"
          class="w-full h-full object-cover"
        />
      </div>

      <!-- العنوان على اليسار -->
      <div class="flex-1 text-right">
        <!-- العنوان الفرعي -->
        <p v-if="article.subtitle" class="text-xs lg:text-sm text-blue-600 font-semibold mb-1 line-clamp-1">
          {{ article.subtitle }}
        </p>
        <h3 class="text-gray-900 text-lg lg:text-xl font-bold leading-tight line-clamp-3">
          {{ article.title }}
        </h3>
        
        <!-- التاريخ (اختياري) -->
        <time v-if="showDate" class="text-xs text-gray-500 mt-1 block">
          {{ formatDate(article.published_at, 'relative') }}
        </time>
      </div>
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const props = defineProps<{
  articles: Article[]
  showDate?: boolean
}>()

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
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
