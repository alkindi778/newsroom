<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-900 mb-2">مقالات الرأي</h1>
      <p class="text-gray-600">اقرأ آراء وتحليلات كُتابنا المميزين</p>
    </div>

    <!-- Opinions Top Advertisement -->
    <div class="my-8">
      <AdvertisementZone position="homepage_top" page="opinions" :auto-rotate="false" :show-dots="false" />
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-8">
      <div class="flex flex-wrap items-center gap-4">
        <!-- البحث -->
        <div class="flex-1 min-w-[200px]">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ابحث في مقالات الرأي..."
            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none"
            @input="debouncedSearch"
          />
        </div>

        <!-- الترتيب -->
        <select
          v-model="sortBy"
          class="px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none"
          @change="() => fetchOpinions()"
        >
          <option value="latest">الأحدث</option>
          <option value="popular">الأكثر مشاهدة</option>
          <option value="likes">الأكثر إعجاباً</option>
        </select>

        <!-- زر المسح -->
        <button
          v-if="searchQuery"
          @click="clearFilters"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
        >
          مسح الفلاتر
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <LoadingSpinner v-if="loading && opinions.length === 0" type="dots" size="lg" text="جاري تحميل مقالات الرأي..." color="secondary" />

    <!-- Grid المقالات -->
    <div v-else-if="opinions.length > 0">
      <!-- مقالات مع صور -->
      <div v-if="opinionsWithImages.length > 0">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          مقالات مصورة
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          <TransitionGroup name="fade-slide">
            <OpinionCard 
              v-for="opinion in opinionsWithImages" 
              :key="opinion.id" 
              :opinion="opinion" 
            />
          </TransitionGroup>
        </div>
      </div>

      <!-- مقالات بدون صور -->
      <div v-if="opinionsWithoutImages.length > 0">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          مقالات أخرى
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TransitionGroup name="fade-slide">
            <OpinionCard 
              v-for="opinion in opinionsWithoutImages" 
              :key="opinion.id" 
              :opinion="opinion" 
            />
          </TransitionGroup>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="hasMore" class="text-center mt-8">
        <button
          @click="loadMore"
          :disabled="loading"
          class="bg-orange-600 hover:bg-orange-700 disabled:bg-gray-400 text-white px-8 py-3 rounded-full font-semibold transition-colors"
        >
          <span v-if="!loading">تحميل المزيد</span>
          <span v-else>جاري التحميل...</span>
        </button>
      </div>
    </div>

    <!-- حالة فارغة -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-600 text-lg">لا توجد مقالات متاحة</p>
    </div>

    <!-- الكُتاب المميزون -->
    <section v-if="writers.length > 0" class="mt-16">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-900">كُتابنا المميزون</h2>
        <NuxtLink 
          to="/writers" 
          class="text-orange-600 hover:text-orange-700 font-semibold text-sm flex items-center gap-1"
        >
          <span>عرض الكل</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </NuxtLink>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <NuxtLink
          v-for="writer in displayedWriters"
          :key="writer.id"
          :to="`/writers/${writer.slug}`"
          class="group text-center"
        >
          <div class="relative w-24 h-24 mx-auto mb-2">
            <img
              :src="getImageUrl(writer.image)"
              :alt="writer.name"
              loading="lazy"
              class="w-full h-full object-cover rounded-full border-4 border-orange-400 group-hover:border-orange-600 transition-colors"
            />
          </div>
          <h3 class="font-semibold text-gray-900 group-hover:text-orange-600 transition-colors text-sm">
            {{ writer.name }}
          </h3>
          <p class="text-xs text-gray-500">{{ writer.opinions_count || 0 }} مقال</p>
        </NuxtLink>
      </div>
      
      <!-- عرض المزيد من الكُتاب -->
      <div v-if="writers.length > 12" class="text-center mt-6">
        <button
          @click="toggleWritersView"
          class="text-orange-600 hover:text-orange-700 font-semibold text-sm flex items-center gap-2 mx-auto"
        >
          <span>{{ showAllWriters ? 'عرض أقل' : `عرض ${writers.length - 12} كاتب آخر` }}</span>
          <svg 
            class="w-4 h-4 transition-transform" 
            :class="{ 'rotate-180': showAllWriters }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const opinionsStore = useOpinionsStore()

const opinions = computed(() => opinionsStore.opinions)
const writers = computed(() => opinionsStore.writers)
const loading = computed(() => opinionsStore.loading)
const hasMore = computed(() => opinionsStore.hasMore)

// فصل المقالات حسب وجود الصورة
const opinionsWithImages = computed(() => opinions.value.filter(opinion => opinion.image))
const opinionsWithoutImages = computed(() => opinions.value.filter(opinion => !opinion.image))

// إدارة عرض الكُتاب
const showAllWriters = ref(false)
const displayedWriters = computed(() => {
  return showAllWriters.value ? writers.value : writers.value.slice(0, 12)
})

const toggleWritersView = () => {
  showAllWriters.value = !showAllWriters.value
}

const searchQuery = ref('')
const sortBy = ref('latest')

const { getImageUrl } = useImageUrl()
const settingsStore = useSettingsStore()

// SEO Meta Tags - ديناميكي من Backend
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    useSeoMeta({
      title: 'مقالات الرأي',
      description: `اقرأ مقالات الرأي والتحليلات من كُتابنا المميزين في ${siteName}. آراء متنوعة وتحليلات عميقة للأحداث الجارية`,
      keywords: 'مقالات رأي، تحليلات، كُتاب، آراء، مقالات',
      ogTitle: `مقالات الرأي - ${siteName}`,
      ogDescription: 'اقرأ مقالات الرأي والتحليلات من كُتابنا المميزين'
    })
  }
})

// جلب المقالات
const fetchOpinions = async (append = false) => {
  const params = {
    search: searchQuery.value || undefined,
    sort: sortBy.value,
    per_page: 9,
    page: append ? opinionsStore.pagination.current_page + 1 : 1
  }
  
  await opinionsStore.fetchOpinions(params)
}

// البحث مع تأخير
let searchTimeout: NodeJS.Timeout | null = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchOpinions()
  }, 500)
}

// تحميل المزيد
const loadMore = () => {
  fetchOpinions(true)
}

// مسح الفلاتر
const clearFilters = () => {
  searchQuery.value = ''
  sortBy.value = 'latest'
  fetchOpinions()
}

// جلب البيانات عند التحميل
onMounted(() => {
  fetchOpinions()
  opinionsStore.fetchWriters()
})
</script>

<style scoped>
.fade-slide-enter-active {
  transition: all 0.3s ease-out;
}

.fade-slide-leave-active {
  transition: all 0.2s ease-in;
}

.fade-slide-enter-from {
  transform: translateY(20px);
  opacity: 0;
}

.fade-slide-leave-to {
  opacity: 0;
}

.fade-slide-move {
  transition: transform 0.3s ease;
}
</style>
