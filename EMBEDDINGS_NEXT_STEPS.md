# خطوات التطبيق التالية - Google Gemini Embeddings

## ✅ ما تم إنجازه

### **المرحلة 1: البنية الأساسية** ✓
- ✓ إنشاء جدول `article_embeddings`
- ✓ إنشاء Model `ArticleEmbedding`
- ✓ تحديث Article Model بالعلاقة
- ✓ إنشاء `EmbeddingService`
- ✓ إنشاء `VectorSearchService`
- ✓ إضافة Gemini API Key إلى config

### **المرحلة 2: تحديث النظام الحالي** ✓
- ✓ تحديث `ArticleService` لتوليد embeddings
- ✓ تحديث `SearchController` للبحث الذكي
- ✓ إضافة endpoints جديدة للمقالات المشابهة والمحتوى المكرر
- ✓ إنشاء Command لمعالجة المقالات الموجودة

---

## 🔧 الخطوات التالية المطلوبة

### **1. الحصول على Google Gemini API Key** (مهم جداً)

**الخطوات:**
1. اذهب إلى https://aistudio.google.com
2. انقر على **"Get API Key"** في الأعلى
3. اختر **"Create API key in new project"**
4. سيتم إنشاء API Key تلقائياً
5. انسخ الـ Key

**إضافة الـ Key إلى .env:**
```bash
# في ملف .env
GEMINI_API_KEY=your_api_key_here_xxxxxxxxxx
```

---

### **2. تشغيل Migration**

```bash
# انتقل إلى مجلد backend
cd c:\xampp\htdocs\newsroom\backend

# تشغيل Migration
php artisan migrate

# يجب أن ترى رسالة:
# Migration table created successfully.
# Migrating: 2025_11_12_000000_create_article_embeddings_table
# Migrated: 2025_11_12_000000_create_article_embeddings_table
```

---

### **3. توليد Embeddings للمقالات الموجودة**

```bash
# توليد embeddings لجميع المقالات بدون embeddings
php artisan embeddings:generate

# هذا قد يستغرق وقتاً اعتماداً على عدد المقالات
# سترى progress bar يظهر التقدم
```

**ملاحظات:**
- الطبقة المجانية محدودة بـ 100 طلب/يوم
- إذا كان لديك أكثر من 100 مقال، قسّم التوليد على عدة أيام
- أو استخدم الطبقة المدفوعة من Google

---

### **4. اختبار النظام**

#### **اختبار البحث الذكي:**

```bash
# استخدم Postman أو curl

# البحث عن "أزمة اقتصادية"
GET http://localhost:8000/api/v1/articles/search?q=أزمة%20اقتصادية

# يجب أن يرجع مقالات تحتوي على:
# - "أزمة اقتصادية"
# - "ركود مالي"
# - "انهيار اقتصادي"
```

#### **اختبار المقالات المشابهة:**

```bash
# احصل على مقالات مشابهة للمقال رقم 1
GET http://localhost:8000/api/v1/articles/1/similar?limit=5

# يجب أن يرجع 5 مقالات مشابهة
```

#### **اختبار فحص المحتوى المكرر:**

```bash
# فحص ما إذا كان المقال رقم 1 مكرراً
GET http://localhost:8000/api/v1/articles/1/check-duplicates?threshold=0.95

# يجب أن يرجع:
# {
#   "status": "success",
#   "data": [...],
#   "has_duplicates": false
# }
```

---

### **5. تحديث الواجهة الأمامية (Frontend)**

#### **إضافة اقتراحات المقالات المشابهة في صفحة المقال:**

في `frontend/pages/news/[slug].vue`:

```vue
<template>
  <!-- محتوى المقال الحالي -->
  
  <!-- المقالات المشابهة -->
  <section v-if="similarArticles.length > 0" class="mt-12 py-8">
    <h2 class="text-2xl font-bold mb-6">مقالات ذات صلة</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <NewsCard 
        v-for="article in similarArticles"
        :key="article.id"
        :article="article"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
const { apiFetch } = useApi()
const similarArticles = ref([])

// جلب المقالات المشابهة
const fetchSimilarArticles = async (articleId) => {
  try {
    const response = await apiFetch(`/articles/${articleId}/similar?limit=3`)
    if (response?.data) {
      similarArticles.value = response.data
    }
  } catch (error) {
    console.error('Error fetching similar articles:', error)
  }
}

onMounted(() => {
  fetchSimilarArticles(route.params.id)
})
</script>
```

---

### **6. تحسين صفحة البحث**

تحديث `frontend/pages/search.vue` لاستخدام البحث الذكي:

```vue
<!-- البحث الحالي يعمل بالفعل! -->
<!-- لكن يمكن إضافة مؤشر يوضح أن البحث ذكي -->

<div class="mb-4 p-3 bg-blue-50 rounded">
  <p class="text-sm text-blue-700">
    🤖 البحث الذكي: يجد المقالات بالمعنى وليس فقط بالكلمات المفتاحية
  </p>
</div>
```

---

### **7. إضافة نظام الإشعارات عند اكتشاف محتوى مكرر**

في `backend/app/Http/Controllers/Admin/ArticleController.php`:

```php
public function store(Request $request)
{
    // ... الكود الموجود ...
    
    $article = $this->articleService->createArticle($data, $request);
    
    // فحص المحتوى المكرر
    $duplicates = $this->vectorSearchService->findDuplicates($article, 0.95);
    
    if ($duplicates->count() > 0) {
        // إرسال تحذير للمحرر
        \Notification::send(
            auth()->user(),
            new DuplicateContentWarning($article, $duplicates)
        );
    }
    
    return $article;
}
```

---

### **8. مراقبة الأداء والتحسينات**

#### **تتبع استخدام API:**

```bash
# عرض السجلات
tail -f storage/logs/laravel.log | grep -i embedding

# عد عدد الطلبات اليومية
grep -c "embedding" storage/logs/laravel.log
```

#### **تحسينات مستقبلية:**

- [ ] استخدام Redis للـ caching
- [ ] استخدام Vector Database (Pinecone, Weaviate)
- [ ] دعم البحث متعدد اللغات
- [ ] تحسين الأداء باستخدام Batch Processing

---

## 📊 الجدول الزمني المقترح

| المرحلة | المدة | الأولوية |
|--------|------|---------|
| الحصول على API Key | 5 دقائق | 🔴 عالية جداً |
| تشغيل Migration | 2 دقيقة | 🔴 عالية جداً |
| توليد Embeddings | 10-30 دقيقة | 🔴 عالية |
| الاختبار | 15 دقيقة | 🟡 متوسطة |
| تحديث Frontend | 30 دقيقة | 🟡 متوسطة |
| التحسينات الإضافية | يومين | 🟢 منخفضة |

---

## 🚨 المشاكل المحتملة والحلول

### **المشكلة: "GEMINI_API_KEY not configured"**
**الحل:**
1. تأكد من إضافة الـ Key في `.env`
2. قم بـ `php artisan config:clear`
3. أعد تشغيل الخادم

### **المشكلة: "Rate limit exceeded"**
**الحل:**
1. انتظر حتى الغد (الطبقة المجانية 100 طلب/يوم)
2. استخدم الطبقة المدفوعة
3. قسّم توليد الـ embeddings على عدة أيام

### **المشكلة: "No embeddings found"**
**الحل:**
1. تأكد من تشغيل Migration
2. تأكد من توليد الـ embeddings بـ `php artisan embeddings:generate`
3. تحقق من السجلات في `storage/logs/laravel.log`

---

## 📞 التواصل والدعم

إذا واجهت أي مشاكل:

1. تحقق من السجلات: `storage/logs/laravel.log`
2. تحقق من الـ API Key في Google Console
3. تأكد من الاتصال بالإنترنت
4. تحقق من حالة Google API

---

## 📚 المراجع

- [Google Gemini API Documentation](https://ai.google.dev/gemini-api/docs)
- [Embeddings Guide](https://ai.google.dev/gemini-api/docs/embeddings)
- [Laravel Documentation](https://laravel.com/docs)

---

**آخر تحديث:** 12 نوفمبر 2025
