<template>
  <aside v-if="hasAds" class="sidebar-ads space-y-4">
    <!-- Right Sidebar Ads -->
    <div v-if="rightSidebarAds.length > 0" class="sidebar-ads-section">
      <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 text-center">
        إعلان
      </div>
      <AdvertisementZone
        position="sidebar_right"
        :page="currentPage"
        :auto-rotate="true"
        :rotate-interval="8000"
        :show-dots="false"
      />
    </div>

    <!-- Left Sidebar Ads -->
    <div v-if="leftSidebarAds.length > 0" class="sidebar-ads-section">
      <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 text-center">
        إعلان
      </div>
      <AdvertisementZone
        position="sidebar_left"
        :page="currentPage"
        :auto-rotate="true"
        :rotate-interval="8000"
        :show-dots="false"
      />
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAdvertisements } from '~/composables/useAdvertisements'
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const route = useRoute()
const { getByPosition, getDeviceType } = useAdvertisements()

const rightSidebarAds = ref<any[]>([])
const leftSidebarAds = ref<any[]>([])

const currentPage = computed(() => {
  const path = route.path
  if (path === '/') return 'home'
  if (path.startsWith('/article/')) return 'article'
  if (path.startsWith('/category/')) return 'category'
  if (path.startsWith('/articles')) return 'articles'
  if (path.startsWith('/opinions')) return 'opinions'
  if (path.startsWith('/videos')) return 'videos'
  return 'home'
})

const hasAds = computed(() => {
  return rightSidebarAds.value.length > 0 || leftSidebarAds.value.length > 0
})

const loadAds = async () => {
  const device = getDeviceType()
  
  // Load right sidebar ads
  rightSidebarAds.value = await getByPosition('sidebar_right', device)
  
  // Load left sidebar ads
  leftSidebarAds.value = await getByPosition('sidebar_left', device)
}

onMounted(() => {
  loadAds()
})
</script>

<style scoped>
.sidebar-ads {
  @apply w-full;
}

.sidebar-ads-section {
  @apply bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm;
}

/* Hide on mobile */
@media (max-width: 768px) {
  .sidebar-ads {
    @apply hidden;
  }
}
</style>
