<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8 flex items-center gap-4">
        <div
          class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg flex items-center justify-center"
        >
          <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6a2 2 0 012-2h11a1 1 0 011 1v13a1 1 0 01-1 1H6a2 2 0 01-2-2V6zm3 1h8M7 10h8M7 13h4"
            />
          </svg>
        </div>
        <div>
          <h1 class="text-4xl font-bold text-gray-900 mb-1">أرشيف إصدارات الصحف</h1>
          <p class="text-gray-600">استعرض جميع الأعداد المنشورة حسب التاريخ أو المشاهدات أو التحميلات</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="mb-8 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div class="flex-1 flex items-center gap-3">
          <div class="relative flex-1">
            <input
              v-model="searchQuery"
              type="text"
              class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="ابحث باسم الصحيفة أو وصف الإصدار..."
              @keyup.enter="() => fetchIssues()"
            />
            <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </span>
          </div>
          <button
            type="button"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors"
            @click="() => fetchIssues()"
          >
            بحث
          </button>
        </div>

        <div class="flex items-center gap-3">
          <select
            v-model="sortBy"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            @change="() => fetchIssues()"
          >
            <option value="recent">الأحدث أولاً</option>
            <option value="oldest">الأقدم أولاً</option>
            <option value="views">الأكثر مشاهدة</option>
            <option value="downloads">الأكثر تحميلاً</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner v-if="loading" type="dots" size="lg" text="جاري تحميل الإصدارات..." />

      <!-- Issues Grid -->
      <div
        v-else-if="issues.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <article
          v-for="issue in issues"
          :key="issue.id"
          class="group flex flex-col bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
        >
          <!-- Cover -->
          <div class="relative h-52 bg-gray-100 overflow-hidden">
            <img
              v-if="issue.cover_image"
              :src="issue.cover_image"
              :alt="issue.newspaper_name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">
              📰
            </div>

            <!-- Issue number badge -->
            <div class="absolute bottom-3 right-3 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
              العدد {{ issue.issue_number }}
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 flex flex-col justify-between p-4">
            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-1 line-clamp-2">
                {{ issue.newspaper_name }}
              </h2>
              <p v-if="issue.description" class="text-sm text-gray-600 line-clamp-2 mb-2">
                {{ issue.description }}
              </p>
              <p class="text-xs text-gray-500 mb-3">
                تاريخ النشر: {{ formatDate(issue.publication_date) }}
              </p>
            </div>

            <!-- Stats + actions -->
            <div class="mt-2 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
              <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                  <i class="fas fa-eye text-gray-400"></i>
                  {{ formatNumber(issue.stats?.views ?? issue.views ?? 0) }}
                </span>
                <span class="flex items-center gap-1">
                  <i class="fas fa-download text-gray-400"></i>
                  {{ formatNumber(issue.stats?.downloads ?? issue.downloads ?? 0) }}
                </span>
              </div>
              <div>
                <a
                  v-if="issue.pdf_url"
                  :href="issue.pdf_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold"
                >
                  <i class="fas fa-file-pdf text-sm"></i>
                  <span class="text-xs">فتح العدد</span>
                </a>
              </div>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg p-12 text-center border border-dashed border-gray-300">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6a2 2 0 012-2h11a1 1 0 011 1v13a1 1 0 01-1 1H6a2 2 0 01-2-2V6zm3 1h8M7 10h8M7 13h4"
          />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد إصدارات متاحة</h3>
        <p class="text-gray-600">لم يتم العثور على أي أعداد للصحف وفق المعايير الحالية</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-10 flex justify-center">
        <nav class="flex gap-2">
          <button
            v-for="page in paginationPages"
            :key="page"
            @click="goToPage(page)"
            :disabled="page === pagination.current_page"
            class="px-4 py-2 rounded-lg font-medium transition-colors text-sm"
            :class="page === pagination.current_page
              ? 'bg-blue-600 text-white cursor-default'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
          >
            {{ page }}
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import LoadingSpinner from '~/components/LoadingSpinner.vue'

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

const { apiFetch } = useApi()
const settingsStore = useSettingsStore()

const issues = ref<NewspaperIssue[]>([])
const loading = ref<boolean>(false)
const pagination = ref<any | null>(null)
const searchQuery = ref<string>('')
const sortBy = ref<string>('recent')

const formatDate = (date?: string) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatNumber = (num: number) => {
  return (num || 0).toLocaleString('ar-EG')
}

const fetchIssues = async (page = 1) => {
  loading.value = true

  try {
    const response = await apiFetch<any>('/newspaper-issues', {
      params: {
        page,
        per_page: 12,
        sort: sortBy.value,
        search: searchQuery.value || undefined
      }
    })

    if (response?.success && response?.data) {
      issues.value = response.data
      pagination.value = response.pagination
    }
  } catch (error) {
    console.error('Error fetching newspaper issues:', error)
    issues.value = []
    pagination.value = null
  } finally {
    loading.value = false
  }
}

const paginationPages = computed(() => {
  if (!pagination.value) return []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages: number[] = []

  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }

  return pages
})

const goToPage = (page: number) => {
  fetchIssues(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// SEO meta
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name') || 'غرفة الأخبار'

  useSeoMeta({
    title: `أرشيف إصدارات الصحف - ${siteName}`,
    description: `استعرض جميع أعداد الصحف المنشورة في ${siteName} حسب التاريخ والمشاهدات والتحميلات`,
    ogTitle: `أرشيف إصدارات الصحف - ${siteName}`,
    ogDescription:
      'تصفح الأعداد السابقة للصحف بصيغة PDF، مع إمكانية الفرز حسب الأحدث والأكثر مشاهدة والأكثر تحميلاً'
  })
})

onMounted(() => {
  fetchIssues()
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
