<template>
  <div v-if="ads.length > 0" class="footer-ads-container">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
          إعلان
        </div>
        <AdvertisementZone
          position="footer"
          :page="currentPage"
          :auto-rotate="false"
          :show-dots="false"
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
  ads.value = await getByPosition('footer', device)
}

onMounted(() => {
  loadAds()
})
</script>

<style scoped>
.footer-ads-container {
  padding: 2rem 0;
  background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.02));
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.dark .footer-ads-container {
  background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.02));
  border-top-color: rgba(255, 255, 255, 0.05);
}

@media (max-width: 640px) {
  .footer-ads-container {
    padding: 1rem 0;
  }
}
</style>
