<template>
  <div class="relative" v-click-outside="closeDropdown">
    <button 
      @click="toggleDropdown" 
      class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors rounded-lg hover:bg-gray-50"
      :aria-label="$t('common.language')"
    >
      <span class="text-lg">{{ currentFlag }}</span>
      <span class="hidden sm:inline">{{ currentLocaleName }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown -->
    <div 
      v-if="isOpen" 
      class="absolute top-full mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 transform origin-top-right ltr:right-0 rtl:left-0"
    >
      <button
        v-for="localeItem in availableLocales"
        :key="localeItem.code"
        @click="changeLocale(localeItem.code)"
        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors text-start"
        :class="{ 'bg-primary/5 text-primary font-semibold': localeItem.code === currentCode }"
      >
        <span class="text-lg">{{ getFlag(localeItem.code) }}</span>
        <span>{{ localeItem.name }}</span>
      </button>
    </div>
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

const getFlag = (code) => {
  return code === 'ar' ? '🇾🇪' : '🇺🇸'
}

const currentFlag = computed(() => getFlag(currentCode.value))

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
