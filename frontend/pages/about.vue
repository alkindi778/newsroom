<template>
  <div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">من نحن</h1>
      
      <div class="prose prose-lg max-w-none">
        <p class="text-xl text-gray-700 leading-relaxed mb-6">
          موقع إخباري شامل يقدم آخر الأخبار والتحليلات من مختلف المجالات.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">رؤيتنا</h2>
        <p class="text-gray-700 leading-relaxed">
          نسعى لتقديم محتوى إخباري موثوق ومتنوع يلبي احتياجات القراء في الوطن العربي.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">مهمتنا</h2>
        <p class="text-gray-700 leading-relaxed">
          تقديم الأخبار بمصداقية وشفافية، مع التركيز على الجودة والموضوعية في التغطية الإخبارية.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const settingsStore = useSettingsStore()

watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    useSeoMeta({
      title: 'من نحن',
      description: `تعرف على ${siteName} الإخباري ورؤيتنا ومهمتنا في تقديم الأخبار`,
      ogTitle: `من نحن - ${siteName}`,
      ogDescription: `تعرف على ${siteName} الإخباري ورؤيتنا ومهمتنا`
    })
  }
})
</script>
