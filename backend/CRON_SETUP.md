# Cron Job Setup للترجمة التلقائية

## 📅 إعداد الترجمة التلقائية (200 طلب/يوم)

### 1. افتح Crontab
```bash
crontab -e
```

### 2. أضف السطر التالي (يعمل يومياً الساعة 2 صباحاً):
```bash
0 2 * * * cd /var/www/newsroom/backend && php artisan translate:daily --limit=200 >> /var/log/newsroom-translate.log 2>&1
```

### 3. حفظ والخروج
- في nano: اضغط `Ctrl+X` ثم `Y` ثم `Enter`
- في vim: اضغط `ESC` ثم اكتب `:wq` ثم `Enter`

---

## 🔧 توزيع الترجمات اليومية

| النوع | عدد العناصر | الأولوية |
|-------|-------------|----------|
| Articles | 130 | عالية جداً 🔥 |
| Opinions | 30 | عالية 📝 |
| Writers | 35 | عالية ✍️ |
| Videos | 5 | منخفضة 🎬 |
| **المجموع** | **200** | |

---

## 🎯 أوامر يدوية (للتجربة)

### تشغيل الترجمة اليومية يدوياً:
```bash
php artisan translate:daily
```

### تغيير الحد (مثلاً 100 بدلاً من 200):
```bash
php artisan translate:daily --limit=100
```

### مراقبة التقدم:
```bash
watch -n 10 'php artisan tinker --execute="
echo \"Videos: \" . App\Models\Video::whereNotNull(\"title_en\")->count() . \"/\" . App\Models\Video::count() . PHP_EOL;
echo \"Writers: \" . App\Models\Writer::whereNotNull(\"name_en\")->count() . \"/\" . App\Models\Writer::count() . PHP_EOL;
echo \"Opinions: \" . App\Models\Opinion::whereNotNull(\"title_en\")->count() . \"/\" . App\Models\Opinion::count() . PHP_EOL;
echo \"Articles: \" . App\Models\Article::whereNotNull(\"title_en\")->count() . \"/\" . App\Models\Article::count() . PHP_EOL;
"'
```

---

## 📊 عرض السجل (Logs)
```bash
tail -f /var/log/newsroom-translate.log
```

---

## ⚙️ تفعيل Queue Workers تلقائياً مع PM2

### إنشاء ملف PM2 Ecosystem:
```bash
cd /var/www/newsroom/backend
nano ecosystem.config.cjs
```

### المحتوى:
```javascript
module.exports = {
  apps: [
    {
      name: 'newsroom-queue',
      script: 'artisan',
      interpreter: 'php',
      args: 'queue:work --tries=3 --timeout=180 --sleep=3 --max-time=3600',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '512M',
      env: {
        APP_ENV: 'production'
      }
    }
  ]
};
```

### تشغيل Queue Worker:
```bash
pm2 start ecosystem.config.cjs
pm2 save
pm2 startup
```

---

## ✅ التحقق من النظام

### 1. تحقق من Cron Jobs:
```bash
crontab -l
```

### 2. تحقق من PM2:
```bash
pm2 status
pm2 logs newsroom-queue
```

### 3. تحقق من التقدم:
```bash
php artisan translate:daily --limit=0  # عرض الإحصائيات فقط
```

---

## 🚨 استكشاف الأخطاء

### إذا لم تعمل الترجمة:
```bash
# تحقق من API Key
grep GEMINI_API_KEY /var/www/newsroom/backend/.env

# تحقق من Queue
php artisan queue:failed

# أعد تشغيل Queue Worker
pm2 restart newsroom-queue

# تحقق من Logs
tail -100 storage/logs/laravel.log
```

---

## 💡 نصائح

1. **لا تزيد عن 200 طلب/يوم** لتجنب تجاوز حد API المجاني
2. **Queue Workers يجب أن تعمل دائماً** لمعالجة Jobs
3. **راقب Logs يومياً** للتأكد من عدم وجود أخطاء
4. **احتفظ بنسخة احتياطية من .env** قبل تحديث API Keys
