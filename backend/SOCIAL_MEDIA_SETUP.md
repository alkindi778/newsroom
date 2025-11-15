# نظام النشر التلقائي على وسائل التواصل الاجتماعي

## نظرة عامة
نظام متكامل لنشر الأخبار والمقالات والفيديوهات تلقائياً على:
- Facebook
- Twitter
- Telegram

## المتطلبات

### 1. Facebook
- صفحة Facebook
- Access Token من Facebook Graph API
- Page ID

### 2. Twitter
- حساب Twitter Developer
- API Keys و Secrets
- Bearer Token

### 3. Telegram
- Bot Token من BotFather
- Channel ID

## التثبيت

### 1. تشغيل Migration
```bash
php artisan migrate
```

### 2. تحديث ملف .env
انسخ الإعدادات من `.env.social-media.example` إلى `.env`

### 3. مسح الـ Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## الاستخدام

### النشر التلقائي
عند إنشاء مقالة جديدة مع `is_published = true`، سيتم نشرها تلقائياً على جميع المنصات المفعّلة.

### لوحة التحكم
- الإعدادات: `/admin/social-media/settings`
- المنشورات: `/admin/social-media/posts`

## الميزات

✅ نشر تلقائي عند إنشاء مقالة
✅ دعم ثلاث منصات (Facebook, Twitter, Telegram)
✅ تتبع حالة المنشورات
✅ إعادة محاولة المنشورات الفاشلة
✅ جدولة المنشورات
✅ تحكم كامل من لوحة الإدارة
✅ رسائل مخصصة لكل منصة
✅ دعم الهاشتاجات والفئات
