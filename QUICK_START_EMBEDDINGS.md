# 🚀 البدء السريع - Google Gemini Embeddings

## ✅ ما تم إنجازه حتى الآن

- ✓ تم إنشاء جدول `article_embeddings` في قاعدة البيانات
- ✓ تم إضافة `GEMINI_API_KEY` إلى ملف `.env`
- ✓ جميع الملفات والـ Services جاهزة

---

## 📌 الخطوة الأولى والأهم: الحصول على Google Gemini API Key

### **1️⃣ اذهب إلى Google AI Studio**
```
https://aistudio.google.com
```

### **2️⃣ انقر على "Get API Key"**
- ستجد الزر في الأعلى الأيسر

### **3️⃣ اختر "Create API key in new project"**
- سيتم إنشاء مشروع جديد تلقائياً
- سيتم إنشاء API Key تلقائياً

### **4️⃣ انسخ الـ API Key**
- سيظهر في صفحة جديدة
- انسخه بالكامل

### **5️⃣ أضفه في ملف `.env`**

افتح الملف:
```
c:\xampp\htdocs\newsroom\backend\.env
```

ابحث عن السطر:
```
GEMINI_API_KEY=
```

أضف الـ Key:
```
GEMINI_API_KEY=AIzaSyDxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

## 🧪 اختبر أن كل شيء يعمل

### **1️⃣ امسح الـ Cache**
```bash
php artisan config:clear
```

### **2️⃣ اختبر الـ API Key**
```bash
php artisan tinker
```

ثم اكتب:
```php
$service = app(\App\Services\EmbeddingService::class);
$embedding = $service->generateEmbedding("مرحبا");
echo "✓ API Key يعمل بنجاح!";
```

إذا رأيت `✓ API Key يعمل بنجاح!` فأنت جاهز! 🎉

---

## 📝 توليد Embeddings للمقالات الموجودة

بعد التأكد من أن الـ API Key يعمل:

```bash
# توليد embeddings لجميع المقالات
php artisan embeddings:generate

# ستظهر progress bar تظهر التقدم
```

---

## 🔍 اختبر البحث الذكي

### **استخدم Postman أو curl:**

```bash
# البحث عن "أزمة اقتصادية"
curl "http://localhost/newsroom/backend/public/api/v1/articles/search?q=أزمة%20اقتصادية"
```

**يجب أن يرجع:**
```json
{
  "status": "success",
  "data": [...],
  "total": 5,
  "current_page": 1,
  "last_page": 1,
  "per_page": 12
}
```

---

## 🎯 الخطوات التالية

1. ✅ الحصول على API Key
2. ✅ إضافته في `.env`
3. ✅ توليد embeddings
4. ⏳ تحديث الواجهة الأمامية (اختياري)

---

## ⚠️ المشاكل الشائعة

### **"GEMINI_API_KEY not configured"**
- تأكد من إضافة الـ Key في `.env`
- قم بـ `php artisan config:clear`
- أعد تشغيل الخادم

### **"Rate limit exceeded"**
- الطبقة المجانية محدودة بـ 100 طلب/يوم
- انتظر حتى الغد أو استخدم الطبقة المدفوعة

### **"No embeddings found"**
- تأكد من تشغيل `php artisan embeddings:generate`
- تحقق من السجلات: `storage/logs/laravel.log`

---

## 📚 المراجع

- [Google Gemini API](https://ai.google.dev/gemini-api/docs)
- [Embeddings Documentation](https://ai.google.dev/gemini-api/docs/embeddings)
- [Setup Guide](./GEMINI_EMBEDDINGS_SETUP.md)
- [Next Steps](./EMBEDDINGS_NEXT_STEPS.md)

---

**آخر تحديث:** 12 نوفمبر 2025
