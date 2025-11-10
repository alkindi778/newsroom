<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-900 mb-2"><i class="fas fa-video text-red-600"></i> {{ sectionTitle }}</h1>
      <p class="text-gray-600">شاهد أحدث الفيديوهات والتقارير الإخبارية</p>
    </div>

    <!-- Videos Top Advertisement -->
    <div class="my-8">
      <AdvertisementZone position="homepage_top" page="videos" :auto-rotate="false" :show-dots="false" />
    </div>

    <!-- Filters -->
    <div class="mb-8 flex flex-wrap gap-4">
      <select
        v-model="selectedType"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        @change="() => fetchVideos()"
      >
        <option value="">جميع الأنواع</option>
        <option value="youtube">YouTube</option>
        <option value="vimeo">Vimeo</option>
      </select>

      <select
        v-model="sortBy"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        @change="() => fetchVideos()"
      >
        <option value="recent">الأحدث</option>
        <option value="popular">الأكثر مشاهدة</option>
      </select>
    </div>

    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="dots" size="lg" text="جاري التحميل..." />

    <!-- Videos Grid -->
    <div v-else-if="videos.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <NuxtLink
        v-for="video in videos"
        :key="video.id"
        :to="`/videos/${video.slug}`"
        class="group cursor-pointer"
      >
        <!-- Thumbnail -->
        <div class="relative overflow-hidden bg-gray-900 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
          <!-- Placeholder -->
          <div 
            v-if="!imageLoaded[video.id]"
            class="absolute inset-0 bg-gray-800 animate-pulse"
          >
            <div class="w-full h-full flex items-center justify-center">
              <i class="fas fa-play-circle text-6xl text-gray-600"></i>
            </div>
          </div>

          <img
            :src="video.thumbnail_placeholder || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
            :data-src="video.thumbnail"
            :alt="video.title"
            loading="lazy"
            class="lazy-image w-full h-56 object-cover transition-all duration-500 group-hover:scale-105"
            :class="{ 'opacity-0': !imageLoaded[video.id], 'opacity-100': imageLoaded[video.id] }"
            @load="onImageLoad(video.id)"
          />
          
          <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>

          <!-- Duration Badge -->
          <div v-if="video.duration" class="absolute bottom-3 left-3 bg-black/70 text-white px-2.5 py-1 text-sm font-bold rounded shadow-lg">
            {{ video.duration }}
          </div>

          <!-- Views Badge -->
          <div class="absolute top-3 left-3 bg-black/70 text-white px-2.5 py-1 text-xs font-medium rounded shadow-lg flex items-center gap-1">
            <i class="fas fa-eye"></i>
            <span>{{ formatNumber(video.views) }}</span>
          </div>
        </div>

        <!-- Video Info -->
        <div class="mt-3">
          <h3 class="text-base font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors leading-tight">
            {{ video.title }}
          </h3>
          <p class="text-sm text-gray-500 mt-1">{{ formatDate(video.published_at, 'relative') }}</p>
        </div>
      </NuxtLink>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16">
      <i class="fas fa-video-slash text-6xl text-gray-400 mb-4"></i>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد فيديوهات</h3>
      <p class="text-gray-600">لم يتم العثور على فيديوهات متاحة حالياً</p>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.last_page > 1" class="mt-12 flex justify-center">
      <nav class="flex gap-2">
        <button
          v-for="page in paginationPages"
          :key="page"
          @click="goToPage(page)"
          :disabled="page === pagination.current_page"
          class="px-4 py-2 rounded-lg font-medium transition-colors"
          :class="page === pagination.current_page 
            ? 'bg-blue-600 text-white cursor-default' 
            : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
        >
          {{ page }}
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const videos = ref<any[]>([])
const loading = ref(false)
const pagination = ref<any>(null)
const selectedType = ref('')
const sortBy = ref('recent')
const imageLoaded = ref<Record<number, boolean>>({})
const sectionTitle = ref<string>('فيديو العربية')

const { apiFetch } = useApi()
const { formatDate } = useDateFormat()

// Format numbers
const formatNumber = (num: number) => {
  return new Intl.NumberFormat('ar-EG').format(num)
}

// Track image load
const onImageLoad = (videoId: number) => {
  imageLoaded.value[videoId] = true
}

// Lazy load images
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
    }, { rootMargin: '50px' })

    nextTick(() => {
      document.querySelectorAll('.lazy-image').forEach(img => {
        imageObserver.observe(img)
      })
    })
  }
}

// Fetch videos
const fetchVideos = async (page = 1) => {
  loading.value = true

  try {
    const response = await apiFetch<any>('/videos', {
      params: {
        page,
        video_type: selectedType.value,
        sort: sortBy.value,
        per_page: 12
      }
    })

    if (response?.success && response?.data) {
      videos.value = response.data
      pagination.value = response.pagination
      
      // Set section title from API
      if (response.section_title) {
        sectionTitle.value = response.section_title
      }

      // Initialize image loaded state
      videos.value.forEach(video => {
        imageLoaded.value[video.id] = false
      })

      // Start lazy loading
      nextTick(() => {
        lazyLoadImages()
      })
    }
  } catch (err) {
    console.error('Error fetching videos:', err)
  } finally {
    loading.value = false
  }
}

// Pagination pages
const paginationPages = computed(() => {
  if (!pagination.value) return []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

// Go to page
const goToPage = (page: number) => {
  fetchVideos(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// SEO - will be updated after fetching videos
const pageTitle = computed(() => `${sectionTitle.value} - غرفة الأخبار`)

useHead({
  title: pageTitle,
  meta: [
    { name: 'description', content: 'شاهد أحدث الفيديوهات والتقارير الإخبارية من غرفة الأخبار' },
    { name: 'keywords', content: 'فيديوهات, أخبار, تقارير, العربية' }
  ]
})

// Load videos on mount
onMounted(() => {
  fetchVideos()
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

.lazy-image {
  transition: opacity 0.5s ease-in-out, transform 0.3s ease;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
