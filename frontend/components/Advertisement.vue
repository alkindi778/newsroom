<template>
  <div 
    v-if="advertisement"
    class="advertisement-container"
    :class="`ad-type-${advertisement.type} ad-position-${advertisement.position}`"
    :style="containerStyle"
    @click="handleClick"
  >
    <!-- Image Advertisement -->
    <a
      v-if="advertisement.image_url && advertisement.link"
      :href="advertisement.link"
      :target="advertisement.open_new_tab ? '_blank' : '_self'"
      :rel="advertisement.open_new_tab ? 'noopener noreferrer' : ''"
      class="block"
      :aria-label="advertisement.title"
    >
      <img
        :src="advertisement.image_url"
        :alt="advertisement.title"
        :width="advertisement.width || 'auto'"
        :height="advertisement.height || 'auto'"
        :style="getImageStyle"
        loading="lazy"
      />
    </a>

    <!-- Image Without Link -->
    <img
      v-else-if="advertisement.image_url && !advertisement.link"
      :src="advertisement.image_url"
      :alt="advertisement.title"
      :width="advertisement.width || 'auto'"
      :height="advertisement.height || 'auto'"
      :style="getImageStyle"
      loading="lazy"
    />

    <!-- HTML Content Advertisement -->
    <div
      v-else-if="advertisement.content"
      class="ad-content"
      v-html="advertisement.content"
    ></div>

    <!-- Fallback -->
    <div v-else class="ad-placeholder bg-gray-100 dark:bg-gray-800 p-4 text-center">
      <span class="text-sm text-gray-500">إعلان</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAdvertisements } from '~/composables/useAdvertisements'

interface Props {
  advertisement: {
    id: number
    title: string
    type: string
    position: string
    image_url: string
    link: string | null
    open_new_tab: boolean
    width: number | null
    height: number | null
    content: string | null
  }
  trackViews?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  trackViews: true
})

const { trackView, trackClick } = useAdvertisements()

const containerStyle = computed(() => {
  const styles: Record<string, string> = {}
  
  // Full width container - no max-width restriction
  styles.width = '100%'
  
  return styles
})

const getImageStyle = computed(() => {
  const styles: Record<string, string> = {}
  
  // Make image fill the container width
  styles.width = '100%'
  styles.height = 'auto'
  
  // Maintain aspect ratio
  styles.objectFit = 'contain'
  
  return styles
})

const handleClick = async () => {
  if (props.advertisement.link) {
    await trackClick(props.advertisement.id)
  }
}

onMounted(() => {
  if (props.trackViews) {
    trackView(props.advertisement.id)
  }
})
</script>

<style scoped>
.advertisement-container {
  display: block;
  width: 100%;
  overflow: hidden;
}

.advertisement-container a,
.advertisement-container img {
  display: block;
  width: 100%;
}

.ad-type-banner {
  /* Full width banner - no border radius */
  box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}

.ad-type-sidebar {
  margin-bottom: 1rem;
}

.ad-type-popup {
  position: fixed;
  z-index: 50;
  border-radius: 0.5rem;
  box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
}

.ad-type-inline {
  margin-top: 1rem;
  margin-bottom: 1rem;
}

.ad-type-floating {
  position: fixed;
  z-index: 40;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .ad-type-sidebar {
    width: 100%;
  }
}

.ad-content :deep(img) {
  max-width: 100%;
  height: auto;
}

.ad-content :deep(a) {
  color: rgb(37 99 235);
}

.ad-content :deep(a:hover) {
  color: rgb(30 64 175);
}
</style>
