<template>
  <div 
    v-if="ads.length > 0"
    class="advertisement-zone"
    :class="`zone-${position}`"
  >
    <!-- Get layout from first ad (all ads in same position should have same layout) -->
    <div 
      v-if="ads.length > 0"
      :class="getLayoutClass(ads[0].layout || 'single')"
    >
      <div 
        v-for="(ad, index) in ads" 
        :key="ad.id" 
        class="ad-item"
        :class="{ 'mb-4': shouldShowGap(ads[0].layout, index, ads.length) }"
      >
        <Advertisement
          :advertisement="ad"
          :track-views="true"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAdvertisements } from '~/composables/useAdvertisements'
import Advertisement from './Advertisement.vue'

interface Props {
  position?: string
  page?: string
  autoRotate?: boolean
  rotateInterval?: number
  showDots?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  autoRotate: true,
  rotateInterval: 5000,
  showDots: true,
})

const { getByPosition, getForPage, getDeviceType } = useAdvertisements()

const ads = ref<any[]>([])

const loadAdvertisements = async () => {
  const device = getDeviceType()
  
  let loadedAds: any[] = []
  
  // Load by page first to respect target_pages filtering
  if (props.page) {
    loadedAds = await getForPage(props.page, device)
    // Filter by position if specified
    if (props.position) {
      loadedAds = loadedAds.filter((ad: any) => ad.position === props.position)
    }
  }
  
  // Fallback: Load by position only (won't filter by page)
  if (loadedAds.length === 0 && props.position) {
    loadedAds = await getByPosition(props.position, device)
  }
  
  ads.value = loadedAds
}

const getLayoutClass = (layout: string) => {
  switch (layout) {
    case 'double':
      return 'ad-layout-double'
    case 'triple':
      return 'ad-layout-triple'
    default:
      return 'ad-layout-single'
  }
}

const shouldShowGap = (layout: string, index: number, total: number) => {
  // Don't show gap after last item
  if (index === total - 1) return false
  
  // For single layout, show gap between all items
  if (layout === 'single') return true
  
  // For double/triple, no gap needed (flex handles spacing)
  return false
}

onMounted(() => {
  loadAdvertisements()
})
</script>

<style scoped>
.advertisement-zone {
  width: 100%;
}

.zone-sidebar_right,
.zone-sidebar_left {
  position: sticky;
  top: 1rem;
}

.zone-article_top {
  margin-bottom: 0;
}

.zone-article_bottom {
  margin-top: 0;
}

.zone-article_middle {
  margin-top: 0;
  margin-bottom: 0;
}

.zone-between_articles {
  margin-top: 0;
  margin-bottom: 0;
}

/* Layout Styles */
.ad-layout-single {
  display: block;
  width: 100%;
}

.ad-layout-double {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  width: 100%;
}

.ad-layout-triple {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  width: 100%;
}

.ad-item {
  width: 100%;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .ad-layout-triple {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .ad-layout-double,
  .ad-layout-triple {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
}

/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide Transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.5s ease;
}

.slide-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>
