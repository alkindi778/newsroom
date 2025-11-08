<template>
  <div>
    <!-- Header Advertisement (Full Width) -->
    <div class="mt-8">
      <SectionsHeaderAds page="home" />
    </div>

    <!-- Homepage Top Advertisement (After Header, Before Content) -->
    <div class="container mx-auto px-4 my-8">
      <AdvertisementZone position="homepage_top" page="home" :auto-rotate="false" :show-dots="false" />
    </div>

    <!-- Article Top Advertisement (also show in homepage) -->
    <div class="container mx-auto px-4 my-8">
      <AdvertisementZone position="article_top" page="home" :auto-rotate="false" :show-dots="false" />
    </div>

    <!-- Loading State -->
    <div v-if="sectionsStore.loading" class="container mx-auto px-4 py-8">
      <div class="animate-pulse space-y-8">
        <div class="h-96 bg-gray-200 rounded-lg"></div>
        <div class="h-64 bg-gray-200 rounded-lg"></div>
        <div class="h-64 bg-gray-200 rounded-lg"></div>
      </div>
    </div>

    <!-- Dynamic Sections -->
    <template v-else>
      <template v-for="section in sectionsStore.activeSections" :key="section.id">
        <!-- Videos Only: Full Width -->
        <div v-if="section.type === 'videos'" class="mb-8">
          <DynamicSection :section="section" />
        </div>
        
        <!-- All Other Sections: Container -->
        <div v-else class="container mx-auto px-4 mb-8">
          <DynamicSection :section="section" />
        </div>

        <!-- Advertisement After Section -->
        <SectionAdvertisement :section-id="section.id" page="home" />
      </template>
    </template>

    <!-- Fallback: Static sections if API fails -->
    <div v-if="!sectionsStore.loading && sectionsStore.sections.length === 0" class="container mx-auto px-4 py-8">
      <!-- السلايدر الرئيسي مع الأخبار الجانبية -->
      <section v-if="sliderArticles.length > 0" class="mb-12">
        <HeroSlider 
          :articles="sliderArticles" 
          :sideArticles="sidebarArticles"
        />
      </section>

      <!-- أحدث الأخبار -->
      <SectionsLatestNewsSection />

      <!-- الأكثر قراءة -->
      <section class="mb-12">
        <TrendingArticles />
      </section>

      <!-- فيديو العربية - Full Width -->
      <section class="mb-12">
        <TrendingVideos />
      </section>

      <!-- مقالات الرأي -->
      <SectionsOpinionsSection />
    </div>

    <!-- Homepage Bottom Advertisement -->
    <div class="container mx-auto px-4 my-8">
      <AdvertisementZone position="homepage_bottom" page="home" :auto-rotate="false" :show-dots="false" />
    </div>

    <!-- Footer Advertisement (Full Width) -->
    <SectionsFooterAds page="home" />
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'
import SectionAdvertisement from '~/components/SectionAdvertisement.vue'

const articlesStore = useArticlesStore()
const opinionsStore = useOpinionsStore()
const settingsStore = useSettingsStore()
const sectionsStore = useHomepageSectionsStore()

const featuredArticles = computed(() => articlesStore.featuredArticles)
const articles = computed(() => articlesStore.articles)

// السلايدر والأخبار الجانبية
const sliderArticles = computed(() => featuredArticles.value.slice(0, 6))

const sidebarArticles = computed(() => {
  // إذا كان هناك 10 أخبار أو أكثر، استخدم 6-10
  if (featuredArticles.value.length >= 10) {
    return featuredArticles.value.slice(6, 10)
  }
  // إذا كان أقل، استخدم الأخبار الموجودة أو كررها
  const available = featuredArticles.value.slice(6)
  if (available.length >= 4) return available.slice(0, 4)
  
  // املأ بأحدث الأخبار إذا لم يكن هناك ما يكفي
  const combined = [...available, ...articles.value]
  return combined.slice(0, 4)
})

// SEO Meta Tags
const { setHomeSeoMeta } = useAppSeoMeta()

// تطبيق SEO للصفحة الرئيسية - خارج onMounted ليعمل في SSR
watchEffect(() => {
  if (settingsStore.settings.site_name) {
    setHomeSeoMeta()
  }
})

// جلب البيانات في onMounted
onMounted(async () => {
  try {
    // جلب الإعدادات وأقسام الصفحة الرئيسية أولاً
    await Promise.all([
      settingsStore.fetchSettings(),
      sectionsStore.fetchSections()
    ])
    
    // جلب الأخبار المميزة أولاً (للسلايدر) - للـ fallback فقط
    await articlesStore.fetchFeaturedArticles(10)
    
    // ثم جلب باقي البيانات بشكل متوازي - للـ fallback فقط
    await Promise.all([
      articlesStore.fetchArticles({ per_page: 8 }),
      opinionsStore.fetchFeaturedOpinions() // سيجلب 10 مقالات افتراضياً
    ])
  } catch (error) {
    console.error('Error loading homepage data:', error)
  }
})
</script>
