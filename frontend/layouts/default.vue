<template>
  <div class="min-h-screen flex flex-col">
    <LayoutHeader />
    
    <!-- شريط آخر الأخبار -->
    <LatestNewsTicker />
    
    <main class="flex-1">
      <slot />
    </main>
    
    <LayoutFooter />
    
    <!-- شريط الأخبار العاجلة -->
    <ClientOnly>
      <BreakingNewsBar />
    </ClientOnly>
    
    <!-- مطالبة الإشعارات -->
    <ClientOnly>
      <NotificationPrompt />
    </ClientOnly>
    
    <!-- زر العودة للأعلى -->
    <Transition name="fade">
      <button
        v-if="showScrollTop"
        @click="scrollToTop"
        class="fixed bottom-8 left-8 bg-primary hover:bg-primary-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 z-40"
        title="العودة للأعلى"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
      </button>
    </Transition>
  </div>
</template>

<script setup lang="ts">
const showScrollTop = ref(false)

// إظهار زر العودة للأعلى عند التمرير
const handleScroll = () => {
  showScrollTop.value = window.scrollY > 300
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
