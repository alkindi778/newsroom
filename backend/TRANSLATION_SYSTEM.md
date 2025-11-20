# نظام الترجمة التلقائية باستخدام Gemini AI

## 📋 نظرة عامة

تم تنفيذ نظام ترجمة تلقائي كامل يترجم محتوى المقالات من العربية إلى الإنجليزية باستخدام Google Gemini 2.0 Flash API. النظام يعمل في الخلفية بشكل تلقائي ولا يؤثر على سرعة الموقع.

## 🔧 المكونات المضافة

### 1. قاعدة البيانات (Database Migration)
- **الملف**: `database/migrations/2025_11_20_000000_add_english_translation_columns_to_articles_table.php`
- **الأعمدة المضافة**:
  - `title_en` (String, Nullable): العنوان بالإنجليزية
  - `content_en` (LongText, Nullable): المحتوى بالإنجليزية

### 2. خدمة الترجمة (Translation Service)
- **الملف**: `app/Services/GeminiTranslationService.php`
- **المسؤوليات**:
  - الاتصال بـ Gemini API
  - إرسال طلبات الترجمة بـ System Prompt محكم
  - معالجة الاستجابات وتحويلها لـ JSON
  - التعامل مع الأخطاء وتسجيلها
  
### 3. وظيفة الخلفية (Background Job)
- **الملف**: `app/Jobs/TranslateContentJob.php`
- **المزايا**:
  - يعمل في الخلفية (Queued)
  - إعادة المحاولة 3 مرات عند الفشل
  - تسجيل شامل للعمليات
  - تجنب الترجمة المكررة

### 4. مراقب الموديل (Observer)
- **الملف**: `app/Observers/ArticleObserver.php`
- **الأحداث المراقبة**:
  - `created`: إطلاق الترجمة عند إنشاء مقال جديد
  - `updated`: إعادة الترجمة عند تعديل المحتوى العربي
  - `restored`: الترجمة عند استعادة مقال محذوف

### 5. تحديثات الموديل
- **الملف**: `app/Models/Article.php`
- إضافة `title_en` و `content_en` إلى `$fillable`

### 6. تسجيل Observer
- **الملف**: `app/Providers/AppServiceProvider.php`
- تسجيل `ArticleObserver` في `boot()` method

## ⚙️ الإعدادات المطلوبة

### 1. إضافة المتغيرات في `.env`

```env
# Google Gemini AI Configuration
# احصل على API Key من: https://makersuite.google.com/app/apikey
GEMINI_API_KEY=your-api-key-here
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
GEMINI_MODEL=gemini-2.0-flash-exp
```

### 2. تشغيل Migration

```bash
cd backend
php artisan migrate
```

### 3. تشغيل Queue Worker

لتفعيل الترجمة الخلفية، يجب تشغيل Queue Worker:

```bash
php artisan queue:work --tries=3 --timeout=120
```

**للإنتاج (Production)**: استخدم Supervisor أو نظام مماثل لضمان استمرار عمل Queue Worker.

## 🚀 كيفية الاستخدام

### الترجمة التلقائية
النظام يعمل تلقائياً! عند إنشاء أو تحديث مقال:

```php
$article = Article::create([
    'title' => 'عنوان المقال بالعربية',
    'content' => '<p>محتوى المقال بالعربية</p>',
    'category_id' => 1,
    'user_id' => auth()->id(),
]);

// سيتم إطلاق TranslateContentJob تلقائياً في الخلفية
// بعد ثواني، سيتم تحديث title_en و content_en
```

### الوصول للترجمة

```php
$article = Article::find($id);

echo $article->title_en;    // English title
echo $article->content_en;  // English content
```

### الترجمة اليدوية

إذا أردت ترجمة مقال موجود يدوياً:

```php
use App\Jobs\TranslateContentJob;

$article = Article::find($id);
TranslateContentJob::dispatch($article);
```

### اختبار الاتصال بـ API

```php
use App\Services\GeminiTranslationService;

$service = app(GeminiTranslationService::class);
$isConnected = $service->testConnection();

if ($isConnected) {
    echo "✅ Gemini API متصل بنجاح";
} else {
    echo "❌ فشل الاتصال بـ Gemini API";
}
```

## 📊 مثال على System Prompt المستخدم

النظام يرسل prompt محكم لـ Gemini يضمن:
- ✅ إرجاع نتيجة JSON فقط (بدون markdown أو نص إضافي)
- ✅ الحفاظ على أكواد HTML كما هي
- ✅ ترجمة احترافية بلغة صحفية
- ✅ مراعاة السياق الثقافي

## 🔍 التحقق من الترجمة

### مراجعة Logs

```bash
tail -f storage/logs/laravel.log
```

ستجد سجلات مثل:
```
[INFO] Translation job dispatched: article_id=123
[INFO] Starting translation job: article_id=123
[INFO] Article translated successfully: article_id=123
```

### التحقق من Queue

```bash
php artisan queue:failed
```

## 🎯 مميزات النظام

1. **سرعة عالية**: الترجمة تتم في الخلفية بدون تأثير على تجربة المستخدم
2. **موثوقية**: إعادة المحاولة التلقائية عند الفشل
3. **ذكاء**: تجنب الترجمة المكررة للمقالات المترجمة
4. **مرونة**: سهولة تخصيص الـ prompt والإعدادات
5. **شفافية**: تسجيل شامل لكل العمليات
6. **حفاظ على البنية**: الحفاظ الكامل على أكواد HTML

## 🛠️ استكشاف الأخطاء

### المشكلة: لا تحدث الترجمة

**الحل**:
1. تأكد من وجود `GEMINI_API_KEY` في `.env`
2. تحقق من تشغيل Queue Worker: `php artisan queue:work`
3. راجع الـ logs: `storage/logs/laravel.log`

### المشكلة: ترجمة غير دقيقة

**الحل**:
1. راجع الـ prompt في `GeminiTranslationService::buildTranslationPrompt()`
2. عدّل الـ temperature في `GeminiTranslationService::translateContent()`
3. جرّب model مختلف (gemini-pro مثلاً)

### المشكلة: بطء الترجمة

**الحل**:
1. تأكد من استخدام model سريع (`gemini-2.0-flash-exp`)
2. زد عدد Queue Workers المتزامنة
3. استخدم Redis بدلاً من Database queue

## 📝 ملاحظات مهمة

1. **API Key**: يجب الحصول عليه من [Google AI Studio](https://makersuite.google.com/app/apikey)
2. **الحصص (Quotas)**: راجع حدود الاستخدام لـ Gemini API
3. **التكلفة**: Gemini 2.0 Flash مجاني ضمن حدود معينة
4. **الأمان**: لا تشارك `GEMINI_API_KEY` في الكود المصدري

## 🔄 الترجمة الجماعية (Batch Translation)

لترجمة المقالات الموجودة دفعة واحدة:

```php
// يمكن إنشاء Artisan Command لهذا الغرض
use App\Models\Article;
use App\Jobs\TranslateContentJob;

Article::whereNull('title_en')
    ->orWhereNull('content_en')
    ->chunk(50, function ($articles) {
        foreach ($articles as $article) {
            TranslateContentJob::dispatch($article);
        }
    });
```

## 🎓 التطوير المستقبلي

أفكار للتحسين:
- [ ] إضافة لغات أخرى (فرنسية، ألمانية، إلخ)
- [ ] واجهة إدارة لمراجعة الترجمات
- [ ] نظام تقييم جودة الترجمة
- [ ] تكامل مع خدمات ترجمة أخرى كـ Fallback
- [ ] تخزين Cache للترجمات المتشابهة

## 📞 الدعم

للأسئلة أو المشاكل، راجع:
- Logs: `storage/logs/laravel.log`
- Queue Status: `php artisan queue:failed`
- Gemini API Docs: https://ai.google.dev/docs

---

**تم التنفيذ بواسطة**: AI Coding Assistant  
**التاريخ**: 2025-11-20  
**الإصدار**: 1.0.0
