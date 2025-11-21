<template>
  <section v-if="featuredOpinions.length > 0" class="mb-12">
    <div class="mb-6">
      <NuxtLink to="/opinions" class="flex items-center gap-2">
        <h2 class="text-3xl font-bold text-gray-900">{{ $t('common.opinions') }}</h2>
        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </NuxtLink>
    </div>

    <!-- مقالات مع صور -->
    <div v-if="opinionsWithImages.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <OpinionCard 
        v-for="opinion in opinionsWithImages.slice(0, 3)" 
        :key="opinion.id" 
        :opinion="opinion" 
      />
    </div>

    <!-- مقالات بدون صور -->
    <div v-if="opinionsWithoutImages.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <OpinionCard 
        v-for="opinion in opinionsWithoutImages.slice(0, 3)" 
        :key="opinion.id" 
        :opinion="opinion" 
      />
    </div>

    <!-- زر المزيد -->
    <div v-if="featuredOpinions.length >= 6" class="mt-8">
      <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-gray-300"></div>
        <NuxtLink
          to="/opinions"
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
const opinionsStore = useOpinionsStore()
const featuredOpinions = computed(() => opinionsStore.featuredOpinions)

// فصل المقالات حسب وجود الصورة
const opinionsWithImages = computed(() => 
  featuredOpinions.value.filter(opinion => opinion.image)
)

const opinionsWithoutImages = computed(() => 
  featuredOpinions.value.filter(opinion => !opinion.image)
)
</script>
