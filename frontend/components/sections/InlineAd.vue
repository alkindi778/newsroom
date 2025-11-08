<template>
  <div v-if="ads.length > 0" class="inline-ad-container">
    <div class="inline-ad-wrapper">
      <div class="text-xs text-gray-400 mb-2 text-center uppercase tracking-wide">
        إعلان
      </div>
      <AdvertisementZone
        :position="position"
        :page="currentPage"
        :auto-rotate="false"
        :show-dots="false"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdvertisements } from '~/composables/useAdvertisements'
import AdvertisementZone from '~/components/AdvertisementZone.vue'

interface Props {
  position?: 'article_top' | 'article_middle' | 'article_bottom' | 'between_articles' | 'homepage_top' | 'homepage_bottom'
}

const props = withDefaults(defineProps<Props>(), {
  position: 'article_middle'
})

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
  ads.value = await getByPosition(props.position, device)
}

onMounted(() => {
  loadAds()
})
</script>

<style scoped>
.inline-ad-container {
  margin-top: 2rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: center;
}

.inline-ad-wrapper {
  max-width: 56rem;
  width: 100%;
}

/* Special styling for different positions */
.inline-ad-container :deep(.zone-article_top) {
  border-bottom: 1px solid rgb(229 231 235);
  padding-bottom: 1.5rem;
}

.dark .inline-ad-container :deep(.zone-article_top) {
  border-bottom-color: rgb(55 65 81);
}

.inline-ad-container :deep(.zone-article_bottom) {
  border-top: 1px solid rgb(229 231 235);
  padding-top: 1.5rem;
}

.dark .inline-ad-container :deep(.zone-article_bottom) {
  border-top-color: rgb(55 65 81);
}

.inline-ad-container :deep(.zone-article_middle) {
  border-top: 1px solid rgb(229 231 235);
  border-bottom: 1px solid rgb(229 231 235);
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}

.dark .inline-ad-container :deep(.zone-article_middle) {
  border-top-color: rgb(55 65 81);
  border-bottom-color: rgb(55 65 81);
}

.inline-ad-container :deep(.zone-between_articles) {
  background-color: rgb(249 250 251);
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.dark .inline-ad-container :deep(.zone-between_articles) {
  background-color: rgb(31 41 55);
}

.inline-ad-container :deep(.zone-homepage_top),
.inline-ad-container :deep(.zone-homepage_bottom) {
  background-image: linear-gradient(to right, transparent, rgb(249 250 251), transparent);
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}

.dark .inline-ad-container :deep(.zone-homepage_top),
.dark .inline-ad-container :deep(.zone-homepage_bottom) {
  background-image: linear-gradient(to right, transparent, rgb(31 41 55), transparent);
}

@media (max-width: 768px) {
  .inline-ad-container {
    margin-top: 1rem;
    margin-bottom: 1rem;
  }
}
</style>
