<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 py-16 px-4" dir="rtl">
      <div class="max-w-2xl w-full text-center">
        <!-- Error Icon -->
        <div class="mb-8">
          <div 
            class="inline-flex items-center justify-center w-24 h-24 rounded-full mx-auto"
            :class="errorIconBgClass"
          >
            <!-- 404 Icon -->
            <svg 
              v-if="error.statusCode === 404" 
              class="w-12 h-12"
              :class="errorIconColorClass"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            
            <!-- 403 Forbidden Icon -->
            <svg 
              v-else-if="error.statusCode === 403" 
              class="w-12 h-12"
              :class="errorIconColorClass"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            
            <!-- 500 Server Error Icon -->
            <svg 
              v-else-if="error.statusCode === 500" 
              class="w-12 h-12"
              :class="errorIconColorClass"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            
            <!-- Generic Error Icon -->
            <svg 
              v-else 
              class="w-12 h-12"
              :class="errorIconColorClass"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
        </div>

        <!-- Error Code -->
        <div class="mb-4">
          <h1 class="text-6xl md:text-7xl font-bold text-gray-900 mb-2">
            {{ error.statusCode || 'خطأ' }}
          </h1>
          <p class="text-xl md:text-2xl font-semibold text-gray-700">
            {{ errorTitle }}
          </p>
        </div>

        <!-- Error Message -->
        <p class="text-gray-600 mb-8 text-base md:text-lg leading-relaxed">
          {{ errorMessage }}
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <NuxtLink 
            to="/" 
            class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-600 text-white px-8 py-3 rounded-lg font-semibold transition-all transform hover:scale-105"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            الصفحة الرئيسية
          </NuxtLink>
          
          <button 
            @click="goBack"
            class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-300 px-8 py-3 rounded-lg font-semibold transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            العودة للخلف
          </button>
        </div>

        <!-- Additional Help Links -->
        <div class="mt-12 pt-8 border-t border-gray-200">
          <p class="text-sm text-gray-500 mb-4">هل تحتاج مساعدة؟</p>
          <div class="flex flex-wrap gap-4 justify-center text-sm">
            <NuxtLink to="/about" class="text-primary hover:text-primary-600 font-semibold transition-colors">
              من نحن
            </NuxtLink>
            <span class="text-gray-400">•</span>
            <NuxtLink to="/contact" class="text-primary hover:text-primary-600 font-semibold transition-colors">
              اتصل بنا
            </NuxtLink>
            <span class="text-gray-400">•</span>
            <button @click="reportError" class="text-primary hover:text-primary-600 font-semibold transition-colors">
              الإبلاغ عن مشكلة
            </button>
          </div>
        </div>
      </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  error: {
    type: Object,
    required: true
  }
})

const router = useRouter()

// رسائل الخطأ المخصصة
const errorTitle = computed(() => {
  switch (props.error.statusCode) {
    case 404:
      return 'الصفحة غير موجودة'
    case 403:
      return 'الوصول محظور'
    case 500:
      return 'خطأ في الخادم'
    case 503:
      return 'الخدمة غير متاحة مؤقتاً'
    default:
      return 'حدث خطأ ما'
  }
})

const errorMessage = computed(() => {
  switch (props.error.statusCode) {
    case 404:
      return 'عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى عنوان آخر. يمكنك العودة للصفحة الرئيسية أو البحث عن محتوى آخر.'
    case 403:
      return 'عذراً، ليس لديك صلاحية الوصول إلى هذه الصفحة. إذا كنت تعتقد أن هذا خطأ، يرجى التواصل معنا.'
    case 500:
      return 'عذراً، حدث خطأ غير متوقع في الخادم. نحن نعمل على حل المشكلة. يرجى المحاولة مرة أخرى لاحقاً.'
    case 503:
      return 'الموقع غير متاح مؤقتاً بسبب أعمال الصيانة أو الضغط الزائد. يرجى المحاولة مرة أخرى بعد قليل.'
    default:
      return props.error.message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى أو الاتصال بالدعم الفني.'
  }
})

// تحديد ألوان الأيقونات بناءً على نوع الخطأ
const errorIconBgClass = computed(() => {
  switch (props.error.statusCode) {
    case 404:
      return 'bg-primary-50'
    case 403:
      return 'bg-accent-50'
    case 500:
      return 'bg-red-50'
    default:
      return 'bg-gray-100'
  }
})

const errorIconColorClass = computed(() => {
  switch (props.error.statusCode) {
    case 404:
      return 'text-primary'
    case 403:
      return 'text-accent'
    case 500:
      return 'text-red-500'
    default:
      return 'text-gray-600'
  }
})

// الدوال
const goBack = () => {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/')
  }
}

const reportError = () => {
  // يمكن تحسين هذه الدالة لاحقاً لإرسال تقرير للخادم
  alert('شكراً لإبلاغنا! سنقوم بمراجعة المشكلة في أقرب وقت.')
  router.push('/contact')
}

// SEO
useHead({
  title: `خطأ ${props.error.statusCode || ''} - ${errorTitle.value}`,
  meta: [
    { name: 'robots', content: 'noindex, nofollow' }
  ]
})

// تسجيل الخطأ (اختياري)
if (process.client) {
  console.error('Error page:', props.error)
}
</script>

<style scoped>
/* تحسينات إضافية للأنيميشن */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

main > div {
  animation: fadeIn 0.5s ease-out;
}

/* تأثير Hover محسّن للأزرار */
button, a {
  position: relative;
  overflow: hidden;
}

button::before, 
a.inline-flex::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

button:hover::before,
a.inline-flex:hover::before {
  width: 300px;
  height: 300px;
}
</style>
