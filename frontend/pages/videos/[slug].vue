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
        <div 
          v-if="isReelsOrShorts"
          class="relative bg-black flex items-center justify-center mx-auto w-full"
          style="max-width: min(100%, 420px); aspect-ratio: 9/16; height: auto; max-height: min(calc(100vh - 200px), 750px);"
        >
          <iframe
            :src="video.embed_url"
            :title="getVideoTitle(video)"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            class="absolute inset-0 w-full h-full rounded-lg"
          ></iframe>
        </div>
        <div 
          v-else
          class="relative w-full bg-black"
          style="aspect-ratio: 16/9;"
        >
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
          <div class="flex flex-wrap gap-3 mt-6 mb-6">
            <button
              @click="likeVideo"
              class="flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
              <span>{{ formatNumber(video.likes) }}</span>
            </button>
          </div>

          <!-- أزرار المشاركة -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ locale === 'en' ? 'Share Video:' : 'شارك الفيديو:' }}</h3>
            <div class="flex flex-wrap items-center gap-3">
              <!-- فيسبوك -->
              <button 
                @click="shareOnFacebook(videoUrl, getVideoTitle(video))"
                class="flex items-center gap-2 px-4 py-2 bg-[#1877F2] hover:bg-[#145dbf] text-white rounded-lg transition-colors duration-200"
                title="شارك على فيسبوك"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span class="text-sm font-semibold">فيسبوك</span>
              </button>

              <!-- تويتر/X -->
              <button 
                @click="shareOnTwitter(videoUrl, getVideoTitle(video))"
                class="flex items-center gap-2 px-4 py-2 bg-[#000000] hover:bg-[#333333] text-white rounded-lg transition-colors duration-200"
                title="شارك على X"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span class="text-sm font-semibold">X</span>
              </button>

              <!-- واتساب -->
              <button 
                @click="shareOnWhatsApp(videoUrl, getVideoTitle(video))"
                class="flex items-center gap-2 px-4 py-2 bg-[#25D366] hover:bg-[#1da851] text-white rounded-lg transition-colors duration-200"
                title="شارك على واتساب"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                <span class="text-sm font-semibold">واتساب</span>
              </button>

              <!-- تيليجرام -->
              <button 
                @click="shareOnTelegram(videoUrl, getVideoTitle(video))"
                class="flex items-center gap-2 px-4 py-2 bg-[#0088cc] hover:bg-[#0077b5] text-white rounded-lg transition-colors duration-200"
                title="شارك على تيليجرام"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                <span class="text-sm font-semibold">تيليجرام</span>
              </button>

              <!-- نسخ الرابط -->
              <button 
                @click="copyVideoUrl"
                class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200"
                title="نسخ الرابط"
              >
                <svg v-if="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold">{{ locale === 'en' ? (copied ? 'Copied!' : 'Copy Link') : (copied ? 'تم النسخ!' : 'نسخ الرابط') }}</span>
              </button>
            </div>
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
const { shareOnFacebook, shareOnTwitter, shareOnWhatsApp, shareOnTelegram, buildShareText } = useShare()

// حالة نسخ الرابط
const copied = ref(false)

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

// Check if video is Reels or Shorts (vertical format)
const isReelsOrShorts = computed(() => {
  if (!video.value) return false
  
  const videoUrl = video.value.video_url?.toLowerCase() || ''
  const embedUrl = video.value.embed_url?.toLowerCase() || ''
  
  // Check for Facebook Reels
  if (video.value.video_type === 'facebook' && (videoUrl.includes('/reel/') || videoUrl.includes('/share/r/'))) {
    return true
  }
  
  // Check for YouTube Shorts
  if (video.value.video_type === 'youtube' && (videoUrl.includes('/shorts/') || embedUrl.includes('/shorts/'))) {
    return true
  }
  
  return false
})

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

// رابط الفيديو
const videoUrl = computed(() => {
  if (process.client) {
    return window.location.href
  }
  const siteUrl = (config as any).public?.siteUrl || ''
  return siteUrl ? `${siteUrl}/videos/${slug.value}` : `/videos/${slug.value}`
})

// دالة نسخ رابط الفيديو مع التوقيع
const copyVideoUrl = async () => {
  if (process.client && navigator.clipboard && video.value) {
    try {
      const textToCopy = buildShareText(getVideoTitle(video.value), videoUrl.value)
      await navigator.clipboard.writeText(textToCopy)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (err) {
      console.error('Failed to copy:', err)
    }
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
