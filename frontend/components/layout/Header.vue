<template>
  <header class="bg-white border-b border-gray-200">
    <!-- شريط علوي رفيع -->
    <div class="bg-navy text-white">
      <div class="container mx-auto px-2 sm:px-4">
        <div class="flex items-center justify-between h-7 sm:h-8 text-[10px] sm:text-xs">
          <!-- التاريخ -->
          <ClientOnly>
            <div class="flex items-center gap-2">
              <span class="truncate">{{ currentDate }}</span>
            </div>
          </ClientOnly>

          <!-- روابط سريعة -->
          <div class="hidden md:flex items-center gap-6">
            <NuxtLink to="/about" class="hover:text-gray-300 transition-colors">من نحن</NuxtLink>
            <NuxtLink to="/contact" class="hover:text-gray-300 transition-colors">اتصل بنا</NuxtLink>
            <a href="/rss" target="_blank" rel="noopener" class="flex items-center gap-1 hover:text-primary-200 transition-colors">
              <span>RSS</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4a16 16 0 0116 16M4 11a9 9 0 019 9M6 18a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- الشعار والأدوات -->
    <div class="container mx-auto px-2 sm:px-4">
      <div class="flex items-center justify-between min-h-16 sm:min-h-20 py-3">
        <!-- الشعار على اليسار -->
        <NuxtLink to="/" class="flex-shrink-0">
          <div class="text-left flex items-center gap-3">
            <!-- Logo Image if available -->
            <img 
              v-if="siteLogo" 
              :src="siteLogo" 
              :alt="siteName"
              :style="{ width: logoWidth + 'px', height: 'auto', maxWidth: '100%' }"
              class="object-contain"
            />
            <!-- Site Name - يظهر فقط إذا لم يكن هناك شعار -->
            <h1 v-if="!siteLogo" class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
              {{ siteName }}
            </h1>
          </div>
        </NuxtLink>

        <!-- الأدوات على اليمين -->
        <div class="flex items-center gap-1 sm:gap-2 md:gap-3">
          <!-- القائمة للموبايل -->
          <button 
            @click="toggleMobileMenu"
            class="md:hidden p-1.5 sm:p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <!-- البحث -->
          <button @click="toggleSearch" class="p-1.5 sm:p-2 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </button>

          <!-- زر مباشر -->
          
        </div>
      </div>
    </div>

    <!-- شريط البحث المنسدل -->
    <Transition name="slide-down">
      <div v-if="showSearch" class="bg-gray-50 border-t border-b border-gray-200">
        <div class="container mx-auto px-4 py-4">
          <div class="flex items-center gap-3">
            <SearchBar class="flex-1" />
            <button 
              @click="showSearch = false"
              class="p-2 hover:bg-gray-200 rounded-full transition-colors"
            >
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- قائمة التصنيفات -->
    <nav class="hidden md:block bg-white border-t border-gray-100 relative overflow-visible">
      <div class="container mx-auto px-2 sm:px-4">
        <ul class="flex items-center gap-2 md:gap-0 md:justify-between py-0 overflow-visible scrollbar-hide">
          <li>
            <NuxtLink 
              to="/" 
              class="nav-link"
              :class="{ 'active': isActive('/') }"
            >
              الرئيسية
            </NuxtLink>
          </li>
          <!-- عرض أول 9 أقسام -->
          <li v-for="category in visibleCategories" :key="category.id" class="relative group">
            <!-- إذا كان للقسم أطفال: عرض dropdown بدل الرابط -->
            <template v-if="hasChildren(category.id)">
              <button 
                class="nav-link flex gap-1"
                style="display: flex; align-items: center; justify-content: center;"
                :class="{ 'active': isActive(`/category/${category.slug}`) }"
              >
                <span>{{ category.name }}</span>
                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" style="flex-shrink: 0; margin-top: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
              </button>
              
              <!-- Dropdown menu - positioned below navbar item -->
              <div class="absolute right-0 top-full mt-0 w-48 bg-white border border-gray-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <NuxtLink 
                  v-for="child in getChildren(category.id)" 
                  :key="child.id"
                  :to="`/category/${child.slug}`"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg last:rounded-b-lg transition-colors"
                  :class="{ 'bg-blue-50 text-blue-600': isActive(`/category/${child.slug}`) }"
                >
                  {{ child.name }}
                </NuxtLink>
              </div>
            </template>
            
            <!-- إذا لم يكن للقسم أطفال: رابط عادي -->
            <NuxtLink 
              v-else
              :to="`/category/${category.slug}`" 
              class="nav-link"
              :class="{ 'active': isActive(`/category/${category.slug}`) }"
            >
              {{ category.name }}
            </NuxtLink>
          </li>
          
          <li>
            <NuxtLink 
              to="/opinions" 
              class="nav-link"
              :class="{ 'active': isActive('/opinions') }"
            >
              مقالات الرأي
            </NuxtLink>
          </li>
          
          <!-- قائمة "المزيد" للأقسام الإضافية -->
          <li v-if="hasMoreCategories" class="relative group">
            <button 
              class="nav-link flex gap-1"
              style="display: flex; align-items: center; justify-content: center;"
            >
              <span>المزيد</span>
              <svg class="w-4 h-4 transition-transform group-hover:rotate-180" style="flex-shrink: 0; margin-top: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
              </svg>
            </button>
            
            <!-- Dropdown للأقسام الإضافية -->
            <div class="absolute right-0 top-full mt-0 w-56 bg-white border border-gray-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-h-96 overflow-y-auto">
              <NuxtLink 
                v-for="category in moreCategories" 
                :key="category.id"
                :to="`/category/${category.slug}`"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg last:rounded-b-lg transition-colors"
                :class="{ 'bg-blue-50 text-blue-600': isActive(`/category/${category.slug}`) }"
              >
                {{ category.name }}
              </NuxtLink>
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <!-- قائمة الموبايل -->
    <Transition name="slide-down">
      <div v-if="showMobileMenu" class="md:hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="container mx-auto px-3 py-4">
          <nav>
            <ul class="space-y-1">
              <li>
                <NuxtLink 
                  to="/" 
                  class="mobile-nav-link"
                  :class="{ 'active': isActive('/') }"
                  @click="closeMobileMenu"
                >
                  الرئيسية
                </NuxtLink>
              </li>
              <!-- عرض أول 9 أقسام -->
              <li v-for="category in visibleCategories" :key="category.id">
                <!-- إذا كان للقسم أطفال: عرض dropdown -->
                <template v-if="hasChildren(category.id)">
                  <button 
                    class="mobile-nav-link w-full text-right flex items-center justify-between"
                    :class="{ 'active': isActive(`/category/${category.slug}`) }"
                    @click="toggleMobileSubmenu(category.id)"
                  >
                    <span>{{ category.name }}</span>
                    <svg 
                      class="w-4 h-4 transition-transform" 
                      :class="{ 'rotate-180': isCategoryExpanded(category.id) }"
                      fill="none" 
                      stroke="currentColor" 
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                  </button>
                  
                  <!-- Submenu -->
                  <Transition name="slide-down">
                    <ul v-if="isCategoryExpanded(category.id)" class="space-y-1 mt-1 pr-4 border-r-2 border-gray-300">
                      <li v-for="child in getChildren(category.id)" :key="child.id">
                        <NuxtLink 
                          :to="`/category/${child.slug}`"
                          class="mobile-nav-link text-sm"
                          :class="{ 'active': isActive(`/category/${child.slug}`) }"
                          @click="closeMobileMenu"
                        >
                          {{ child.name }}
                        </NuxtLink>
                      </li>
                    </ul>
                  </Transition>
                </template>
                
                <!-- إذا لم يكن للقسم أطفال: رابط عادي -->
                <NuxtLink 
                  v-else
                  :to="`/category/${category.slug}`" 
                  class="mobile-nav-link"
                  :class="{ 'active': isActive(`/category/${category.slug}`) }"
                  @click="closeMobileMenu"
                >
                  {{ category.name }}
                </NuxtLink>
              </li>
              
              <li>
                <NuxtLink 
                  to="/opinions" 
                  class="mobile-nav-link"
                  :class="{ 'active': isActive('/opinions') }"
                  @click="closeMobileMenu"
                >
                  مقالات الرأي
                </NuxtLink>
              </li>
              
              <!-- قائمة "المزيد" للأقسام الإضافية في الموبايل -->
              <li v-if="hasMoreCategories">
                <button 
                  class="mobile-nav-link w-full text-right flex items-center justify-between"
                  @click="toggleMobileSubmenu('more')"
                >
                  <span>المزيد</span>
                  <svg 
                    class="w-4 h-4 transition-transform" 
                    :class="{ 'rotate-180': isCategoryExpanded('more') }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                </button>
                
                <!-- الأقسام الإضافية -->
                <Transition name="slide-down">
                  <ul v-if="isCategoryExpanded('more')" class="space-y-1 mt-1 pr-4 border-r-2 border-gray-300">
                    <li v-for="category in moreCategories" :key="category.id">
                      <NuxtLink 
                        :to="`/category/${category.slug}`"
                        class="mobile-nav-link text-sm"
                        :class="{ 'active': isActive(`/category/${category.slug}`) }"
                        @click="closeMobileMenu"
                      >
                        {{ category.name }}
                      </NuxtLink>
                    </li>
                  </ul>
                </Transition>
              </li>
            </ul>
          </nav>

          <!-- روابط إضافية للموبايل -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-col gap-2 text-sm">
              <NuxtLink to="/about" class="text-gray-600 hover:text-gray-900 px-3.5 py-2" @click="closeMobileMenu">من نحن</NuxtLink>
              <NuxtLink to="/contact" class="text-gray-600 hover:text-gray-900 px-3.5 py-2" @click="closeMobileMenu">اتصل بنا</NuxtLink>
              <a href="/rss" target="_blank" rel="noopener" class="text-gray-600 hover:text-gray-900 px-3.5 py-2">RSS</a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
const categoriesStore = useCategoriesStore()
const settingsStore = useSettingsStore()
const route = useRoute()
const config = useRuntimeConfig()

const showMobileMenu = ref(false)
const showSearch = ref(false)
const expandedMobileCategories = ref<(number | string)[]>([])
const categories = computed(() => categoriesStore.categories)
const mainCategories = computed(() => categoriesStore.getMainCategories)

// تحديد عدد الأقسام المرئية (9 أقسام + الرئيسية + مقالات الرأي = 11 عنصر في القائمة)
const MAX_VISIBLE_CATEGORIES = 9

// الأقسام المرئية (أول 9 أقسام)
const visibleCategories = computed(() => {
  return mainCategories.value.slice(0, MAX_VISIBLE_CATEGORIES)
})

// الأقسام الإضافية (بعد الـ9 أقسام الأولى)
const moreCategories = computed(() => {
  return mainCategories.value.slice(MAX_VISIBLE_CATEGORIES)
})

// هل يوجد أقسام إضافية؟
const hasMoreCategories = computed(() => {
  return mainCategories.value.length > MAX_VISIBLE_CATEGORIES
})

// Check if a category has children
const hasChildren = (categoryId: number) => {
  return categoriesStore.getChildrenOf(categoryId).length > 0
}

// Get children of a category
const getChildren = (categoryId: number) => {
  return categoriesStore.getChildrenOf(categoryId)
}

// Helper function to check if category is expanded
const isCategoryExpanded = (categoryId: number | string): boolean => {
  return expandedMobileCategories.value.some(id => id === categoryId)
}

// Toggle mobile submenu
const toggleMobileSubmenu = (categoryId: number | string) => {
  const index = expandedMobileCategories.value.findIndex(id => id === categoryId)
  if (index > -1) {
    expandedMobileCategories.value.splice(index, 1)
  } else {
    expandedMobileCategories.value = [...expandedMobileCategories.value, categoryId]
  }
}

// Site Settings
const siteName = computed(() => settingsStore.getSetting('site_name', 'غرفة الأخبار'))
const siteLogo = computed(() => {
  const logo = settingsStore.getSetting('site_logo')
  if (!logo) return null
  // إذا كان الشعار يبدأ بـ / نستخدمه مباشرة، وإلا نضيف storage/
  return logo.startsWith('http') ? logo : `${(config as any).public.apiBase.replace('/api/v1', '')}/storage/${logo}`
})
const logoWidth = computed(() => {
  const width = settingsStore.getSetting('site_logo_width', '180')
  return parseInt(width) || 180
})
const showSiteName = computed(() => true) // يمكن جعله setting لاحقاً

// التاريخ الحالي
const currentDate = computed(() => {
  const date = new Date()
  return new Intl.DateTimeFormat('ar-SA', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date)
})

// التحقق من الرابط النشط
const isActive = (path: string) => {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
  showSearch.value = false
}

const toggleSearch = () => {
  showSearch.value = !showSearch.value
  showMobileMenu.value = false
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
  expandedMobileCategories.value = []
}

// جلب الأقسام عند التحميل
onMounted(() => {
  if (categories.value.length === 0) {
    categoriesStore.fetchCategories()
  }
})
</script>

<style scoped>
.nav-link {
  display: inline-block;
  padding: 1rem 0.5rem;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.2s;
  border-bottom: 3px solid transparent;
  letter-spacing: 0.01em;
}

@media (min-width: 640px) {
  .nav-link {
    padding: 1.25rem 0;
    font-size: 1.0625rem;
  }
}

.nav-link:hover {
  color: #111827;
}

.nav-link.active {
  color: #111827;
  font-weight: 700;
  border-bottom-color: #D4AF37;
}

/* إخفاء سكرول بار */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Animation */
.slide-down-enter-active {
  animation: slide-down 0.3s ease-out;
}

.slide-down-leave-active {
  animation: slide-down 0.3s ease-in reverse;
}

@keyframes slide-down {
  from {
    max-height: 0;
    opacity: 0;
  }
  to {
    max-height: 200px;
    opacity: 1;
  }
}

/* Pulse animation للزر المباشر */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Mobile nav link */
.mobile-nav-link {
  display: block;
  padding: 0.625rem 0.875rem;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.mobile-nav-link:hover {
  background-color: #f3f4f6;
  color: #111827;
}

.mobile-nav-link.active {
  background-color: #D4AF37;
  color: white;
  font-weight: 600;
}
</style>
