<template>
  <div v-if="ads.length > 0" class="header-ads-container">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
          إعلان
        </div>
        <AdvertisementZone
          position="header"
          :page="currentPage"
          :auto-rotate="true"
          :rotate-interval="10000"
          :show-dots="true"
          transition-name="fade"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdvertisements } from '~/composables/useAdvertisements'
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const route = useRoute()
const { getByPosition, getDeviceType } = useAdvertisements()

const ads = ref<any[]>([])

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

const loadAds = async () => {
  const device = getDeviceType()
  ads.value = await getByPosition('header', device)
}

onMounted(() => {
  loadAds()
})
</script>

<style scoped>
.header-ads-container {
  @apply py-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700;
}

@media (max-width: 640px) {
  .header-ads-container {
    @apply py-2;
  }
}
</style>
