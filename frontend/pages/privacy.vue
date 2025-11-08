<template>
  <div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">سياسة الخصوصية</h1>
      
      <div class="prose prose-lg max-w-none space-y-6">
        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">المقدمة</h2>
          <p class="text-gray-700 leading-relaxed">
            نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية معلوماتك.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">جمع المعلومات</h2>
          <p class="text-gray-700 leading-relaxed mb-3">
            نقوم بجمع المعلومات التالية:
          </p>
          <ul class="list-disc list-inside text-gray-700 space-y-2 mr-4">
            <li>معلومات الاستخدام والتصفح</li>
            <li>عنوان IP والموقع الجغرافي</li>
            <li>معلومات الجهاز والمتصفح</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">استخدام المعلومات</h2>
          <p class="text-gray-700 leading-relaxed">
            نستخدم المعلومات المجمعة لتحسين تجربة المستخدم، تخصيص المحتوى، وتحليل أداء الموقع.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">حماية البيانات</h2>
          <p class="text-gray-700 leading-relaxed">
            نتخذ جميع التدابير الأمنية اللازمة لحماية بياناتك من الوصول غير المصرح به أو الإفصاح أو التعديل أو الإتلاف.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">ملفات تعريف الارتباط (Cookies)</h2>
          <p class="text-gray-700 leading-relaxed">
            نستخدم ملفات تعريف الارتباط لتحسين تجربة التصفح وتقديم محتوى مخصص.
          </p>
        </section>

        <section>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">التحديثات</h2>
          <p class="text-gray-700 leading-relaxed">
            قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. سيتم إشعارك بأي تغييرات جوهرية.
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

const currentDate = new Date().toLocaleDateString('ar-EG', { 
  year: 'numeric', 
  month: 'long', 
  day: 'numeric' 
})

watchEffect(() => {
  const siteName = settingsStore.getSetting('site_name')
  
  if (siteName) {
    useSeoMeta({
      title: 'سياسة الخصوصية',
      description: `اطلع على سياسة الخصوصية في ${siteName} وكيفية حماية بياناتك وخصوصيتك`,
      ogTitle: `سياسة الخصوصية - ${siteName}`,
      ogDescription: `اطلع على سياسة الخصوصية في ${siteName}`
    })
  }
})
</script>
