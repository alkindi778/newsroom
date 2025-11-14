<template>
  <section class="mb-12">
    <div class="relative w-full overflow-hidden">
      <!-- Background Image with Blur - Full Width (same as videos) -->
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
        <!-- Header -->
        <div class="flex flex-col gap-4 mb-6" v-if="title || subtitle">
          <div>
            <h2 class="text-3xl font-bold text-white mb-1">
              {{ title || 'أرشيف الإصدارات' }}
            </h2>
            <p v-if="subtitle" class="text-sm text-slate-200/80">
              {{ subtitle }}
            </p>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="i in limit" :key="i" class="animate-pulse">
            <div class="bg-slate-700/60 h-52 rounded-xl mb-3"></div>
            <div class="h-4 bg-slate-700/60 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-slate-700/60 rounded w-1/2"></div>
          </div>
        </div>

        <!-- Issues Grid -->
        <div v-else-if="issues.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <article
              v-for="issue in issues"
              :key="issue.id"
              class="group cursor-pointer overflow-hidden shadow-lg hover:shadow-xl transition-shadow rounded-lg"
            >
              <!-- Cover block (like video card) -->
              <div class="relative overflow-hidden bg-gray-900">
                <div 
                  v-if="!issue.cover_image"
                  class="w-full h-64 flex items-center justify-center text-slate-500 text-4xl"
                >
                  <i class="fas fa-newspaper"></i>
                </div>
                <!-- Actual Image with lazy loading -->
                <img
                  v-else
                  :src="'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                  :data-src="issue.cover_image"
                  :alt="issue.newspaper_name"
                  loading="lazy"
                  class="lazy-image issue-cover w-full object-contain bg-slate-100"
                  :class="{ 'opacity-0': !imageLoadedIssues[issue.id], 'opacity-100': imageLoadedIssues[issue.id] }"
                  @load="onIssueImageLoad(issue.id)"
                />
              </div>

              <!-- Bottom colored title block (like video card) -->
              <div class="bg-teal-400 group-hover:bg-teal-500 transition-colors px-4 py-4">
                <h3 class="text-base font-bold text-white leading-relaxed line-clamp-2 min-h-[3rem] mb-2">
                  {{ issue.newspaper_name }} - العدد {{ issue.issue_number }}
                </h3>

                <!-- Meta: PDF link only -->
                <div class="flex items-center justify-end text-xs text-teal-100/90">
                  <a
                    v-if="issue.pdf_url"
                    :href="issue.pdf_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1 text-white font-semibold hover:text-slate-100"
                  >
                    <i class="fas fa-file-pdf text-xs"></i>
                    <span class="text-xs">فتح العدد</span>
                  </a>
                </div>
              </div>
            </article>
          </div>

          <!-- More Button with line behind (like videos) -->
          <div class="mt-8 pb-4" v-if="issues.length > 0">
            <div class="flex items-center gap-4">
              <div class="flex-1 h-px bg-white/30"></div>
              <NuxtLink
                to="/newspaper-issues"
                class="inline-flex items-center gap-2 px-6 py-2 border border-white text-white font-semibold whitespace-nowrap rounded-md hover:bg-white/10 transition-colors"
              >
                <span>المزيد</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </NuxtLink>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-12 text-slate-200">
          لا توجد إصدارات متاحة حالياً
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
interface NewspaperIssue {
  id: number
  newspaper_name: string
  issue_number: number
  slug: string
  description?: string
  pdf_url?: string
  cover_image?: string
  publication_date?: string
  views?: number
  downloads?: number
  stats?: {
    views?: number
    downloads?: number
  }
}

interface Props {
  title?: string | null
  subtitle?: string | null
  limit?: number
  settings?: Record<string, any> | null
}

const props = withDefaults(defineProps<Props>(), {
  title: null,
  subtitle: null,
  limit: 8,
  settings: null
})

const { apiFetch } = useApi()

const issues = ref<NewspaperIssue[]>([])
const loading = ref(true)

const imageLoadedIssues = ref<Record<number, boolean>>({})

const formatDate = (date?: string) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatNumber = (num: number) => {
  return num.toLocaleString('ar-EG')
}

const onIssueImageLoad = (issueId: number) => {
  imageLoadedIssues.value[issueId] = true
}

const lazyLoadIssueImages = () => {
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

    nextTick(() => {
      document.querySelectorAll<HTMLImageElement>('.issue-cover.lazy-image').forEach(img => {
        imageObserver.observe(img)
      })
    })
  }
}

const fetchIssues = async () => {
  loading.value = true
  try {
    const response = await apiFetch<any>('/newspaper-issues', {
      params: {
        per_page: props.limit
      }
    })

    if (response?.data) {
      issues.value = response.data
      imageLoadedIssues.value = {}
      issues.value.forEach(issue => {
        imageLoadedIssues.value[issue.id] = false
      })

      nextTick(() => {
        lazyLoadIssueImages()
      })
    }
  } catch (error) {
    console.error('Error fetching newspaper issues:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchIssues()
})
</script>
