<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('infographic.title') }}</h1>
        <p class="text-gray-600">{{ t('infographic.description') }}</p>
      </div>

      <!-- Loading -->
      <LoadingSpinner v-if="loading" />

      <!-- Infographics Grid -->
      <div v-else-if="infographics.length > 0" class="space-y-8">
        <!-- Mobile Slider -->
        <div class="md:hidden overflow-x-auto scrollbar-hide">
          <div class="flex gap-4 pb-4">
            <div 
              v-for="(item, index) in infographics" 
              :key="item.id || index"
              @click="openInfographic(item)"
              class="group cursor-pointer bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 flex-shrink-0 w-64"
            >
              <!-- Image -->
              <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                <!-- Loading Placeholder -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 animate-pulse"></div>
                
                <img 
                  v-if="item.image" 
                  :src="getImageUrl(item.image)" 
                  :alt="item.title || t('infographic.label')"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 relative z-10"
                  loading="lazy"
                  decoding="async"
                />
                
                <!-- Overlay on hover -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 z-20"></div>
              </div>
              
              <!-- Content -->
              <div class="p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                  {{ item.title || t('infographic.label') }}
                </h3>
                <p v-if="item.description" class="text-sm text-gray-600 line-clamp-2 mb-3">
                  {{ item.description }}
                </p>
                
                <!-- Footer -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                  <span v-if="item.date || item.created_at" class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(item.date || item.created_at) }}
                  </span>
                  
                  <span class="flex items-center gap-1 text-blue-600 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ t('infographic.view') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Desktop Grid -->
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="(item, index) in infographics" 
            :key="item.id || index"
            @click="openInfographic(item)"
            class="group cursor-pointer bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300"
          >
            <!-- Image -->
            <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
              <!-- Loading Placeholder -->
              <div class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 animate-pulse"></div>
              
              <img 
                v-if="item.image" 
                :src="getImageUrl(item.image)" 
                :alt="item.title || t('infographic.label')"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 relative z-10"
                loading="lazy"
                decoding="async"
              />
              
              <!-- Overlay on hover -->
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 z-20"></div>
            </div>
            
            <!-- Content -->
            <div class="p-4">
              <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                {{ item.title || t('infographic.label') }}
              </h3>
              <p v-if="item.description" class="text-sm text-gray-600 line-clamp-2 mb-3">
                {{ item.description }}
              </p>
              
              <!-- Footer -->
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span v-if="item.date || item.created_at" class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDate(item.date || item.created_at) }}
                </span>
                
                <span class="flex items-center gap-1 text-blue-600 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {{ t('infographic.view') }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <p class="text-gray-600 text-lg">{{ t('infographic.noData') }}</p>
      </div>

      <!-- Load More Button -->
      <div v-if="infographics.length > 0 && !loading && hasMore" class="mt-8">
        <div class="flex items-center gap-4">
          <div class="flex-1 h-px bg-gray-300"></div>
          <button
            @click="loadMore"
            :disabled="loadingMore"
            class="inline-flex items-center gap-2 px-6 py-2 border border-gray-900 text-gray-900 font-semibold whitespace-nowrap rounded-md hover:bg-gray-900 hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span>{{ loadingMore ? t('common.loading') : t('common.read_more') }}</span>
            <svg v-if="!loadingMore" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <Teleport to="body">
      <transition name="fade">
        <div 
          v-if="selectedInfographic" 
          class="fixed inset-0 z-50 overflow-y-auto bg-black/95 backdrop-blur-sm"
          @click="closeInfographic"
        >
          <!-- Close Button (Fixed) -->
          <button 
            @click="closeInfographic"
            class="fixed top-4 left-4 z-[60] p-2 text-white bg-white/10 hover:bg-white/20 rounded-full transition-colors backdrop-blur-md"
            :title="$t('common.close')"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="min-h-full flex items-center justify-center p-4 py-12">
            <div class="relative max-w-4xl w-full" @click.stop>
              <!-- Image -->
              <div class="bg-white rounded-t-lg overflow-hidden">
                <img 
                  :src="getImageUrl(selectedInfographic.image)" 
                  :alt="selectedInfographic.title"
                  class="w-full h-auto block"
                  loading="eager"
                />
              </div>
              
              <!-- Info -->
              <div class="p-6 bg-white rounded-b-lg border-t border-gray-100">
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">
                  {{ selectedInfographic.title }}
                </h3>
                <p v-if="selectedInfographic.description" class="text-gray-600 text-base md:text-lg leading-relaxed">
                  {{ selectedInfographic.description }}
                </p>
                
                <!-- Download Button (Optional) -->
                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                  <a 
                    :href="getImageUrl(selectedInfographic.image)" 
                    download
                    target="_blank"
                    class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm"
                    @click.stop
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ t('common.download') || 'تحميل الصورة' }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
const { t, locale } = useI18n()

interface InfographicItem {
  id?: number
  title?: string
  description?: string
  image: string
  date?: string
  created_at?: string
}

// State
const infographics = ref<InfographicItem[]>([])
const loading = ref(true)
const loadingMore = ref(false)
const selectedInfographic = ref<InfographicItem | null>(null)
const currentPage = ref(1)
const hasMore = ref(true)
const perPage = 12

// Fetch infographics
const fetchInfographics = async (page: number = 1) => {
  try {
    const api = useApi()
    const result = await api.apiFetch<any>(`/infographics?page=${page}&per_page=${perPage}`)
    
    if (page === 1) {
      infographics.value = result?.data || []
    } else {
      infographics.value = [...infographics.value, ...(result?.data || [])]
    }
    
    // Check if there are more pages
    hasMore.value = result?.data?.length === perPage
    currentPage.value = page
  } catch (error) {
    console.error('Error fetching infographics:', error)
  }
}

// Load more
const loadMore = async () => {
  loadingMore.value = true
  await fetchInfographics(currentPage.value + 1)
  loadingMore.value = false
}

// Methods
const getImageUrl = (image: string) => {
  if (!image) return '/placeholder-infographic.jpg'
  if (image.startsWith('http')) return image
  const config = useRuntimeConfig()
  const apiBase = ((config as any).public?.apiBase || '') as string
  if (!apiBase) {
    console.error('API Base URL is not configured')
    return '/placeholder-infographic.jpg'
  }
  const baseUrl = apiBase.replace(/\/api(\/v1)?$/, '')
  return baseUrl + '/storage/' + image
}

const formatDate = (date: string | undefined) => {
  if (!date) return ''
  const d = new Date(date)
  const dateLocale = locale.value === 'en' ? 'en-US' : 'ar-SA'
  return d.toLocaleDateString(dateLocale, { year: 'numeric', month: 'long', day: 'numeric' })
}

const openInfographic = (item: InfographicItem) => {
  selectedInfographic.value = item
  document.body.style.overflow = 'hidden'
}

const closeInfographic = () => {
  selectedInfographic.value = null
  document.body.style.overflow = ''
}

// Fetch on mount
onMounted(async () => {
  loading.value = true
  await fetchInfographics(1)
  loading.value = false
})

// Cleanup
onUnmounted(() => {
  document.body.style.overflow = ''
})

// SEO
const settingsStore = useSettingsStore()
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  const isEnglish = locale.value === 'en'
  
  if (siteName) {
    useSeoMeta({
      title: isEnglish ? 'Infographics' : 'إنفوجرافيك',
      description: isEnglish 
        ? `Browse the ${siteName} infographics collection`
        : `تصفح مجموعة إنفوجرافيك ${siteName}`,
      ogTitle: isEnglish 
        ? `Infographics - ${siteName}`
        : `إنفوجرافيك - ${siteName}`,
      ogDescription: isEnglish 
        ? 'Visual and interactive content'
        : 'محتوى مرئي وتفاعلي'
    })
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Hide scrollbar for mobile slider */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
