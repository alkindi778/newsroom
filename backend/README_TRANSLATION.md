# 🌐 نظام الترجمة التلقائية - Automatic Translation System

<div dir="rtl">

## 📖 نظرة عامة

نظام ترجمة تلقائي احترافي ومتكامل يترجم محتوى المقالات من العربية إلى الإنجليزية باستخدام **Google Gemini 2.0 Flash API**. 

### ✨ المميزات

- ✅ **ترجمة تلقائية**: تتم فور إنشاء المقال دون تدخل يدوي
- ⚡ **معالجة خلفية**: لا تؤثر على سرعة الموقع
- 🔄 **إعادة محاولة ذكية**: 3 محاولات مع فترات انتظار متدرجة
- 🎯 **حفظ HTML**: يحافظ على جميع أكواد HTML والتنسيق
- 📊 **تسجيل شامل**: تتبع كامل لجميع العمليات
- ⚙️ **قابل للتخصيص**: إعدادات مرنة عبر ملف config
- 🧪 **قابل للاختبار**: Test Suite كامل مرفق

---

## 📦 المكونات

### ملفات PHP الأساسية

| الملف | الوصف |
|-------|-------|
| `app/Services/GeminiTranslationService.php` | خدمة الاتصال بـ Gemini API |
| `app/Jobs/TranslateContentJob.php` | Job للمعالجة الخلفية |
| `app/Observers/ArticleObserver.php` | مراقب أحداث المقالات |
| `app/Console/Commands/TranslateExistingArticles.php` | أمر CLI للترجمة الجماعية |

### ملفات التكوين

| الملف | الوصف |
|-------|-------|
| `config/translation.php` | ⭐ تكوينات النظام الكاملة |
| `config/services.php` | إعدادات Gemini API |
| `.env` | متغيرات البيئة |

### قاعدة البيانات

| الملف | الوصف |
|-------|-------|
| `database/migrations/2025_11_20_000000_add_english_translation_columns_to_articles_table.php` | Migration لإضافة الأعمدة |

### الاختبارات

| الملف | الوصف |
|-------|-------|
| `tests/Feature/TranslationSystemTest.php` | اختبارات شاملة للنظام |
| `test_translation.php` | ⭐ سكريبت اختبار سريع |

### التوثيق

| الملف | الوصف |
|-------|-------|
| `TRANSLATION_SETUP.md` | ⭐ دليل البدء السريع |
| `TRANSLATION_SYSTEM.md` | الوثائق الكاملة |
| `TRANSLATION_FLOW.md` | مخططات تدفق النظام |
| `IMPLEMENTATION_SUMMARY.md` | ملخص التنفيذ |

---

## 🚀 البدء السريع

### 1️⃣ الحصول على API Key

```
1. افتح: https://makersuite.google.com/app/apikey
2. سجل الدخول بحساب Google
3. اضغط "Create API Key"
4. انسخ المفتاح
```

### 2️⃣ تكوين .env

أضف في ملف `.env`:

```env
# إعدادات Gemini API (مطلوبة)
GEMINI_API_KEY=your-api-key-here

# إعدادات اختيارية
GEMINI_MODEL=gemini-2.0-flash-exp
TRANSLATION_ENABLED=true
TRANSLATION_AUTO_ON_CREATE=true
TRANSLATION_AUTO_ON_UPDATE=true
```

### 3️⃣ تشغيل Migration

```bash
cd backend
php artisan migrate
```

### 4️⃣ اختبار النظام

```bash
php test_translation.php
```

إذا كانت جميع الاختبارات ✅، فالنظام جاهز!

### 5️⃣ تشغيل Queue Worker

```bash
php artisan queue:work
```

**⚠️ مهم**: اترك هذا الأمر يعمل في terminal منفصل

---

## 📚 الاستخدام

### إنشاء مقال جديد

```php
$article = Article::create([
    'title' => 'عنوان الخبر',
    'content' => '<p>محتوى الخبر</p>',
    'category_id' => 1,
    'user_id' => auth()->id(),
]);

// ✅ الترجمة ستحدث تلقائياً في الخلفية!
```

### الوصول للترجمة

```php
$article = Article::find($id);

echo $article->title;      // العنوان العربي
echo $article->title_en;   // العنوان الإنجليزي

echo $article->content;    // المحتوى العربي  
echo $article->content_en; // المحتوى الإنجليزي
```

### ترجمة المقالات الموجودة

```bash
# ترجمة جميع المقالات غير المترجمة
php artisan articles:translate

# ترجمة 10 مقالات فقط
php artisan articles:translate --limit=10

# إعادة ترجمة المقالات المترجمة أيضاً
php artisan articles:translate --force
```

### ترجمة يدوية

```php
use App\Jobs\TranslateContentJob;

$article = Article::find($article_id);
TranslateContentJob::dispatch($article);
```

---

## ⚙️ التكوينات المتقدمة

### تعطيل الترجمة التلقائية مؤقتاً

في `.env`:
```env
TRANSLATION_ENABLED=false
```

### تعطيل إعادة الترجمة عند التحديث

في `.env`:
```env
TRANSLATION_AUTO_ON_UPDATE=false
```

### ضبط جودة الترجمة

في `.env`:
```env
# Temperature: 0.0 (محددة) إلى 1.0 (إبداعية)
TRANSLATION_TEMPERATURE=0.3

# Max tokens للمقالات الطويلة
TRANSLATION_MAX_TOKENS=8192
```

### تخصيص Queue

في `.env`:
```env
QUEUE_CONNECTION=database
TRANSLATION_QUEUE_NAME=translations
TRANSLATION_QUEUE_TRIES=3
TRANSLATION_QUEUE_TIMEOUT=120
```

---

## 🔍 المراقبة

### مراقبة Logs مباشرة

```bash
tail -f storage/logs/laravel.log
```

ستشاهد:
```
[INFO] Translation job dispatched: article_id=123
[INFO] Starting translation job: article_id=123  
[INFO] Article translated successfully: title_en=Breaking News
```

### فحص Jobs الفاشلة

```bash
php artisan queue:failed
```

### إعادة تشغيل Jobs الفاشلة

```bash
# إعادة جميع Jobs الفاشلة
php artisan queue:retry all

# إعادة job محدد
php artisan queue:retry [job-id]
```

### حذف Jobs الفاشلة

```bash
php artisan queue:flush
```

---

## 🧪 الاختبار

### تشغيل جميع الاختبارات

```bash
php artisan test --filter=TranslationSystemTest
```

### اختبار الاتصال بـ API

```bash
php artisan tinker
```

```php
$service = app(\App\Services\GeminiTranslationService::class);
$service->testConnection(); // يجب أن يرجع true
```

### اختبار ترجمة بسيطة

```php
$service = app(\App\Services\GeminiTranslationService::class);
$result = $service->translateContent(
    'اختبار',
    '<p>هذا اختبار</p>'
);
dd($result);
```

---

## 🛠️ استكشاف الأخطاء

### ❌ المشكلة: "Translation job dispatched" لكن لا توجد ترجمة

**الحل**:
```bash
# تحقق من Queue Worker
php artisan queue:work

# أو تحقق من Jobs الفاشلة
php artisan queue:failed
```

### ❌ المشكلة: "Gemini API Error: 401"

**الحل**:
```env
# تحقق من صحة API Key في .env
GEMINI_API_KEY=AIza...
```

### ❌ المشكلة: "Gemini API Error: 429"

**السبب**: تجاوزت حصة API  
**الحل**: انتظر قليلاً أو ارفع الحصة في Google AI Studio

### ❌ المشكلة: الترجمة بطيئة

**الحل**:
```bash
# شغّل عدة Queue Workers متزامنة
php artisan queue:work &
php artisan queue:work &
php artisan queue:work &
```

### ❌ المشكلة: أكواد HTML تغيرت

**السبب**: نادر جداً  
**الحل**: 
1. راجع prompt في `GeminiTranslationService::buildTranslationPrompt()`
2. زد التوضيحات حول الحفاظ على HTML

---

## 🏭 نشر على Production

### 1. استخدم Supervisor للـ Queue Worker

إنشاء `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

ثم:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 2. استخدم Redis بدلاً من Database

في `.env`:
```env
QUEUE_CONNECTION=redis
```

### 3. قم بإعداد Cron Job للـ Failed Jobs

```cron
*/30 * * * * cd /path/to/project && php artisan queue:retry all --queue=failed
```

### 4. فعّل التنبيهات

في `config/translation.php`:
```php
'notifications' => [
    'notify_on_failure' => true,
    'failure_recipients' => 'admin@example.com',
],
```

---

## 📊 الإحصائيات والمقاييس

### عدد المقالات المترجمة

```php
$translatedCount = Article::whereNotNull('title_en')
    ->whereNotNull('content_en')
    ->count();
```

### عدد المقالات بانتظار الترجمة

```php
$pendingCount = Article::where(function($q) {
    $q->whereNull('title_en')
      ->orWhereNull('content_en');
})->count();
```

### متوسط وقت الترجمة

راجع `storage/logs/laravel.log` وقارن timestamps بين:
- `Translation job dispatched`
- `Article translated successfully`

---

## 🔐 الأمان

✅ **API Key**: محفوظ في `.env` (غير مشمول في Git)  
✅ **Input Validation**: جميع المدخلات محمية  
✅ **Rate Limiting**: Gemini API لديه حماية تلقائية  
✅ **Secure Logs**: لا يتم تسجيل بيانات حساسة  

---

## 💰 التكلفة

**Gemini 2.0 Flash** مجاني ضمن حدود معينة:

| الإصدار | الطلبات المجانية | بعد الحد |
|---------|------------------|-----------|
| Free Tier | 15 RPM, 1M TPM | - |
| Paid | حسب الاستخدام | $0.075/$1M tokens |

راجع: [Gemini Pricing](https://ai.google.dev/pricing)

---

## 🎓 التطوير المستقبلي

أفكار للتحسين:

- [ ] دعم لغات إضافية (فرنسية، ألمانية، إلخ)
- [ ] واجهة مراجعة الترجمات في Admin Panel  
- [ ] نظام تقييم جودة الترجمات
- [ ] Cache للترجمات المتشابهة
- [ ] Fallback لخدمة ترجمة أخرى
- [ ] Webhooks عند اكتمال الترجمة
- [ ] Dashboard للإحصائيات

---

## 📞 الدعم

### الوثائق الكاملة

- **البدء السريع**: `TRANSLATION_SETUP.md`
- **التفاصيل الكاملة**: `TRANSLATION_SYSTEM.md`
- **مخططات التدفق**: `TRANSLATION_FLOW.md`
- **ملخص التنفيذ**: `IMPLEMENTATION_SUMMARY.md`

### روابط مفيدة

- [Gemini API Documentation](https://ai.google.dev/docs)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Observer Documentation](https://laravel.com/docs/eloquent#observers)

---

## ✅ Checklist - قائمة التحقق

قبل البدء، تأكد من:

- [ ] تم الحصول على `GEMINI_API_KEY`
- [ ] تم إضافة المفتاح في `.env`
- [ ] تم تشغيل `php artisan migrate`
- [ ] تم اختبار `php test_translation.php` بنجاح
- [ ] Queue Worker يعمل: `php artisan queue:work`
- [ ] تم اختبار إنشاء مقال جديد
- [ ] تم التحقق من ظهور `title_en` و `content_en`

---

## 🎉 خلاصة

لديك الآن نظام ترجمة **احترافي** و**موثوق** و**قابل للتوسع**!

### ما تم تنفيذه:

✅ 8 ملفات PHP جديدة  
✅ 5 ملفات محدثة  
✅ ملف تكوين شامل  
✅ 4 ملفات توثيق  
✅ اختبارات كاملة  
✅ سكريبت اختبار سريع  
✅ أمر CLI للترجمة الجماعية  
✅ معالجة أخطاء قوية  
✅ Clean Code & PSR Standards  

**النظام جاهز للاستخدام الفوري!** 🚀

</div>

---

**Developed by**: AI Coding Expert  
**Date**: November 20, 2025  
**Version**: 1.0.0  
**Laravel**: 11.x  
**PHP**: 8.2+  
**License**: MIT
