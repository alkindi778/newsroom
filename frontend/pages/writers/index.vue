<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-900 mb-2">كُتاب الرأي</h1>
      <p class="text-gray-600">تعرف على كُتابنا وآرائهم المتنوعة</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-8">
      <div class="flex flex-wrap items-center gap-4">
        <!-- البحث -->
        <div class="flex-1 min-w-[200px]">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ابحث عن كاتب..."
            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none"
            @input="debouncedSearch"
          />
        </div>

        <!-- الترتيب -->
        <select
          v-model="sortBy"
          class="px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none"
          @change="fetchWriters()"
        >
          <option value="name">الاسم</option>
          <option value="articles">الأكثر نشاطاً</option>
          <option value="latest">الأحدث</option>
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
    <LoadingSpinner v-if="loading && writers.length === 0" type="dots" size="lg" text="جاري تحميل الكُتاب..." color="secondary" />

    <!-- Grid الكُتاب -->
    <div v-else-if="writers.length > 0">
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        <TransitionGroup name="fade-slide">
          <NuxtLink
            v-for="writer in writers"
            :key="writer.id"
            :to="`/writers/${writer.slug}`"
            class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 p-4 text-center"
          >
            <div class="relative w-24 h-24 mx-auto mb-3">
              <img
                :src="getImageUrl(writer.image)"
                :alt="writer.name"
                loading="lazy"
                class="w-full h-full object-cover rounded-full border-4 border-orange-400 group-hover:border-orange-600 transition-colors"
              />
            </div>
            <h3 class="font-bold text-gray-900 group-hover:text-orange-600 transition-colors mb-1">
              {{ writer.name }}
            </h3>
            <p v-if="writer.specialty" class="text-xs text-gray-500 mb-2">{{ writer.specialty }}</p>
            <div class="flex items-center justify-center gap-2 text-xs text-gray-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>{{ writer.opinions_count || 0 }} مقال</span>
            </div>
          </NuxtLink>
        </TransitionGroup>
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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <p class="text-gray-600 text-lg">لا يوجد كُتاب متاحون</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const writersStore = useWritersStore()

const writers = computed(() => writersStore.writers)
const loading = computed(() => writersStore.loading)
const hasMore = computed(() => writersStore.hasMore)

const searchQuery = ref('')
const sortBy = ref('name')

const { getImageUrl } = useImageUrl()
const settingsStore = useSettingsStore()

// SEO Meta Tags - ديناميكي من Backend
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  // فقط إذا كان siteName موجود
  if (siteName) {
    useSeoMeta({
      title: 'كُتاب الرأي',
      description: `تعرف على كُتاب الرأي في ${siteName} وآرائهم المتنوعة. كُتاب متخصصون في مختلف المجالات`,
      keywords: 'كُتاب، كُتاب رأي، محللون، خبراء، كتّاب',
      ogTitle: `كُتاب الرأي - ${siteName}`,
      ogDescription: 'تعرف على كُتاب الرأي وآرائهم المتنوعة'
    })
  }
})

// جلب الكُتاب
const fetchWriters = async (append = false) => {
  const params = {
    search: searchQuery.value || undefined,
    sort: sortBy.value,
    per_page: 24,
    page: append ? writersStore.pagination.current_page + 1 : 1
  }
  
  await writersStore.fetchWriters(params)
}

// البحث مع تأخير
let searchTimeout: NodeJS.Timeout | null = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchWriters()
  }, 500)
}

// تحميل المزيد
const loadMore = () => {
  fetchWriters(true)
}

// مسح الفلاتر
const clearFilters = () => {
  searchQuery.value = ''
  sortBy.value = 'name'
  fetchWriters()
}

// جلب البيانات عند التحميل
onMounted(() => {
  fetchWriters()
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
