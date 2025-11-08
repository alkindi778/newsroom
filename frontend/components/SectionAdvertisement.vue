<template>
  <div v-if="ads.length > 0" class="my-8">
    <div class="container mx-auto px-4">
      <!-- Get layout from first ad -->
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
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAdvertisements } from '~/composables/useAdvertisements'
import Advertisement from '~/components/Advertisement.vue'

interface Props {
  sectionId: number
  page?: string
}

const props = withDefaults(defineProps<Props>(), {
  page: 'home'
})

const { getAfterSection } = useAdvertisements()
const ads = ref<any[]>([])

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
  if (index === total - 1) return false
  if (layout === 'single') return true
  return false
}

onMounted(async () => {
  ads.value = await getAfterSection(props.sectionId, props.page)
})
</script>

<style scoped>
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
</style>
