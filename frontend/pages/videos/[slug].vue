<template>
  <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 md:py-8">
    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="bars" size="lg" fullScreen text="جاري تحميل الفيديو..." />

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h2>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <NuxtLink to="/" class="text-blue-600 hover:text-blue-700 font-semibold">
        العودة للرئيسية
      </NuxtLink>
    </div>

    <!-- Video Content -->
    <div v-else-if="video" class="max-w-6xl mx-auto">
      <!-- Breadcrumb -->
      <nav class="mb-4 md:mb-6 text-xs sm:text-sm px-2 sm:px-0">
        <ol class="flex items-center gap-1 sm:gap-2 text-gray-600">
          <li><NuxtLink :to="localePath('/')" class="hover:text-blue-600">{{ locale === 'en' ? 'Home' : 'الرئيسية' }}</NuxtLink></li>
          <li>/</li>
          <li><NuxtLink :to="localePath('/videos')" class="hover:text-blue-600">{{ locale === 'en' ? 'Videos' : 'الفيديوهات' }}</NuxtLink></li>
          <li>/</li>
          <li class="text-gray-900 font-semibold truncate">{{ getVideoTitle(video) }}</li>
        </ol>
      </nav>

      <!-- Video Player -->
      <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Video Embed -->
        <div class="relative w-full bg-black" style="padding-bottom: 56.25%;">
          <iframe
            :src="video.embed_url"
            :title="getVideoTitle(video)"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            class="absolute inset-0 w-full h-full"
          ></iframe>
        </div>

        <!-- Video Info -->
        <div class="p-4 sm:p-6 md:p-8">
          <!-- Title -->
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
            {{ getVideoTitle(video) }}
          </h1>

          <!-- Meta Info -->
          <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 pb-4 border-b border-gray-200 mb-6">
            <!-- Views -->
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <span>{{ formatNumber(video.views) }} مشاهدة</span>
            </div>

            <!-- Duration -->
            <div v-if="video.duration" class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ video.duration }}</span>
            </div>

            <!-- Date -->
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>{{ formatDate(video.published_at, 'full') }}</span>
            </div>
          </div>

          <!-- Description -->
          <div v-if="getVideoDescription(video)" class="prose prose-lg max-w-none text-right leading-relaxed text-gray-700">
            <p style="white-space: pre-line;">{{ getVideoDescription(video) }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3 mt-6">
            <button
              @click="likeVideo"
              class="flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
              <span>{{ formatNumber(video.likes) }}</span>
            </button>

            <button
              @click="shareVideo"
              class="flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
              <span>مشاركة</span>
            </button>
          </div>
        </div>
      </article>

      <!-- Related Videos -->
      <section v-if="relatedVideos.length > 0" class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ locale === 'en' ? 'Related Videos' : 'فيديوهات ذات صلة' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NuxtLink
            v-for="relatedVideo in relatedVideos"
            :key="relatedVideo.id"
            :to="localePath(`/videos/${relatedVideo.slug}`)"
            class="group"
          >
            <div class="relative overflow-hidden bg-gray-900 rounded-lg">
              <img
                :src="relatedVideo.thumbnail"
                :alt="getVideoTitle(relatedVideo)"
                loading="lazy"
                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
              />
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
              <div v-if="relatedVideo.duration" class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 text-xs font-bold rounded">
                {{ relatedVideo.duration }}
              </div>
            </div>
            <h3 class="mt-2 text-base font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
              {{ getVideoTitle(relatedVideo) }}
            </h3>
          </NuxtLink>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const config = useRuntimeConfig()

const video = ref<any>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const relatedVideos = ref<any[]>([])

const { apiFetch } = useApi()
const { formatDate } = useDateFormat()
const { locale } = useI18n()
const localePath = useLocalePath()

// دوال للحصول على النصوص المترجمة
const getVideoTitle = (video: any) => {
  return locale.value === 'en' && video.title_en ? video.title_en : video.title
}

const getVideoDescription = (video: any) => {
  return locale.value === 'en' && video.description_en ? video.description_en : video.description
}

// Format numbers
const formatNumber = (num: number) => {
  return new Intl.NumberFormat('ar-EG').format(num)
}

// Fetch video
const slug = computed(() => route.params.slug as string)

// جلب الفيديو مع SSR
const { data: videoData, error: fetchError } = await useAsyncData(
  `video-${slug.value}`,
  async () => {
    const response = await apiFetch<any>(`/videos/${slug.value}`)
    return response?.success && response?.data ? response.data : null
  },
  {
    watch: [slug]
  }
)

// Update video ref when data changes
watch(videoData, (newData) => {
  if (newData) {
    video.value = newData
  }
}, { immediate: true })

watch(fetchError, (err) => {
  if (err) {
    error.value = err.message || 'حدث خطأ أثناء تحميل الفيديو'
  } else {
    error.value = null
  }
}, { immediate: true })

// Fetch related videos
const fetchRelatedVideos = async () => {
  try {
    const response = await apiFetch<any>('/videos/featured', {
      params: { limit: 3 }
    })

    if (response?.success && response?.data) {
      relatedVideos.value = response.data.filter((v: any) => v.id !== video.value?.id).slice(0, 3)
    }
  } catch (err) {
    console.error('Error fetching related videos:', err)
  }
}

// Like video
const likeVideo = async () => {
  if (!video.value) return

  try {
    await apiFetch(`/videos/${video.value.id}/like`, { method: 'POST' })
    video.value.likes++
  } catch (err) {
    console.error('Error liking video:', err)
  }
}

// Share video
const shareVideo = () => {
  if (navigator.share) {
    navigator.share({
      title: getVideoTitle(video.value),
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href)
    alert('تم نسخ الرابط!')
  }
}

// SEO Meta Tags
watchEffect(() => {
  if (video.value) {
    const siteUrl = (config as any).public?.siteUrl || (process.client ? window.location.origin : '')
    const title = getVideoTitle(video.value)
    const description = getVideoDescription(video.value) || ''
    
    useHead({
      title: title,
      meta: [
        { name: 'description', content: video.value.meta_description || description },
        { name: 'keywords', content: video.value.meta_keywords || '' },
        { property: 'og:title', content: title },
        { property: 'og:description', content: description },
        { property: 'og:image', content: video.value.thumbnail },
        { property: 'og:url', content: `${siteUrl}/videos/${video.value.slug}` },
        { property: 'og:type', content: 'video.other' },
        { property: 'og:video', content: video.value.video_url },
        { name: 'twitter:card', content: 'player' },
        { name: 'twitter:title', content: title },
        { name: 'twitter:description', content: description },
        { name: 'twitter:image', content: video.value.thumbnail },
        { name: 'twitter:player', content: video.value.embed_url }
      ]
    })
  }
})

// زيادة المشاهدات وجلب الفيديوهات المرتبطة عند التحميل
onMounted(async () => {
  if (video.value) {
    // Increment views
    await apiFetch(`/videos/${video.value.id}/view`, { method: 'POST' }).catch(() => {})
    // Fetch related videos
    fetchRelatedVideos()
  }
})

// إعادة زيادة المشاهدات عند تغيير الفيديو
watch(video, async (newVideo) => {
  if (newVideo) {
    await apiFetch(`/videos/${newVideo.id}/view`, { method: 'POST' }).catch(() => {})
    fetchRelatedVideos()
  }
})

// Scroll to top عند تغيير الصفحة
watch(slug, () => {
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
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
