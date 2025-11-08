<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="text-center">
      <!-- Spinner Type: Dots -->
      <div v-if="type === 'dots'" class="flex justify-center gap-2 mb-4">
        <div 
          v-for="i in 3" 
          :key="i"
          class="rounded-full animate-bounce"
          :class="[dotSizeClass, dotColorClass]"
          :style="{ animationDelay: `${i * 0.15}s` }"
        ></div>
      </div>

      <!-- Spinner Type: Pulse -->
      <div v-else-if="type === 'pulse'" class="flex justify-center mb-4">
        <div class="relative" :class="sizeClass">
          <div 
            class="absolute inset-0 rounded-full animate-ping opacity-75"
            :class="pulseColorClass"
          ></div>
          <div 
            class="relative rounded-full"
            :class="[sizeClass, pulseColorClass]"
          ></div>
        </div>
      </div>

      <!-- Spinner Type: Bars -->
      <div v-else-if="type === 'bars'" class="flex justify-center gap-1.5 items-end h-12 mb-4">
        <div 
          v-for="i in 5" 
          :key="i"
          class="w-2 rounded-full bar-animation"
          :class="barColorClass"
          :style="{ 
            animationDelay: `${i * 0.12}s`
          }"
        ></div>
      </div>

      <!-- Spinner Type: Circle (Enhanced) -->
      <div v-else class="flex justify-center mb-4">
        <div class="relative" :class="sizeClass">
          <div class="absolute inset-0 rounded-full border-3 border-gray-200"></div>
          <div 
            class="absolute inset-0 rounded-full border-3 border-t-transparent animate-spin"
            :class="colorClass"
            style="animation-duration: 0.8s;"
          ></div>
        </div>
      </div>

      <!-- Text -->
      <div v-if="text" class="animate-pulse">
        <p class="text-base font-semibold" :class="textColorClass">
          {{ text }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  size?: 'sm' | 'md' | 'lg'
  color?: 'primary' | 'secondary' | 'white'
  type?: 'circle' | 'dots' | 'pulse' | 'bars'
  text?: string
  fullScreen?: boolean
}>(), {
  size: 'md',
  color: 'primary',
  type: 'dots',
  fullScreen: false
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-10 h-10'
    case 'lg': return 'w-20 h-20'
    default: return 'w-16 h-16'
  }
})

const dotSizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-2.5 h-2.5'
    case 'lg': return 'w-5 h-5'
    default: return 'w-4 h-4'
  }
})

const colorClass = computed(() => {
  switch (props.color) {
    case 'secondary': return 'border-gold'
    case 'white': return 'border-white'
    default: return 'border-primary'
  }
})

const dotColorClass = computed(() => {
  switch (props.color) {
    case 'secondary': return 'bg-gold'
    case 'white': return 'bg-white'
    default: return 'bg-primary'
  }
})

const pulseColorClass = computed(() => {
  switch (props.color) {
    case 'secondary': return 'bg-gold'
    case 'white': return 'bg-white'
    default: return 'bg-primary'
  }
})

const barColorClass = computed(() => {
  switch (props.color) {
    case 'secondary': return 'bg-gold'
    case 'white': return 'bg-white'
    default: return 'bg-primary'
  }
})

const textColorClass = computed(() => {
  switch (props.color) {
    case 'secondary': return 'text-gold'
    case 'white': return 'text-white'
    default: return 'text-navy-700'
  }
})

const containerClass = computed(() => {
  return props.fullScreen ? 'min-h-screen' : 'py-12'
})
</script>

<style scoped>
/* Smooth bounce animation for dots */
@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0) translateY(0);
    opacity: 0.3;
  }
  40% {
    transform: scale(1) translateY(-10px);
    opacity: 1;
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite ease-in-out;
}

/* Bar animation */
@keyframes bar-scale {
  0%, 100% {
    transform: scaleY(0.4);
    opacity: 0.6;
  }
  50% {
    transform: scaleY(1);
    opacity: 1;
  }
}

.bar-animation {
  height: 48px;
  transform-origin: bottom;
  animation: bar-scale 1s infinite ease-in-out;
}

/* Enhanced border width */
.border-3 {
  border-width: 3px;
}

/* Smooth spin */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
