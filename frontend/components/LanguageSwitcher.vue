<template>
  <button 
    @click="toggleLanguage" 
    class="flex items-center gap-1 sm:gap-2 px-1.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-primary transition-colors rounded-lg hover:bg-gray-50 active:bg-gray-100 touch-manipulation"
    :aria-label="$t('common.language')"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
    </svg>
    <!-- Show code (ar/en) on mobile, full name on desktop -->
    <span class="sm:hidden uppercase font-semibold">{{ currentCode }}</span>
    <span class="hidden sm:inline">{{ currentLocaleName }}</span>
  </button>
</template>

<script setup>
const { locale, locales } = useI18n()
const switchLocalePath = useSwitchLocalePath()

const currentCode = computed(() => locale.value)

const currentLocaleName = computed(() => {
  const l = locales.value.find(l => l.code === currentCode.value)
  return l ? l.name : ''
})

const toggleLanguage = async () => {
  // التبديل بين العربية والإنجليزية
  const newLocale = currentCode.value === 'ar' ? 'en' : 'ar'
  const path = switchLocalePath(newLocale)
  await navigateTo(path)
}
</script>
