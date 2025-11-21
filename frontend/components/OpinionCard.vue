<template>
  <NuxtLink 
    :to="localePath('/opinions/' + opinion.slug)"
    class="group block bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100"
  >
    <!-- الصورة فقط -->
    <div v-if="opinion.image" class="relative h-44 overflow-hidden">
      <img 
        :src="getImageUrl(opinion.image)" 
        :alt="opinion.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
        loading="lazy"
      />
      
      <!-- Badge مميز -->
      <div v-if="opinion.is_featured" class="absolute top-3 left-3">
        <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          {{ $t('common.featured') }}
        </span>
      </div>

      <!-- الإحصائيات -->
      <div class="absolute bottom-3 flex items-center gap-3" style="right: 0.75rem;">
        <!-- المشاهدات -->
        <span class="flex items-center gap-1 bg-black/50 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          {{ formatNumber(opinion.views) }}
        </span>
        
        <!-- الإعجابات -->
        <span class="flex items-center gap-1 bg-black/50 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
          </svg>
          {{ formatNumber(opinion.likes) }}
        </span>
      </div>
    </div>

    <!-- المحتوى -->
    <div class="p-4">
      <!-- العنوان -->
      <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-orange-600 transition-colors leading-tight">
        {{ getOpinionTitle }}
      </h3>

      <!-- الكاتب والتاريخ -->
      <div class="pt-3 border-t border-gray-200">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <img 
              v-if="opinion.writer?.image" 
              :src="getImageUrl(opinion.writer.image)" 
              :alt="opinion.writer.name"
              loading="lazy"
              class="w-20 h-20 rounded-full border-3 border-orange-400"
            />
            <div>
              <p class="text-lg font-bold text-gray-900">{{ getWriterName }}</p>
              <time class="text-sm text-gray-500">{{ formatDate(opinion.published_at, 'relative') }}</time>
            </div>
          </div>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Opinion } from '~/types'

const localePath = useLocalePath()
const { locale } = useI18n()

const props = defineProps<{
  opinion: Opinion
}>()

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()

// دالة للحصول على عنوان المقال المترجم
const getOpinionTitle = computed(() => {
  return locale.value === 'en' && props.opinion.title_en ? props.opinion.title_en : props.opinion.title
})

// دالة للحصول على اسم الكاتب المترجم
const getWriterName = computed(() => {
  if (!props.opinion.writer) return ''
  return locale.value === 'en' && props.opinion.writer.name_en ? props.opinion.writer.name_en : props.opinion.writer.name
})

const { t } = useI18n()

const formatNumber = (num: number): string => {
  const isEnglish = locale.value === 'en'
  if (num >= 1000000) return (num / 1000000).toFixed(1) + (isEnglish ? 'M' : 'م')
  if (num >= 1000) return (num / 1000).toFixed(1) + (isEnglish ? 'K' : 'ألف')
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
</style>
