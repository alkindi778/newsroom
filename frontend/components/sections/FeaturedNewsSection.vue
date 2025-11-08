<template>
  <section v-if="featuredArticles.length > 1" class="mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">أخبار مميزة</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <NewsCard 
        v-for="article in featuredArticles.slice(1, 5)" 
        :key="article.id" 
        :article="article" 
      />
    </div>
  </section>
</template>

<script setup lang="ts">
const articlesStore = useArticlesStore()
const featuredArticles = computed(() => articlesStore.featuredArticles)
</script>
