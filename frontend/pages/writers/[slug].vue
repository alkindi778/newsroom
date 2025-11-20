<template>
  <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 md:py-8">
    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="pulse" size="lg" fullScreen text="جاري تحميل بيانات الكاتب..." color="secondary" />

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h2>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <NuxtLink to="/opinions" class="text-orange-600 hover:text-orange-700 font-semibold">
        العودة لمقالات الرأي
      </NuxtLink>
    </div>

    <!-- Writer Content -->
    <div v-else-if="writer" class="max-w-6xl mx-auto">
      <!-- Writer Profile -->
      <div class="bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-lg overflow-hidden mb-8 sm:mb-12">
        <div class="md:flex">
          <!-- الصورة -->
          <div class="md:w-1/3 p-4 sm:p-6 md:p-8 flex items-center justify-center">
            <img 
              :src="getImageUrl(writer.image)" 
              :alt="writer.name"
              class="w-40 h-40 sm:w-52 sm:h-52 md:w-64 md:h-64 rounded-full border-4 border-orange-400 shadow-xl object-cover"
            />
          </div>

          <!-- المعلومات -->
          <div class="md:w-2/3 p-4 sm:p-6 md:p-8 text-center md:text-right">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2 sm:mb-3">{{ getWriterName }}</h1>
            
            <p v-if="getWriterPosition" class="text-sm sm:text-base md:text-lg text-gray-600 font-medium mb-2">
              {{ getWriterPosition }}
            </p>
            
            <p v-if="getWriterSpecialty" class="text-base sm:text-lg md:text-xl text-orange-600 font-semibold mb-3 sm:mb-4">
              {{ getWriterSpecialty }}
            </p>

            <p v-if="getWriterBio" class="text-gray-700 text-sm sm:text-base md:text-lg leading-relaxed mb-4 sm:mb-6">
              {{ getWriterBio }}
            </p>

            <!-- الإحصائيات -->
            <div class="flex items-center justify-center md:justify-start gap-4 sm:gap-6 mb-4 sm:mb-6">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-gray-700 text-sm sm:text-base">
                  <span class="font-bold text-gray-900">{{ writer.opinions_count }}</span> مقال
                </span>
              </div>
            </div>

            <!-- الروابط الاجتماعية -->
            <div v-if="hasSocialLinks" class="flex items-center justify-center md:justify-start gap-2 sm:gap-3">
              <a 
                v-if="writer.social_links?.facebook" 
                :href="writer.social_links.facebook" 
                target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>
              <a 
                v-if="writer.social_links?.twitter" 
                :href="writer.social_links.twitter" 
                target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-black text-white hover:bg-gray-800 transition-colors"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
              </a>
              <a 
                v-if="writer.social_links?.linkedin" 
                :href="writer.social_links.linkedin" 
                target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-700 text-white hover:bg-blue-800 transition-colors"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
              </a>
              <a 
                v-if="writer.social_links?.website" 
                :href="writer.social_links.website" 
                target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-700 text-white hover:bg-gray-800 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- مقالات الكاتب -->
      <div>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center justify-center md:justify-start gap-2 sm:gap-3">
          <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>مقالات {{ getWriterName }}</span>
        </h2>

        <!-- Loading Opinions -->
        <LoadingSpinner v-if="loadingOpinions" type="dots" text="جاري تحميل المقالات..." color="secondary" />

        <!-- Opinions Grid -->
        <div v-else-if="opinions.length > 0">
          <div class="mb-4 text-xs sm:text-sm text-gray-600 text-center md:text-right">
            عرض <span class="font-bold text-gray-900">{{ opinions.length }}</span> من أصل 
            <span class="font-bold text-gray-900">{{ totalOpinions }}</span> مقال
          </div>
          
          <!-- مقالات مع صور -->
          <div v-if="opinionsWithImages.length > 0">
            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              مقالات مصورة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
              <OpinionCard 
                v-for="opinion in opinionsWithImages" 
                :key="opinion.id" 
                :opinion="opinion" 
              />
            </div>
          </div>

          <!-- مقالات بدون صور -->
          <div v-if="opinionsWithoutImages.length > 0">
            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              مقالات أخرى
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
              <OpinionCard 
                v-for="opinion in opinionsWithoutImages" 
                :key="opinion.id" 
                :opinion="opinion" 
              />
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-center gap-1.5 sm:gap-2 flex-wrap">
            <button
              v-for="page in totalPages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 sm:px-4 py-1.5 sm:py-2 rounded text-sm sm:text-base font-semibold transition-colors',
                currentPage === page
                  ? 'bg-orange-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
            >
              {{ page }}
            </button>
          </div>
        </div>

        <!-- No Opinions -->
        <div v-else class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg">
          <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <p class="text-sm sm:text-base text-gray-600">لا توجد مقالات بعد</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Writer, Opinion } from '~/types'

const route = useRoute()
const { apiFetch } = useApi()
const { getImageUrl } = useImageUrl()
const { setWriterSeoMeta } = useAppSeoMeta()
const { locale } = useI18n()

// دوال الترجمة
const getWriterName = computed(() => {
  if (!writer.value) return ''
  return locale.value === 'en' && writer.value.name_en ? writer.value.name_en : writer.value.name
})

const getWriterPosition = computed(() => {
  if (!writer.value?.position) return null
  return locale.value === 'en' && writer.value.position_en ? writer.value.position_en : writer.value.position
})

const getWriterSpecialty = computed(() => {
  if (!writer.value) return null
  const specialty = writer.value.specialization || writer.value.specialty
  const specialty_en = writer.value.specialization_en
  if (!specialty && !specialty_en) return null
  return locale.value === 'en' && specialty_en ? specialty_en : specialty
})

const getWriterBio = computed(() => {
  if (!writer.value?.bio) return null
  return locale.value === 'en' && writer.value.bio_en ? writer.value.bio_en : writer.value.bio
})

const writer = ref<Writer | null>(null)
const opinions = ref<Opinion[]>([])
const loading = ref(false)
const loadingOpinions = ref(false)
const error = ref<string | null>(null)
const currentPage = ref(1)
const totalPages = ref(1)
const totalOpinions = ref(0)
const perPage = 12

const hasSocialLinks = computed(() => {
  return writer.value?.social_links?.facebook || 
         writer.value?.social_links?.twitter || 
         writer.value?.social_links?.linkedin ||
         writer.value?.social_links?.website
})

// فصل المقالات حسب وجود الصورة
const opinionsWithImages = computed(() => opinions.value.filter(opinion => opinion.image))
const opinionsWithoutImages = computed(() => opinions.value.filter(opinion => !opinion.image))

// جلب بيانات الكاتب مع SSR
const slug = computed(() => route.params.slug as string)

const { data: writerData, error: fetchError } = await useAsyncData(
  `writer-${slug.value}`,
  async () => {
    const response = await apiFetch<any>(`/writers/${slug.value}`)
    return response?.data || response || null
  },
  {
    watch: [slug]
  }
)

// Update writer ref when data changes
watch(writerData, (newData) => {
  if (newData) {
    writer.value = newData
  }
}, { immediate: true })

watch(fetchError, (err) => {
  if (err) {
    error.value = err.message || 'حدث خطأ في تحميل بيانات الكاتب'
  }
}, { immediate: true })

// جلب مقالات الكاتب
const fetchWriterOpinions = async (page: number = 1) => {
  if (!writer.value) return
  
  loadingOpinions.value = true
  
  try {
    const response = await apiFetch<any>(
      `/writers/${writer.value.id}/opinions?page=${page}&per_page=${perPage}&status=published&sort_by=published_at&sort_dir=desc`
    )
    
    if (response && response.data) {
      opinions.value = response.data
      
      // Update pagination data
      if (response.pagination) {
        currentPage.value = response.pagination.current_page
        totalPages.value = response.pagination.last_page
        totalOpinions.value = response.pagination.total
      }
    } else if (Array.isArray(response)) {
      opinions.value = response
    }
  } catch (err) {
    console.error('Error fetching writer opinions:', err)
  } finally {
    loadingOpinions.value = false
  }
}

// الانتقال لصفحة معينة
const goToPage = (page: number) => {
  currentPage.value = page
  fetchWriterOpinions(page)
  window.scrollTo({ top: 500, behavior: 'smooth' })
}

// SEO Meta Tags - يتم تحديثها عند تغيير الكاتب
watchEffect(() => {
  if (writer.value) {
    setWriterSeoMeta(writer.value)
  }
})

// تحميل مقالات الكاتب عند التحميل
onMounted(async () => {
  if (writer.value) {
    await fetchWriterOpinions()
  }
})

// إعادة تحميل المقالات عند تغيير الكاتب
watch(writer, async (newWriter) => {
  if (newWriter) {
    await fetchWriterOpinions()
  }
})

// Scroll to top عند تغيير الصفحة
watch(slug, () => {
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>
