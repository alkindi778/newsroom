<template>
  <div class="relative w-full overflow-hidden">
    <!-- Background Image with Blur - Full Width -->
    <div class="absolute inset-0 left-0 right-0 z-0">
      <img 
        src="/background.jpg" 
        alt="background" 
        class="w-full min-w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 py-8 max-w-7xl mx-auto px-4">
      <!-- Header Title -->
      <div class="mb-6">
        <h2 class="text-4xl font-bold text-white">
          {{ sectionTitle }}
        </h2>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-8 text-center">
        <LoadingSpinner type="dots" size="sm" text="جاري التحميل..." color="primary" />
      </div>

      <!-- Videos Grid -->
      <div v-else-if="videos.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 px-4">
        <NuxtLink
          v-for="video in videos"
          :key="video.id"
          :to="`/videos/${video.slug}`"
          class="group cursor-pointer overflow-hidden shadow-lg hover:shadow-xl transition-shadow"
        >
          <!-- Video Background Image -->
          <div class="relative overflow-hidden bg-gray-900">
            <!-- Placeholder with blur effect -->
            <div 
              v-if="!imageLoaded[video.id]"
              class="absolute inset-0 bg-gray-800 animate-pulse"
            >
              <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                </svg>
              </div>
            </div>

            <!-- Actual Image with lazy loading -->
            <img
              :src="video.thumbnail_placeholder || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
              :data-src="video.thumbnail || video.image"
              :alt="video.title"
              loading="lazy"
              class="lazy-image w-full h-56 object-cover transition-all duration-500 group-hover:scale-105"
              :class="{ 'opacity-0': !imageLoaded[video.id], 'opacity-100': imageLoaded[video.id] }"
              @load="onImageLoad(video.id)"
            />
            
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>

            <!-- Duration Badge -->
            <div class="absolute bottom-3 left-3 bg-black/70 text-white px-2.5 py-1 text-base font-bold rounded shadow-lg inline-block">
              {{ video.duration || '00:00' }}
            </div>
          </div>

          <!-- Video Title - Outside with Turquoise Background -->
          <div class="bg-teal-400 group-hover:bg-teal-500 transition-colors px-4 py-4">
            <h3 class="text-base font-bold text-white leading-relaxed line-clamp-2 min-h-[3rem]">
              {{ getVideoTitle(video) }}
            </h3>
          </div>
        </NuxtLink>
      </div>

      <!-- Empty State -->
      <div v-else class="p-8 text-center text-gray-500">
        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <p class="text-white">لا توجد فيديوهات متاحة</p>
      </div>

      <!-- More Button -->
      <div v-if="videos.length > 0" class="mt-8 pb-4">
        <div class="flex items-center gap-4">
          <div class="flex-1 h-px bg-white/30"></div>
          <NuxtLink
            to="/videos"
            class="inline-flex items-center gap-2 px-6 py-2 border border-white text-white font-semibold whitespace-nowrap rounded-md"
          >
            <span>المزيد</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Video {
  id: number
  title: string
  title_en?: string
  description?: string
  description_en?: string
  slug: string
  thumbnail?: string
  thumbnail_placeholder?: string
  thumbnail_srcset?: Record<string, string>
  image?: string
  duration?: string
  views?: number
  published_at?: string
}

const videos = ref<Video[]>([])
const loading = ref(false)
const imageLoaded = ref<Record<number, boolean>>({})
const sectionTitle = ref<string>('')

const { apiFetch } = useApi()
const { getImageUrl } = useImageUrl()
const { locale } = useI18n()

// دالة للحصول على عنوان الفيديو المترجم
const getVideoTitle = (video: Video) => {
  return locale.value === 'en' && video.title_en ? video.title_en : video.title
}

// Track image load status
const onImageLoad = (videoId: number) => {
  imageLoaded.value[videoId] = true
}

// Lazy load images using Intersection Observer
const lazyLoadImages = () => {
  if (process.client) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target as HTMLImageElement
          const src = img.getAttribute('data-src')
          if (src) {
            img.src = src
            imageObserver.unobserve(img)
          }
        }
      })
    }, {
      rootMargin: '50px'
    })

    // Observe all lazy images
    nextTick(() => {
      document.querySelectorAll('.lazy-image').forEach(img => {
        imageObserver.observe(img)
      })
    })
  }
}

// Fetch trending videos from API
const fetchTrendingVideos = async () => {
  loading.value = true
  try {
    const response = await apiFetch<any>('/videos/featured', {
      params: {
        limit: 4
      }
    })
    
    if (response?.success && response?.data) {
      videos.value = response.data
      // Set section title from API
      if (response.section_title) {
        sectionTitle.value = response.section_title
      }
      // Initialize image loaded state
      videos.value.forEach(video => {
        imageLoaded.value[video.id] = false
      })
      // Start lazy loading after DOM update
      nextTick(() => {
        lazyLoadImages()
      })
    }
  } catch (err) {
    console.error('Error fetching trending videos:', err)
    videos.value = []
  } finally {
    loading.value = false
  }
}

// Load on mount
onMounted(() => {
  fetchTrendingVideos()
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

/* Lazy loading animations */
.lazy-image {
  transition: opacity 0.5s ease-in-out, transform 0.3s ease;
}

.lazy-image.opacity-0 {
  opacity: 0;
}

.lazy-image.opacity-100 {
  opacity: 1;
}

/* Pulse animation for placeholder */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
