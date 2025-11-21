<template>
  <section class="mb-12">
    <div class="mb-6">
      <NuxtLink to="/news" class="flex items-center gap-2">
        <h2 class="text-3xl font-bold text-gray-900">
          آخر الأخبار
        </h2>
        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </NuxtLink>
    </div>

    <!-- Loading State -->
    <LoadingSpinner v-if="loading" />

    <!-- Grid الأخبار - 4 في السطر -->
    <div v-else-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <TransitionGroup name="fade-slide">
        <NewsCard 
          v-for="article in articles" 
          :key="article.id" 
          :article="article" 
        />
      </TransitionGroup>
    </div>

    <!-- حالة فارغة -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-600 text-lg">{{ $t('common.no_results') }}</p>
    </div>

    <!-- زر المزيد -->
    <div v-if="articles.length >= 8" class="mt-8">
      <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-gray-300"></div>
        <NuxtLink
          to="/news"
          class="inline-flex items-center gap-2 px-6 py-2 border border-gray-900 text-gray-900 font-semibold whitespace-nowrap rounded-md"
        >
          <span>{{ $t('common.read_more') }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const articlesStore = useArticlesStore()

const articles = computed(() => articlesStore.articles)
const loading = computed(() => articlesStore.loading)
</script>

<style scoped>
.fade-slide-enter-active {
  transition: all 0.3s ease-out;
}

.fade-slide-leave-active {
  transition: all 0.2s ease-in;
}

.fade-slide-enter-from {
  transform: translateY(20px);
  opacity: 0;
}

.fade-slide-leave-to {
  opacity: 0;
}

.fade-slide-move {
  transition: transform 0.3s ease;
}
</style>
