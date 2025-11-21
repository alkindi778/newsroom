<template>
  <div class="relative" v-click-outside="closeDropdown">
    <button 
      @click="toggleDropdown" 
      class="flex items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-primary transition-colors rounded-lg hover:bg-gray-50 active:bg-gray-100 touch-manipulation"
      :aria-label="$t('common.language')"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
      </svg>
      <span class="inline truncate max-w-[60px] sm:max-w-none">{{ currentLocaleName }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div 
        v-if="isOpen" 
        class="absolute top-full mt-2 min-w-[140px] bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-[100] transform origin-top ltr:right-0 rtl:left-0"
      >
        <button
          v-for="localeItem in availableLocales"
          :key="localeItem.code"
          @click="changeLocale(localeItem.code)"
          class="w-full flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors text-start active:bg-gray-100 touch-manipulation"
          :class="{ 'bg-primary/5 text-primary font-semibold': localeItem.code === currentCode }"
        >
          <span class="font-medium">{{ localeItem.name }}</span>
          <svg v-if="localeItem.code === currentCode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
const { locale, locales } = useI18n()
const switchLocalePath = useSwitchLocalePath()
const isOpen = ref(false)

const currentCode = computed(() => locale.value)

const availableLocales = computed(() => {
  return locales.value
})

const currentLocaleName = computed(() => {
  const l = locales.value.find(l => l.code === currentCode.value)
  return l ? l.name : ''
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const changeLocale = async (code) => {
  closeDropdown()
  if (code === currentCode.value) return
  
  const path = switchLocalePath(code)
  await navigateTo(path)
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event, el)
      }
    }
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>
