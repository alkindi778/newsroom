<template>
  <div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">اتصل بنا</h1>
      
      <div class="grid md:grid-cols-2 gap-8">
        <!-- معلومات الاتصال -->
        <div class="bg-gray-50 p-6 rounded-lg">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">معلومات التواصل</h2>
          
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <div>
                <h3 class="font-semibold text-gray-900">البريد الإلكتروني</h3>
                <p class="text-gray-600">info@newsroom.com</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <div>
                <h3 class="font-semibold text-gray-900">الهاتف</h3>
                <p class="text-gray-600" dir="ltr">+967 123 456 789</p>
              </div>
            </div>
          </div>
        </div>

        <!-- نموذج الاتصال -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">أرسل لنا رسالة</h2>
          
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
              <input type="text" v-model="form.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
              <input type="email" v-model="form.email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة</label>
              <textarea v-model="form.message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
              إرسال الرسالة
            </button>
          </form>
        </div>
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
      title: 'اتصل بنا',
      description: `تواصل معنا في ${siteName} عبر البريد الإلكتروني أو الهاتف - نحن هنا للإجابة على استفساراتك`,
      ogTitle: `اتصل بنا - ${siteName}`,
      ogDescription: `تواصل معنا في ${siteName}`
    })
  }
})

const form = ref({
  name: '',
  email: '',
  message: ''
})

const handleSubmit = () => {
  alert('شكراً لتواصلك معنا! سنرد عليك قريباً.')
  form.value = { name: '', email: '', message: '' }
}
</script>
