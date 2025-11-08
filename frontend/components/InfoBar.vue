<template>
  <div class="w-full">
    <div class="bg-navy-50 border-y border-navy-100">
      <div class="container mx-auto px-4 md:px-8 lg:px-12">
        <div class="flex items-center justify-between py-4 text-base overflow-x-auto">
        <!-- حالة الطقس -->
        <div class="flex items-center gap-2 min-w-fit">
          <svg class="w-10 h-10 text-gold" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
          </svg>
          <div class="text-right">
            <div class="font-bold text-navy-900 text-base">حالة الطقس (عدن)</div>
            <div class="text-navy-700 text-base">32°C, 89°F</div>
          </div>
        </div>

        <!-- الوقت -->
        <div class="flex items-center gap-2 min-w-fit">
          <svg class="w-10 h-10 text-navy" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/>
          </svg>
          <div class="text-right">
            <div class="font-bold text-navy-900 text-base">الوقت</div>
            <div class="text-navy-700 text-base" dir="ltr">{{ currentTime }}</div>
          </div>
        </div>

        <!-- سعر الصرف -->
        <div class="flex items-center gap-2 min-w-fit">
          <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 01-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004zM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 01-.921.42z"/>
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z" clip-rule="evenodd"/>
          </svg>
          <div class="text-right">
            <div class="font-bold text-navy-900 text-base">سعر الصرف</div>
            <div class="text-navy-700 text-base" dir="ltr">1 USD = 1,580 YER</div>
          </div>
        </div>

        <!-- التعداد السكاني -->
        <div class="flex items-center gap-2 min-w-fit">
          <svg class="w-10 h-10 text-gold-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z"/>
          </svg>
          <div class="text-right">
            <div class="font-bold text-navy-900 text-base">التعداد السكاني 2022</div>
            <div class="text-navy-700 text-base">33.7 مليون</div>
          </div>
        </div>

        <!-- أهم المدن -->
        <div class="flex items-center gap-2 min-w-fit">
          <svg class="w-10 h-10 text-accent" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
          </svg>
          <div class="text-right">
            <div class="font-bold text-navy-900 text-base">أهم المدن</div>
            <div class="text-navy-700 text-base">عدن، حضرموت، سقطرى</div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const currentTime = ref('')

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('ar-SA', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  })
}

let interval: NodeJS.Timeout

onMounted(() => {
  updateTime()
  interval = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (interval) {
    clearInterval(interval)
  }
})
</script>

<style scoped>
/* تأكد من أن الـ scroll يكون سلس على الموبايل */
.overflow-x-auto {
  scrollbar-width: thin;
  scrollbar-color: #D4AF37 transparent;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #D4AF37;
  border-radius: 2px;
}
</style>
