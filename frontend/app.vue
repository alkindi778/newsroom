<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
const settingsStore = useSettingsStore()
const config = useRuntimeConfig()

// جلب الإعدادات قبل render الصفحة (SSR)
await settingsStore.fetchSettings()

// إعدادات Meta Tags العامة من Backend
const siteName = computed(() => settingsStore.getSetting('site_name'))
const siteDescription = computed(() => settingsStore.getSetting('site_description'))
const siteLocale = computed(() => settingsStore.getSetting('site_locale'))
const twitterHandle = computed(() => settingsStore.getSetting('twitter_handle'))
const themeColor = computed(() => settingsStore.getSetting('theme_color'))
const siteFavicon = computed(() => {
  const favicon = settingsStore.getSetting('site_favicon')
  if (!favicon || favicon === '/favicon.ico') return '/favicon.ico'
  return favicon.startsWith('http') ? favicon : `${(config as any).public.apiBase.replace('/api/v1', '')}/storage/${favicon}`
})

// تطبيق SEO Meta Tags عالمية من Backend
watchEffect(() => {
  // استخدام useSeoMeta للـ SEO tags
  useSeoMeta({
    description: siteDescription.value,
    ogSiteName: siteName.value,
    ogLocale: siteLocale.value,
    twitterCard: 'summary_large_image',
    twitterSite: twitterHandle.value
  })

  // استخدام useHead للإعدادات الأخرى
  useHead({
    titleTemplate: siteName.value ? `%s - ${siteName.value}` : '%s',
    htmlAttrs: {
      lang: 'ar',
      dir: 'rtl'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { name: 'format-detection', content: 'telephone=no' },
      ...(themeColor.value ? [{ name: 'theme-color', content: themeColor.value }] : []),
      { name: 'mobile-web-app-capable', content: 'yes' },
      { name: 'apple-mobile-web-app-capable', content: 'yes' },
      { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
      ...(siteName.value ? [{ name: 'apple-mobile-web-app-title', content: siteName.value }] : []),
      { name: 'robots', content: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' },
      { name: 'googlebot', content: 'index, follow' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: siteFavicon.value },
      { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' },
      { rel: 'manifest', href: '/manifest.json' }
    ]
  })
})
</script>
