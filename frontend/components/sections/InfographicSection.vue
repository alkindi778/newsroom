<template>
  <section class="infographic-section my-8">
    <div class="container mx-auto px-4">
      <!-- Section Header -->
      <div class="section-header mb-6" v-if="title">
        <div class="flex items-center justify-between border-b-2 border-primary pb-3">
          <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
              {{ title }}
            </h2>
            <p v-if="subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
              {{ subtitle }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Infographic Grid -->
      <div v-if="infographics && infographics.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="(item, index) in displayedInfographics" 
          :key="item.id || index"
          class="infographic-card group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
        >
          <!-- Image Container -->
          <div class="relative h-96 overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900">
            <img 
              v-if="item.image" 
              :src="getImageUrl(item.image)" 
              :alt="item.title || $t('infographic.label')"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              loading="lazy"
            />
            
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <!-- Title Overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/90 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-300">
              <h3 class="text-white text-lg font-bold mb-2 line-clamp-2">
                {{ item.title || $t('infographic.label') }}
              </h3>
              <p v-if="item.description" class="text-gray-200 text-sm line-clamp-2">
                {{ item.description }}
              </p>
              
              <!-- View Button -->
              <button 
                @click="openInfographic(item)"
                class="mt-3 inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 text-sm font-medium"
              >
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ $t('infographic.view') }}
              </button>
            </div>
            
            <!-- Badge -->
            <div class="absolute top-4 right-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary text-white shadow-lg">
                <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
                {{ $t('infographic.label') }}
              </span>
            </div>
            
            <!-- Date if available -->
            <div v-if="item.date || item.created_at" class="absolute top-4 left-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-700 shadow-md">
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(item.date || item.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <svg class="w-24 h-24 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">
          {{ $t('infographic.noData') }}
        </h3>
        <p class="text-gray-500 dark:text-gray-500">
          {{ $t('infographic.willDisplay') }}
        </p>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <Teleport to="body">
      <transition name="fade">
        <div 
          v-if="selectedInfographic" 
          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90"
          @click="closeInfographic"
        >
          <div class="relative max-w-6xl w-full max-h-screen overflow-auto" @click.stop>
            <!-- Close Button -->
            <button 
              @click="closeInfographic"
              class="absolute top-4 left-4 z-10 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Image -->
            <img 
              :src="getImageUrl(selectedInfographic.image)" 
              :alt="selectedInfographic.title"
              class="w-full h-auto rounded-lg shadow-2xl"
            />
            
            <!-- Info -->
            <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg">
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                {{ selectedInfographic.title }}
              </h3>
              <p v-if="selectedInfographic.description" class="text-gray-600 dark:text-gray-400">
                {{ selectedInfographic.description }}
              </p>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface InfographicItem {
  id?: number
  title?: string
  description?: string
  image: string
  date?: string
  created_at?: string
}

interface Props {
  title?: string
  subtitle?: string
  limit?: number
  settings?: any
}

const props = withDefaults(defineProps<Props>(), {
  limit: 6
})

// State
const selectedInfographic = ref<InfographicItem | null>(null)

// Fetch infographics data
const { data: infographicsData } = await useAsyncData(
  'infographics',
  async () => {
    try {
      const api = useApi()
      const result = await api.apiFetch<any>('/infographics?limit=' + props.limit)
      return result?.data || []
    } catch (error) {
      console.error('Error fetching infographics:', error)
      return []
    }
  }
)

const infographics = computed(() => infographicsData.value || [])
const displayedInfographics = computed(() => infographics.value.slice(0, props.limit))

// Methods
const getImageUrl = (image: string) => {
  if (!image) return '/placeholder-infographic.jpg'
  if (image.startsWith('http')) return image
  const config = useRuntimeConfig()
  const apiBase = ((config as any).public?.apiBase || 'http://localhost:8000/api') as string
  return apiBase.replace('/api', '') + '/storage/' + image
}

const formatDate = (date: string) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' })
}

const openInfographic = (item: InfographicItem) => {
  selectedInfographic.value = item
  document.body.style.overflow = 'hidden'
}

const closeInfographic = () => {
  selectedInfographic.value = null
  document.body.style.overflow = ''
}

// Cleanup on unmount
onUnmounted(() => {
  document.body.style.overflow = ''
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

.infographic-section {
  --primary: #1e40af;
  --primary-dark: #1e3a8a;
}

.dark .infographic-section {
  --primary: #3b82f6;
  --primary-dark: #2563eb;
}

.bg-primary {
  background-color: var(--primary);
}

.hover\:bg-primary-dark:hover {
  background-color: var(--primary-dark);
}

.text-primary {
  color: var(--primary);
}

.border-primary {
  border-color: var(--primary);
}
</style>
