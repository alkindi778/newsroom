# دليل نشر نظام الإشعارات على السيرفر

## المتطلبات الأساسية

### ⚠️ مهم جداً:
- **يجب أن يعمل الموقع على HTTPS** - Web Push Notifications لا تعمل على HTTP
- تأكد من تثبيت Supervisor لإدارة Queue Workers

---

## 1️⃣ رفع الملفات إلى السيرفر

### باستخدام SCP/SFTP:

```bash
# من جهازك المحلي، ارفع الملفات الجديدة:
scp -i newsroom-key.pem -r backend/app/Events root@your-server-ip:/var/www/html/newsroom/backend/app/
scp -i newsroom-key.pem -r backend/app/Listeners root@your-server-ip:/var/www/html/newsroom/backend/app/
scp -i newsroom-key.pem backend/app/Services/PushNotificationService.php root@your-server-ip:/var/www/html/newsroom/backend/app/Services/
scp -i newsroom-key.pem backend/app/Http/Controllers/Api/PushSubscriptionController.php root@your-server-ip:/var/www/html/newsroom/backend/app/Http/Controllers/Api/
scp -i newsroom-key.pem backend/app/Models/PushSubscription.php root@your-server-ip:/var/www/html/newsroom/backend/app/Models/
scp -i newsroom-key.pem backend/database/migrations/2025_11_09_104300_create_push_subscriptions_table.php root@your-server-ip:/var/www/html/newsroom/backend/database/migrations/
```

---

## 2️⃣ تثبيت المكتبات على السيرفر

```bash
# الاتصال بالسيرفر
ssh -i newsroom-key.pem root@your-server-ip

# الانتقال إلى مجلد المشروع
cd /var/www/html/newsroom/backend

# تثبيت minishlink/web-push
composer require minishlink/web-push

# تشغيل Migrations
php artisan migrate

# مسح الـ cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 3️⃣ توليد مفاتيح VAPID على السيرفر

```bash
# توليد المفاتيح
npx web-push generate-vapid-keys

# ستظهر لك:
# Public Key: BxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxQ
# Private Key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### إضافة المفاتيح إلى `.env`:

```bash
nano /var/www/html/newsroom/backend/.env
```

أضف في نهاية الملف:

```env
# VAPID Keys for Web Push Notifications
VAPID_PUBLIC_KEY=YOUR_PUBLIC_KEY_HERE
VAPID_PRIVATE_KEY=YOUR_PRIVATE_KEY_HERE
```

احفظ بـ `Ctrl+X` ثم `Y` ثم `Enter`

```bash
# مسح cache بعد التعديل
php artisan config:cache
```

---

## 4️⃣ إعداد Supervisor لـ Queue Workers

### إنشاء ملف Supervisor:

```bash
sudo nano /etc/supervisor/conf.d/newsroom-worker.conf
```

### أضف المحتوى التالي:

```ini
[program:newsroom-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/newsroom/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/newsroom/backend/storage/logs/worker.log
stopwaitsecs=3600
```

### تفعيل Worker:

```bash
# إعادة قراءة إعدادات Supervisor
sudo supervisorctl reread

# تحديث Supervisor
sudo supervisorctl update

# بدء Worker
sudo supervisorctl start newsroom-worker:*

# التحقق من الحالة
sudo supervisorctl status
```

---

## 5️⃣ تحديث Frontend على السيرفر

### رفع ملفات Frontend:

```bash
# من جهازك المحلي:
scp -i newsroom-key.pem frontend/composables/usePushNotifications.ts root@your-server-ip:/var/www/html/newsroom/frontend/composables/
scp -i newsroom-key.pem frontend/components/NotificationPrompt.vue root@your-server-ip:/var/www/html/newsroom/frontend/components/
scp -i newsroom-key.pem frontend/public/sw.js root@your-server-ip:/var/www/html/newsroom/frontend/public/
scp -i newsroom-key.pem frontend/public/manifest.json root@your-server-ip:/var/www/html/newsroom/frontend/public/
```

### على السيرفر:

```bash
cd /var/www/html/newsroom/frontend

# إعادة بناء المشروع
npm run build

# إعادة تشغيل PM2 (إذا كنت تستخدم PM2)
pm2 restart newsroom-frontend
```

---

## 6️⃣ التحقق من عمل النظام

### 1. تحقق من Queue Worker:

```bash
sudo supervisorctl status newsroom-worker:*
```

يجب أن ترى: `RUNNING`

### 2. تحقق من الـ API:

```bash
curl https://your-domain.com/api/v1/push/public-key
```

يجب أن يعيد المفتاح العام.

### 3. اختبار من المتصفح:

1. افتح الموقع: `https://your-domain.com`
2. انتظر ظهور مربع الإشعارات
3. اضغط "تفعيل الإشعارات"
4. اقبل الأذونات
5. أضف خبر جديد من لوحة التحكم
6. يجب أن يصل الإشعار!

---

## 7️⃣ مراقبة Logs

### Worker Logs:

```bash
tail -f /var/www/html/newsroom/backend/storage/logs/worker.log
```

### Laravel Logs:

```bash
tail -f /var/www/html/newsroom/backend/storage/logs/laravel.log
```

### Supervisor Logs:

```bash
sudo tail -f /var/log/supervisor/supervisord.log
```

---

## 🔧 حل المشاكل الشائعة

### مشكلة: Queue Worker لا يعمل

```bash
sudo supervisorctl restart newsroom-worker:*
sudo supervisorctl tail newsroom-worker:newsroom-worker_00
```

### مشكلة: الإشعارات لا تصل

```bash
# تحقق من الاشتراكات في Database
cd /var/www/html/newsroom/backend
php artisan tinker --execute="echo App\Models\PushSubscription::count();"

# اختبار إرسال إشعار
php artisan tinker --execute="
\$service = app('App\Services\PushNotificationService');
\$service->sendCustomNotification('اختبار', 'هذا إشعار تجريبي', '/');
"
```

### مشكلة: HTTPS مطلوب

Web Push Notifications تتطلب HTTPS. تأكد من:
- تثبيت SSL Certificate (Let's Encrypt مجاني)
- Nginx/Apache يعيد توجيه HTTP إلى HTTPS
- الموقع يعمل على `https://` وليس `http://`

---

## 📝 ملاحظات مهمة

1. **Queue Workers**: تأكد من تشغيلها دائماً باستخدام Supervisor
2. **VAPID Keys**: احتفظ بنسخة احتياطية من المفاتيح - لا يمكن تغييرها
3. **HTTPS**: إلزامي - لن تعمل الإشعارات بدونه
4. **أيقونات الإشعارات**: تأكد من رفع الأيقونات إلى `/public`
5. **Firewall**: تأكد من أن البورت 443 (HTTPS) مفتوح

---

## 🚀 أوامر سريعة

```bash
# إعادة تشغيل كل شيء
sudo supervisorctl restart newsroom-worker:*
php artisan config:cache
php artisan route:cache

# التحقق من حالة كل شيء
sudo supervisorctl status
php artisan queue:work --once  # اختبار
```

---

## ✅ Checklist

- [ ] رفع الملفات إلى السيرفر
- [ ] تثبيت `minishlink/web-push`
- [ ] تشغيل Migration
- [ ] توليد VAPID keys وإضافتها إلى `.env`
- [ ] إعداد Supervisor
- [ ] تحديث Frontend وإعادة البناء
- [ ] التأكد من عمل HTTPS
- [ ] اختبار النظام
- [ ] مراقبة Logs

---

**تم! 🎉 نظام الإشعارات جاهز على السيرفر**
