<template>
  <div class="container mx-auto px-4 py-8 md:py-12">
    <div class="max-w-5xl mx-auto">
      <!-- العنوان -->
      <div class="text-center mb-8 md:mb-12">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-4">
          التواصل مع انتقالي العاصمة عدن
        </h1>
        <p class="text-lg md:text-xl text-gray-600">
          نستقبل استفساراتكم وطلباتكم للقاءات والتواصل
        </p>
      </div>

      <!-- Alert Messages -->
      <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
        <div class="flex items-center gap-3">
          <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <p class="font-semibold">{{ successMessage }}</p>
        </div>
      </div>

      <div v-if="errorMessage" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
        <div class="flex items-center gap-3">
          <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <div>
            <p class="font-semibold mb-1">{{ errorMessage }}</p>
            <ul v-if="errors && Object.keys(errors).length" class="text-sm list-disc list-inside">
              <li v-for="(error, field) in errors" :key="field">{{ error[0] }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- نموذج التواصل -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-6 md:px-8 md:py-8">
          <h2 class="text-2xl md:text-3xl font-bold text-white">نموذج التواصل</h2>
          <p class="text-blue-100 mt-2">املأ البيانات التالية وسيتم التواصل معك في أقرب وقت</p>
        </div>

        <form @submit.prevent="handleSubmit" class="px-6 py-8 md:px-8 md:py-10">
          <div class="grid md:grid-cols-2 gap-6">
            <!-- الاسم الكامل -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">
                الاسم الكامل <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.full_name" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                placeholder="أدخل اسمك الكامل"
                required
                :disabled="loading"
              >
            </div>

            <!-- البريد الإلكتروني -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">
                البريد الإلكتروني <span class="text-red-500">*</span>
              </label>
              <input 
                type="email" 
                v-model="form.email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                placeholder="example@email.com"
                required
                :disabled="loading"
              >
            </div>

            <!-- رقم الهاتف -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">
                رقم الهاتف <span class="text-red-500">*</span>
              </label>
              <input 
                type="tel" 
                v-model="form.phone" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                placeholder="مثال: 777123456"
                required
                :disabled="loading"
              >
            </div>

            <!-- نوع اللقاء -->
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">
                نوع اللقاء <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.meeting_type" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
                :disabled="loading"
              >
                <option value="">اختر نوع الرسالة</option>
                <option value="contact">تواصل</option>
                <option value="complaint">شكاوي</option>
                <option value="suggestion">مقتراحات</option>
              </select>
            </div>

            <!-- الموضوع -->
            <div class="md:col-span-2">
              <label class="block text-sm font-bold text-gray-700 mb-2">
                الموضوع <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.subject" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                placeholder="عنوان الموضوع"
                required
                :disabled="loading"
              >
            </div>

            <!-- الرسالة -->
            <div class="md:col-span-2">
              <label class="block text-sm font-bold text-gray-700 mb-2">
                الرسالة <span class="text-red-500">*</span>
              </label>
              <textarea 
                v-model="form.message" 
                rows="6" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none" 
                placeholder="اكتب رسالتك هنا... (10 أحرف على الأقل)"
                required
                minlength="10"
                :disabled="loading"
              ></textarea>
              <p class="text-sm text-gray-500 mt-1">{{ form.message.length }} حرف</p>
            </div>
          </div>

          <!-- زر الإرسال -->
          <div class="mt-8">
            <button 
              type="submit" 
              class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-4 rounded-lg transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-3"
              :disabled="loading"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="loading">جاري الإرسال...</span>
              <span v-else>إرسال الرسالة</span>
            </button>
          </div>

          <p class="text-sm text-gray-500 text-center mt-4">
            بإرسال هذا النموذج، أنت توافق على أن يتم التواصل معك من قبل انتقالي العاصمة عدن
          </p>
        </form>
      </div>

      <!-- معلومات إضافية -->
      <div class="mt-12 grid md:grid-cols-3 gap-6">
        <div class="bg-blue-50 p-6 rounded-xl text-center">
          <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-2">وقت الاستجابة</h3>
          <p class="text-gray-600">نرد خلال 24-48 ساعة</p>
        </div>

        <div class="bg-green-50 p-6 rounded-xl text-center">
          <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-2">سرية المعلومات</h3>
          <p class="text-gray-600">معلوماتك محمية بالكامل</p>
        </div>

        <div class="bg-purple-50 p-6 rounded-xl text-center">
          <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-2">دعم مستمر</h3>
          <p class="text-gray-600">فريقنا جاهز لمساعدتك</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const settingsStore = useSettingsStore()
const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

// SEO
watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    useSeoMeta({
      title: 'التواصل مع انتقالي العاصمة عدن',
      description: `تواصل مع المجلس الانتقالي الجنوبي - العاصمة عدن. نستقبل استفساراتكم وطلبات اللقاءات`,
      ogTitle: `التواصل مع انتقالي العاصمة عدن - ${siteName}`,
      ogDescription: `تواصل معنا عبر نموذج التواصل الرسمي`
    })
  }
})

// Form data
const form = ref({
  full_name: '',
  email: '',
  phone: '',
  meeting_type: '',
  subject: '',
  message: ''
})

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref<Record<string, string[]>>({})

// Handle form submission
const handleSubmit = async () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''
  errors.value = {}

  try {
    const response: any = await $fetch(`${apiBase}/contact-messages`, {
      method: 'POST',
      body: form.value
    })

    if (response.success) {
      successMessage.value = response.message || 'تم إرسال رسالتك بنجاح! سيتم التواصل معك قريباً.'
      
      // Reset form
      form.value = {
        full_name: '',
        email: '',
        phone: '',
        meeting_type: '',
        subject: '',
        message: ''
      }

      // Scroll to top to show success message
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  } catch (error: any) {
    console.error('Error submitting contact form:', error)
    
    if (error.data?.errors) {
      errors.value = error.data.errors
      errorMessage.value = error.data.message || 'حدث خطأ في البيانات المدخلة'
    } else {
      errorMessage.value = 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'
    }

    // Scroll to top to show error message
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    loading.value = false
  }
}
</script>
