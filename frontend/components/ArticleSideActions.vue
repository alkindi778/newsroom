<template>
  <div class="absolute top-0 z-50 hidden xl:block" style="left: calc(100% + 20px);">
    <div class="sticky flex flex-col gap-3" style="top: 120px;">
    <!-- Font Size Controls -->
    <div class="bg-white rounded-full shadow-lg p-1">
      <!-- Increase Font -->
      <button 
        @click="increaseFontSize"
        class="w-12 h-12 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors"
        title="تكبير النص"
      >
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
      </button>

      <!-- Font Size Indicator -->
      <div class="w-12 h-12 flex items-center justify-center">
        <span class="text-sm font-bold text-gray-700">{{ fontSizeLabel }}</span>
      </div>

      <!-- Decrease Font -->
      <button 
        @click="decreaseFontSize"
        class="w-12 h-12 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors"
        title="تصغير النص"
      >
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
        </svg>
      </button>

      <!-- Text Format Icon -->
      <div class="w-12 h-12 flex items-center justify-center border-t border-gray-200 mt-1 pt-2">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </div>
    </div>

    <!-- Social Share Buttons -->
    <div class="flex flex-col gap-3">
      <!-- Facebook -->
      <a 
        :href="facebookShareUrl" 
        target="_blank"
        class="w-12 h-12 rounded-full bg-[#1877F2] hover:bg-[#0d65d9] flex items-center justify-center transition-colors shadow-lg"
        title="مشاركة على Facebook"
      >
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
      </a>

      <!-- X (Twitter) -->
      <a 
        :href="twitterShareUrl" 
        target="_blank"
        class="w-12 h-12 rounded-full bg-black hover:bg-gray-800 flex items-center justify-center transition-colors shadow-lg"
        title="مشاركة على X"
      >
        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
      </a>

      <!-- Telegram -->
      <a 
        :href="telegramShareUrl" 
        target="_blank"
        class="w-12 h-12 rounded-full bg-[#0088cc] hover:bg-[#0077b5] flex items-center justify-center transition-colors shadow-lg"
        title="مشاركة على Telegram"
      >
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
        </svg>
      </a>

      <!-- WhatsApp -->
      <a 
        :href="whatsappShareUrl" 
        target="_blank"
        class="w-12 h-12 rounded-full bg-[#25D366] hover:bg-[#20ba5a] flex items-center justify-center transition-colors shadow-lg"
        title="مشاركة على WhatsApp"
      >
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
      </a>

      <!-- Copy Link -->
      <button 
        @click="copyLink"
        class="w-12 h-12 rounded-full bg-gray-900 hover:bg-gray-700 flex items-center justify-center transition-colors shadow-lg"
        :title="linkCopied ? 'تم النسخ!' : 'نسخ الرابط'"
      >
        <svg v-if="!linkCopied" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </button>
    </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  url: string
  title: string
}>()

// Font size control
const fontSize = ref(100) // percentage
const linkCopied = ref(false)

const fontSizeLabel = computed(() => {
  if (fontSize.value >= 130) return 'A+'
  if (fontSize.value >= 110) return 'A'
  if (fontSize.value <= 80) return 'A-'
  return 'A'
})

const increaseFontSize = () => {
  if (fontSize.value < 150) {
    fontSize.value += 10
    updateFontSize()
  }
}

const decreaseFontSize = () => {
  if (fontSize.value > 70) {
    fontSize.value -= 10
    updateFontSize()
  }
}

const updateFontSize = () => {
  const articleContent = document.querySelector('.article-content')
  if (articleContent) {
    (articleContent as HTMLElement).style.fontSize = `${fontSize.value}%`
  }
}

// Share URLs
const facebookShareUrl = computed(() => {
  return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.url)}`
})

const twitterShareUrl = computed(() => {
  return `https://twitter.com/intent/tweet?url=${encodeURIComponent(props.url)}&text=${encodeURIComponent(props.title)}`
})

const telegramShareUrl = computed(() => {
  return `https://t.me/share/url?url=${encodeURIComponent(props.url)}&text=${encodeURIComponent(props.title)}`
})

const whatsappShareUrl = computed(() => {
  return `https://wa.me/?text=${encodeURIComponent(props.title + ' ' + props.url)}`
})

// Copy link
const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(props.url)
    linkCopied.value = true
    setTimeout(() => {
      linkCopied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}

// Reset font size on unmount
onUnmounted(() => {
  const articleContent = document.querySelector('.article-content')
  if (articleContent) {
    (articleContent as HTMLElement).style.fontSize = '100%'
  }
})
</script>
