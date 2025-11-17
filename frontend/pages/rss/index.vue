<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8 flex items-center gap-4">
      <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl shadow-lg flex items-center justify-center">
        <span class="text-2xl font-black text-white">RSS</span>
      </div>
      <div>
        <h1 class="text-4xl font-bold text-gray-900 mb-1">تغذيات RSS</h1>
        <p class="text-gray-600">تابع آخر الأخبار عبر تغذيات RSS الرسمية للموقع.</p>
      </div>
    </div>

    <!-- Default feed actions & stats -->
    <div class="mb-10 grid md:grid-cols-2 gap-6">
      <!-- Default feed card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4">
        <div>
          <h2 class="text-lg font-semibold text-gray-900 mb-1">التغذية الافتراضية</h2>
          <p class="text-sm text-gray-600">
            يمكنك البدء بالتغذية العامة التي تحتوي على أحدث الأخبار من مختلف الأقسام.
          </p>
        </div>
        <div class="flex flex-wrap gap-3">
          <a
            v-if="meta.default_feed"
            :href="meta.default_feed"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-600 to-blue-600 font-semibold shadow hover:shadow-lg transition"
          >
            فتح التغذية
          </a>
          <button
            v-if="meta.default_feed"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
            @click="copyLink(meta.default_feed)"
          >
            نسخ الرابط
          </button>
        </div>
      </div>

      <!-- Stats & advantages card -->
      <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-6 flex flex-col justify-between">
        <div>
          <p class="text-sm text-indigo-700 mb-1">عدد التغذيات المتاحة</p>
          <p class="text-3xl font-black text-indigo-900">{{ feeds.length }}</p>
        </div>
        <ul class="mt-4 space-y-2 text-sm text-indigo-800">
          <li>تحديث تلقائي لأحدث المقالات المنشورة.</li>
          <li>متوافقة مع أغلب قارئات الأخبار والتطبيقات.</li>
          <li>روابط موثوقة وآمنة للاستخدام اليومي.</li>
        </ul>
      </div>
    </div>

    <!-- Feeds List -->
    <section>
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-3xl font-bold text-gray-900">تغذيات متاحة</h2>
          <p class="text-gray-500 mt-1">يمكنك الاشتراك في أكثر من تغذية واحدة لاتباع الأقسام التي تهمك.</p>
        </div>
        <div class="flex gap-2 text-sm text-gray-600">
          <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
            العربية بشكل افتراضي
          </span>
          <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
            يتم تحديثها حسب إعداد كل تغذية
          </span>
        </div>
      </div>

      <div v-if="loading" class="grid md:grid-cols-2 gap-6">
        <div v-for="i in 4" :key="i" class="animate-pulse p-6 bg-white rounded-2xl border">
          <div class="h-6 w-32 bg-gray-200 rounded mb-4"></div>
          <div class="h-4 w-full bg-gray-100 rounded mb-2"></div>
          <div class="h-4 w-2/3 bg-gray-100 rounded"></div>
          <div class="mt-4 h-6 w-20 bg-gray-100 rounded"></div>
        </div>
      </div>

      <div v-else-if="feeds.length" class="grid gap-6 lg:grid-cols-2">
        <article
          v-for="feed in feeds"
          :key="feed.id"
          class="p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition bg-white flex flex-col gap-4"
        >
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ feed.title }}</h3>
              <p class="text-gray-600 leading-relaxed">{{ feed.description || 'تغذية إخبارية تلقائية' }}</p>
            </div>
            <span
              class="px-3 py-1 text-xs font-semibold rounded-full"
              :class="feed.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500'"
            >
              {{ feed.is_active ? 'نشطة' : 'متوقفة' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-2">
              <span class="font-semibold text-amber-600">القسم:</span>
              <span>{{ feed.category || 'عام' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-indigo-600">اللغة:</span>
              <span>{{ feed.language?.toUpperCase() }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-sky-600">عدد العناصر:</span>
              <span>{{ feed.items_count }} عناصر</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-semibold text-rose-600">TTL:</span>
              <span>{{ feed.ttl }} دقيقة</span>
            </div>
          </div>

          <div class="flex flex-wrap gap-3 mt-2">
            <a
              :href="feed.url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-600 to-blue-600 font-semibold shadow hover:shadow-lg transition"
            >
              فتح التغذية
            </a>
            <button
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
              @click="copyLink(feed.url)"
            >
              نسخ الرابط
            </button>
            <NuxtLink
              v-if="feed.category_slug"
              :to="`/categories/${feed.category_slug}`"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
            >
              تصفح القسم
            </NuxtLink>
          </div>
        </article>
      </div>

      <div v-else class="text-center py-16 bg-white border rounded-3xl">
        <div class="w-16 h-16 rounded-full bg-gray-100 mx-auto mb-4 flex items-center justify-center text-gray-400 text-xl font-bold">
          RSS
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">لا توجد تغذيات متاحة حالياً</h3>
        <p class="text-gray-600">سيتم تحديث هذه الصفحة فور إضافة تغذيات RSS جديدة.</p>
      </div>
    </section>

    <!-- Instructions -->
    <section class="mt-16">
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="col-span-2 bg-slate-950 text-white rounded-3xl p-8 shadow-2xl">
          <h3 class="text-2xl font-bold mb-6">كيفية استخدام تغذيات RSS</h3>
          <div class="grid md:grid-cols-2 gap-5 text-sm">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <h4 class="font-semibold mb-1">قارئات الأخبار</h4>
              <p class="text-sm text-white/80">
                انسخ رابط التغذية وضعه في تطبيقك المفضل مثل Feedly أو Inoreader لمتابعة التحديثات فور نشرها.
              </p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <h4 class="font-semibold mb-1">التكاملات التقنية</h4>
              <p class="text-sm text-white/80">
                يمكن للمطورين استخدام روابط RSS لاستيراد الأخبار إلى المواقع أو التطبيقات عبر جداول زمنية مخصصة.
              </p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <h4 class="font-semibold mb-1">الإشعارات والتنبيهات</h4>
              <p class="text-sm text-white/80">
                اربط تغذية RSS مع أدوات الأتمتة مثل Zapier أو IFTTT لإرسال إشعارات إلى البريد أو Slack.
              </p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
              <h4 class="font-semibold mb-1">مشاركة الأقسام</h4>
              <p class="text-sm text-white/80">
                شارك روابط التغذيات الخاصة بالأقسام مع فريق التحرير أو الشركاء لاستلام أحدث التحديثات.
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-3xl border shadow-lg p-6 space-y-4">
          <h4 class="text-lg font-bold">نصائح سريعة</h4>
          <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex gap-2">
              <span class="text-blue-500 font-semibold">•</span>
              استخدم HTTPS دائماً لضمان أمان الاتصال بالتغذية.
            </li>
            <li class="flex gap-2">
              <span class="text-blue-500 font-semibold">•</span>
              قد تختلف سرعة التحديث حسب قيمة TTL لكل تغذية.
            </li>
            <li class="flex gap-2">
              <span class="text-blue-500 font-semibold">•</span>
              احتفظ بنسخة من الروابط في أدوات التنظيم الخاصة بفريقك.
            </li>
          </ul>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const feeds = ref<any[]>([])
const meta = ref<Record<string, any>>({})
const loading = ref(false)
const error = ref<string | null>(null)

const { apiFetch } = useApi()

const fetchFeeds = async () => {
  loading.value = true
  error.value = null

  const response = await apiFetch<any>('/rss-feeds')

  if (response?.success) {
    feeds.value = response.data || []
    meta.value = response.meta || {}
  } else {
    error.value = 'تعذر تحميل تغذيات RSS في الوقت الحالي'
  }

  loading.value = false
}

const copyLink = async (link: string) => {
  try {
    await navigator.clipboard.writeText(link)
    if (process.client) {
      alert('تم نسخ رابط التغذية')
    }
  } catch (err) {
    console.warn('Clipboard error:', err)
    if (process.client) {
      alert('تعذر نسخ الرابط')
    }
  }
}

onMounted(fetchFeeds)

useSeoMeta({
  title: 'تغذيات RSS - أخبار لحظية',
  description: 'اختر من بين مجموعة تغذيات RSS الرسمية لموقعنا وادمجها في قارئ الأخبار أو تطبيقاتك بسهولة.'
})
</script>
