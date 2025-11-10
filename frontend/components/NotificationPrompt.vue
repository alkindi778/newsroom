<template>
  <div v-if="shouldShow" class="fixed bottom-4 left-4 right-4 md:right-auto md:left-4 md:w-96 z-50 animate-slide-up">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 flex items-center gap-3">
        <div class="bg-white/20 rounded-full p-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </div>
        <div class="flex-1">
          <h3 class="text-white font-bold text-lg">تفعيل الإشعارات</h3>
          <p class="text-blue-100 text-sm">ابق على اطلاع بآخر الأخبار</p>
        </div>
        <button @click="dismiss" class="text-white/80 hover:text-white transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-4">
        <!-- Permission Blocked -->
        <div v-if="isBlocked" class="text-center py-2">
          <p class="text-red-600 dark:text-red-400 text-sm mb-2">
            تم حظر الإشعارات في المتصفح
          </p>
          <p class="text-gray-600 dark:text-gray-400 text-xs">
            يرجى تفعيل الإشعارات من إعدادات المتصفح
          </p>
        </div>

        <!-- Normal State -->
        <div v-else>
          <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
            احصل على إشعارات فورية عند نشر الأخبار العاجلة والمقالات والفيديوهات الجديدة
          </p>

          <!-- Features -->
          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>أخبار عاجلة فورية</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>مقالات حصرية</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span>محتوى فيديو جديد</span>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex gap-2">
            <button 
              @click="handleSubscribe"
              :disabled="state.loading"
              class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
            >
              <svg v-if="state.loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ state.loading ? 'جاري التفعيل...' : 'تفعيل الإشعارات' }}</span>
            </button>
            <button 
              @click="dismiss"
              class="px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
            >
              لاحقاً
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

const { 
  state, 
  subscribe, 
  checkSubscription,
  isBlocked
} = usePushNotifications()

const shouldShow = ref(false)
const dismissed = ref(false)

// التحقق من إمكانية عرض المطالبة
const checkShouldShow = () => {
  if (process.client) {
    // عدم العرض إذا تم رفض الإذن أو تم الاشتراك مسبقاً
    if (state.value.permission === 'denied' || state.value.subscribed) {
      return false
    }

    // عدم العرض إذا تم رفض المطالبة مسبقاً
    const lastDismissed = localStorage.getItem('notification_prompt_dismissed')
    if (lastDismissed) {
      const dismissedTime = parseInt(lastDismissed)
      const daysSinceDismissed = (Date.now() - dismissedTime) / (1000 * 60 * 60 * 24)
      
      // عرض المطالبة مرة أخرى بعد 7 أيام
      if (daysSinceDismissed < 7) {
        return false
      }
    }

    return true
  }
  return false
}

const handleSubscribe = async () => {
  const success = await subscribe()
  if (success) {
    shouldShow.value = false
  }
}

const dismiss = () => {
  shouldShow.value = false
  dismissed.value = true
  
  if (process.client) {
    localStorage.setItem('notification_prompt_dismissed', Date.now().toString())
  }
}

onMounted(async () => {
  // الانتظار قليلاً قبل عرض المطالبة
  setTimeout(async () => {
    await checkSubscription()
    shouldShow.value = checkShouldShow()
  }, 3000)
})
</script>

<style scoped>
@keyframes slide-up {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slide-up 0.4s ease-out;
}
</style>
