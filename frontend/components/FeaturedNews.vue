<template>
  <div class="relative h-[500px] rounded-2xl overflow-hidden shadow-2xl group">
    <!-- الصورة الخلفية -->
    <img 
      :src="getImageUrl(article.image)" 
      :alt="article.title"
      loading="lazy"
      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
    />
    
    <!-- التدرج -->
    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
    
    <!-- المحتوى -->
    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
      <!-- القسم -->
      <NuxtLink 
        v-if="article.category" 
        :to="localePath('/category/' + article.category.slug)"
        class="inline-block bg-accent hover:bg-accent-600 px-4 py-2 rounded-full text-sm font-bold mb-4 transition-colors"
      >
        {{ getCategoryName(article.category) }}
      </NuxtLink>

      <!-- العنوان الفرعي -->
      <p v-if="article.subtitle" class="text-lg md:text-xl text-primary font-semibold mb-2">
        {{ article.subtitle }}
      </p>

      <!-- العنوان -->
      <NuxtLink :to="getArticleLink(article)">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 line-clamp-3 hover:text-primary transition-colors cursor-pointer">
          {{ article.title }}
        </h2>
      </NuxtLink>

      <!-- المقتطف -->
      <p v-if="article.excerpt" class="text-gray-200 text-lg mb-4 line-clamp-2">
        {{ article.excerpt }}
      </p>

      <!-- المعلومات -->
      <div class="flex items-center gap-6 text-sm">
        <!-- الكاتب -->
        <div class="flex items-center gap-2">
          <img 
            v-if="article.user?.avatar" 
            :src="article.user.avatar" 
            :alt="article.user.name"
            loading="lazy"
            class="w-10 h-10 rounded-full border-2 border-white"
          />
          <span class="font-semibold">{{ article.user?.name }}</span>
        </div>

        <!-- التاريخ -->
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <time>{{ formatDate(article.published_at, 'relative') }}</time>
        </div>

        <!-- المشاهدات -->
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          <span>{{ formatNumber(article.views) }} مشاهدة</span>
        </div>
      </div>
    </div>

    <!-- زر القراءة -->
    <NuxtLink 
      :to="getArticleLink(article)"
      class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-md hover:bg-white/30 text-white px-8 py-4 rounded-full font-bold text-lg opacity-0 group-hover:opacity-100 transition-all duration-300"
    >
      اقرأ المزيد ←
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import type { Article } from '~/types'

const localePath = useLocalePath()
const { getCategoryName } = useLocalizedContent()

const props = defineProps<{
  article: Article
}>()

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { getArticleLink } = useArticleLink()

const formatNumber = (num: number): string => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'م'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'ألف'
  return num.toString()
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
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
