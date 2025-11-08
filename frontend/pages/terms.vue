<template>
  <div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">الشروط والأحكام</h1>
      
      <div class="prose prose-lg max-w-none space-y-6">
        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">القبول بالشروط</h2>
          <p class="text-gray-700 leading-relaxed">
            باستخدامك لهذا الموقع، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق على أي جزء من هذه الشروط، يرجى عدم استخدام الموقع.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">حقوق الملكية الفكرية</h2>
          <p class="text-gray-700 leading-relaxed">
            جميع المحتويات المنشورة على هذا الموقع، بما في ذلك النصوص والصور والرسومات، محمية بموجب قوانين حقوق النشر والملكية الفكرية.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">استخدام المحتوى</h2>
          <p class="text-gray-700 leading-relaxed mb-3">
            يُسمح لك بـ:
          </p>
          <ul class="list-disc list-inside text-gray-700 space-y-2 mr-4">
            <li>مشاهدة وقراءة المحتوى للاستخدام الشخصي</li>
            <li>مشاركة الروابط على وسائل التواصل الاجتماعي</li>
            <li>الاقتباس مع ذكر المصدر</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">المحظورات</h2>
          <p class="text-gray-700 leading-relaxed mb-3">
            يُحظر عليك:
          </p>
          <ul class="list-disc list-inside text-gray-700 space-y-2 mr-4">
            <li>نسخ أو إعادة نشر المحتوى دون إذن</li>
            <li>استخدام المحتوى لأغراض تجارية دون ترخيص</li>
            <li>محاولة اختراق أو الإضرار بالموقع</li>
            <li>نشر محتوى مسيء أو غير قانوني</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">إخلاء المسؤولية</h2>
          <p class="text-gray-700 leading-relaxed">
            نسعى جاهدين لتقديم معلومات دقيقة وموثوقة، لكننا لا نضمن خلو المحتوى من الأخطاء. استخدام المعلومات على مسؤوليتك الخاصة.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">التعديلات</h2>
          <p class="text-gray-700 leading-relaxed">
            نحتفظ بالحق في تعديل هذه الشروط والأحكام في أي وقت. التعديلات تصبح سارية فور نشرها على الموقع.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">القانون الحاكم</h2>
          <p class="text-gray-700 leading-relaxed">
            تخضع هذه الشروط والأحكام للقوانين المعمول بها في الجمهورية اليمنية.
          </p>
        </section>

        <p class="text-sm text-gray-500 mt-8">
          آخر تحديث: {{ currentDate }}
        </p>
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
      title: 'الشروط والأحكام',
      description: `اطلع على شروط وأحكام استخدام ${siteName} والقواعد التي تحكم الاستخدام`,
      ogTitle: `الشروط والأحكام - ${siteName}`,
      ogDescription: `شروط وأحكام استخدام ${siteName}`
    })
  }
})

const currentDate = new Date().toLocaleDateString('ar-EG', { 
  year: 'numeric', 
  month: 'long', 
  day: 'numeric' 
})
</script>
