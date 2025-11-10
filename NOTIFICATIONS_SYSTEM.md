# نظام الإشعارات - Push Notifications

## نظرة عامة
نظام إشعارات متكامل يرسل إشعارات فورية للمستخدمين على الهاتف والحاسوب عند نشر محتوى جديد (أخبار، مقالات، فيديوهات).

## المتطلبات

### Backend (Laravel)
1. تثبيت مكتبة web-push:
```bash
cd backend
composer require minishlink/web-push
```

2. توليد مفاتيح VAPID:
```bash
# استخدام Node.js
npx web-push generate-vapid-keys

# أو باستخدام PHP
php -r "require 'vendor/autoload.php'; \$keys = \Minishlink\WebPush\VAPID::createVapidKeys(); echo 'Public Key: ' . \$keys['publicKey'] . PHP_EOL; echo 'Private Key: ' . \$keys['privateKey'] . PHP_EOL;"
```

3. إضافة المفاتيح إلى `.env`:
```env
VAPID_PUBLIC_KEY=your_public_key_here
VAPID_PRIVATE_KEY=your_private_key_here
```

4. تشغيل Migration:
```bash
php artisan migrate
```

### Frontend (Nuxt.js)
لا توجد مكتبات إضافية مطلوبة - النظام يستخدم Web Push API المدمج في المتصفحات.

## البنية

### Backend

#### 1. Database
- **Table**: `push_subscriptions`
  - `id`: معرف فريد
  - `user_id`: معرف المستخدم (اختياري - nullable)
  - `endpoint`: نقطة النهاية للإشعار
  - `public_key`: المفتاح العام
  - `auth_token`: رمز المصادقة
  - `content_encoding`: ترميز المحتوى
  - `preferences`: تفضيلات المستخدم (JSON)
  - `is_active`: حالة الاشتراك
  - `last_used_at`: آخر استخدام

#### 2. Models
- **PushSubscription**: `backend/app/Models/PushSubscription.php`
  - إدارة اشتراكات الإشعارات
  - Scopes للاشتراكات النشطة والمستخدمين

#### 3. Services
- **PushNotificationService**: `backend/app/Services/PushNotificationService.php`
  - `sendToAll()`: إرسال إشعار لجميع المشتركين
  - `send()`: إرسال إشعار لاشتراك محدد
  - `sendNewArticleNotification()`: إشعار خبر جديد
  - `sendNewVideoNotification()`: إشعار فيديو جديد
  - `sendNewOpinionNotification()`: إشعار رأي جديد
  - `sendCustomNotification()`: إشعار مخصص
  - `cleanupOldSubscriptions()`: تنظيف الاشتراكات القديمة

#### 4. Controllers
- **PushSubscriptionController**: `backend/app/Http/Controllers/Api/PushSubscriptionController.php`
  - `GET /api/v1/push/public-key`: الحصول على المفتاح العام
  - `POST /api/v1/push/subscribe`: إنشاء اشتراك جديد
  - `POST /api/v1/push/unsubscribe`: إلغاء الاشتراك
  - `POST /api/v1/push/update-preferences`: تحديث التفضيلات
  - `POST /api/v1/push/test`: إرسال إشعار تجريبي

### Frontend

#### 1. Service Worker
- **File**: `frontend/public/sw.js`
- يستقبل ويعرض الإشعارات
- يتعامل مع النقر على الإشعار
- يفتح الرابط المرتبط بالإشعار

#### 2. Composable
- **usePushNotifications**: `frontend/composables/usePushNotifications.ts`
  - إدارة حالة الإشعارات
  - تسجيل Service Worker
  - طلب الأذونات
  - الاشتراك/إلغاء الاشتراك
  - التحقق من حالة الاشتراك

#### 3. Components
- **NotificationPrompt**: `frontend/components/NotificationPrompt.vue`
  - مطالبة المستخدم بتفعيل الإشعارات
  - تظهر بعد 3 ثواني من تحميل الصفحة
  - يمكن تأجيلها لمدة 7 أيام

## الاستخدام

### إرسال إشعار عند إنشاء محتوى جديد

#### مثال 1: إرسال إشعار عند إنشاء خبر جديد
```php
use App\Services\PushNotificationService;

class ArticleController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    public function store(Request $request)
    {
        // حفظ الخبر
        $article = Article::create($request->validated());

        // إرسال إشعار للمشتركين
        $this->pushService->sendNewArticleNotification($article);

        return response()->json($article);
    }
}
```

#### مثال 2: إرسال إشعار مخصص
```php
use App\Services\PushNotificationService;

$pushService = app(PushNotificationService::class);

$pushService->sendCustomNotification(
    'عنوان الإشعار',
    'محتوى الإشعار',
    'https://example.com/article/123',
    [
        'icon' => '/images/custom-icon.png',
        'badge' => '/images/badge.png',
        'tag' => 'breaking-news-123',
        'data' => [
            'type' => 'article',
            'id' => 123
        ]
    ]
);
```

### استخدام Composable في Frontend

```vue
<script setup>
import { usePushNotifications } from '~/composables/usePushNotifications'

const {
  state,
  canSubscribe,
  canUnsubscribe,
  isBlocked,
  subscribe,
  unsubscribe,
  sendTestNotification
} = usePushNotifications()

// الاشتراك في الإشعارات
const handleSubscribe = async () => {
  const success = await subscribe()
  if (success) {
    console.log('تم الاشتراك بنجاح')
  }
}

// إلغاء الاشتراك
const handleUnsubscribe = async () => {
  const success = await unsubscribe()
  if (success) {
    console.log('تم إلغاء الاشتراك بنجاح')
  }
}

// إرسال إشعار تجريبي
const handleTest = async () => {
  await sendTestNotification()
}
</script>

<template>
  <div>
    <button v-if="canSubscribe" @click="handleSubscribe">
      تفعيل الإشعارات
    </button>
    
    <button v-if="canUnsubscribe" @click="handleUnsubscribe">
      إلغاء الإشعارات
    </button>
    
    <button v-if="state.subscribed" @click="handleTest">
      إرسال إشعار تجريبي
    </button>
    
    <p v-if="isBlocked">
      تم حظر الإشعارات في المتصفح
    </p>
  </div>
</template>
```

## Events & Listeners (اختياري)

يمكن إضافة Laravel Events لربط النظام تلقائياً:

```php
// app/Events/ArticlePublished.php
class ArticlePublished
{
    public function __construct(public Article $article) {}
}

// app/Listeners/SendArticleNotification.php
class SendArticleNotification
{
    public function __construct(protected PushNotificationService $pushService) {}

    public function handle(ArticlePublished $event)
    {
        $this->pushService->sendNewArticleNotification($event->article);
    }
}

// في EventServiceProvider
protected $listen = [
    ArticlePublished::class => [
        SendArticleNotification::class,
    ],
];
```

## Command للتنظيف الدوري

```php
// app/Console/Commands/CleanupPushSubscriptions.php
php artisan make:command CleanupPushSubscriptions

class CleanupPushSubscriptions extends Command
{
    protected $signature = 'push:cleanup {--days=90}';
    
    public function handle(PushNotificationService $pushService)
    {
        $days = $this->option('days');
        $pushService->cleanupOldSubscriptions($days);
        $this->info("تم تنظيف الاشتراكات القديمة");
    }
}
```

أضف إلى `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // تنظيف الاشتراكات القديمة كل أسبوع
    $schedule->command('push:cleanup --days=90')->weekly();
}
```

## الاختبار

### 1. اختبار API
```bash
# الحصول على المفتاح العام
curl http://localhost/api/v1/push/public-key

# إرسال إشعار تجريبي
curl -X POST http://localhost/api/v1/push/test
```

### 2. اختبار من المتصفح
1. افتح الموقع
2. انتظر ظهور مطالبة الإشعارات
3. اضغط "تفعيل الإشعارات"
4. اقبل الأذونات في المتصفح
5. اذهب إلى console وأدخل:
```javascript
const { sendTestNotification } = usePushNotifications()
await sendTestNotification()
```

## المتصفحات المدعومة
- ✅ Chrome (Desktop & Mobile)
- ✅ Firefox (Desktop & Mobile)
- ✅ Edge
- ✅ Safari (iOS 16.4+)
- ✅ Opera
- ❌ Internet Explorer (غير مدعوم)

## ملاحظات أمنية

1. **VAPID Keys**: احتفظ بالمفاتيح الخاصة آمنة ولا تشاركها
2. **HTTPS**: نظام الإشعارات يتطلب HTTPS (أو localhost للتطوير)
3. **Permissions**: احترم اختيار المستخدم ولا ترسل إشعارات spam
4. **Privacy**: لا تخزن معلومات حساسة في بيانات الإشعار

## الأيقونات المطلوبة

تأكد من وجود هذه الأيقونات في `frontend/public/`:
- `icon-192x192.png`: أيقونة الإشعار (192x192)
- `icon-512x512.png`: أيقونة كبيرة (512x512)
- `badge-72x72.png`: شارة صغيرة (72x72)

## Troubleshooting

### المشكلة: "المتصفح لا يدعم الإشعارات"
**الحل**: تأكد من:
- استخدام HTTPS أو localhost
- المتصفح يدعم Web Push API
- Service Worker مفعل

### المشكلة: "فشل تسجيل Service Worker"
**الحل**: 
- تحقق من وجود `/sw.js` في public
- تأكد من عدم حظر Service Workers في المتصفح
- افحص console للأخطاء

### المشكلة: "لم يتم العثور على مفتاح VAPID"
**الحل**:
- تأكد من إضافة VAPID_PUBLIC_KEY و VAPID_PRIVATE_KEY في .env
- شغل `php artisan config:cache`

### المشكلة: "الإشعارات لا تصل"
**الحل**:
- تحقق من Permissions في المتصفح
- تأكد من أن Service Worker نشط
- افحص logs في الـ Backend
- تأكد من وجود اشتراك نشط في قاعدة البيانات

## الخطوات التالية

1. ✅ تثبيت composer package: `minishlink/web-push`
2. ✅ توليد VAPID keys
3. ✅ تشغيل migrations
4. ✅ إضافة الأيقونات المناسبة
5. 🔲 ربط النظام بإنشاء المحتوى (Articles, Videos, Opinions)
6. 🔲 اختبار النظام على production مع HTTPS
7. 🔲 إضافة لوحة تحكم للإشعارات (admin panel)

## الدعم

للمزيد من المعلومات:
- [Web Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API)
- [Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [minishlink/web-push](https://github.com/web-push-libs/web-push-php)
