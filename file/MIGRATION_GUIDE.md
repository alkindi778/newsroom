# دليل دمج البيانات القديمة

## نظرة عامة
هذا الدليل يشرح كيفية دمج البيانات من قاعدة البيانات القديمة `adenstc_db` إلى قاعدة البيانات الجديدة.

## البيانات التي سيتم دمجها

### من القاعدة القديمة:
- **posts** (الأخبار) → سيتم نقلها إلى جدول `articles` الجديد
- **articles** (مقالات الرأي) → سيتم نقلها إلى جدول `opinions` الجديد
- **writers** (الكتاب) → سيتم نقلها إلى جدول `writers` الجديد

## خطوات التنفيذ

### 1. استيراد قاعدة البيانات القديمة

أولاً، قم باستيراد ملف SQL القديم إلى MySQL:

```bash
# إنشاء قاعدة بيانات جديدة للبيانات القديمة
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS adenstc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# استيراد البيانات
mysql -u root -p adenstc_db < "c:/xampp/htdocs/newsroom/adenstc_db.sql/adenstc_db.sql"
```

### 2. إعداد ملف البيئة `.env`

أضف الإعدادات التالية لملف `.env` في مجلد `backend`:

```env
# إعدادات قاعدة البيانات القديمة
OLD_DB_HOST=127.0.0.1
OLD_DB_PORT=3306
OLD_DB_DATABASE=adenstc_db
OLD_DB_USERNAME=root
OLD_DB_PASSWORD=
```

### 3. إنشاء مجلد الصور القديمة

قم بإنشاء مجلد لتخزين الصور القديمة:

```bash
cd backend
mkdir -p storage/app/public/old_photos
```

إذا كانت لديك الصور القديمة، انسخها إلى:
```
backend/storage/app/public/old_photos/
```

### 4. تنفيذ عملية الدمج

#### دمج كل شيء دفعة واحدة:
```bash
cd backend
php artisan migrate:old-database
```

#### أو تنفيذ كل خطوة على حدة:

**أ. دمج الكتاب أولاً:**
```bash
php artisan migrate:old-database --step=writers
```

**ب. دمج مقالات الرأي:**
```bash
php artisan migrate:old-database --step=opinions
```

**ج. دمج الأخبار:**
```bash
php artisan migrate:old-database --step=articles
```

## ملاحظات هامة

### حول الصور:
- مسارات الصور القديمة ستتحول من `/photos/` إلى `/storage/old_photos/`
- يجب نسخ مجلد الصور القديم يدوياً إلى المسار الجديد
- الصور التي لم يتم العثور عليها سيتم تخطيها

### حول الكتاب:
- سيتم إنشاء كاتب افتراضي للمقالات التي لا تحتوي على كاتب محدد
- سيتم تحويل معلومات الكاتب (الاسم، البريد، السيرة، الصورة، روابط التواصل)

### حول الفئات:
- إذا لم يتم العثور على فئة مطابقة، سيتم استخدام الفئة الافتراضية "عام"
- يُنصح بإنشاء الفئات يدوياً قبل الدمج لضمان التوافق الصحيح

### حول الأخبار والمقالات:
- سيتم نقل العنوان، المحتوى، الصورة، التاريخ، عدد المشاهدات
- المقالات المميزة (`editor_choice = 1`) ستبقى مميزة
- الحالة (`status = 0` تعني منشور)

## التحقق من النتائج

بعد الدمج، تحقق من:

```bash
# عدد الكتاب
mysql -u root -p -e "SELECT COUNT(*) as writers_count FROM writers;" your_new_database

# عدد مقالات الرأي
mysql -u root -p -e "SELECT COUNT(*) as opinions_count FROM opinions;" your_new_database

# عدد الأخبار
mysql -u root -p -e "SELECT COUNT(*) as articles_count FROM articles;" your_new_database
```

## استكشاف الأخطاء

### خطأ: Connection refused
- تأكد من تشغيل MySQL
- تحقق من إعدادات الاتصال في `.env`

### خطأ: Table not found
- تأكد من استيراد قاعدة البيانات القديمة بنجاح
- تحقق من اسم القاعدة في `.env`

### خطأ: Duplicate entry
- إذا كررت عملية الدمج، قد تحتاج لحذف البيانات المكررة أولاً
- أو استخدام خيار `--step` لتنفيذ خطوات محددة فقط

## التنظيف بعد الدمج

بعد التأكد من نجاح الدمج:

1. يمكنك حذف قاعدة البيانات القديمة:
```bash
mysql -u root -p -e "DROP DATABASE adenstc_db;"
```

2. احذف ملف SQL القديم إذا لم تعد بحاجة إليه

3. تحديث روابط الصور القديمة إذا لزم الأمر

## الدعم

إذا واجهت أي مشاكل:
1. تحقق من سجلات Laravel: `backend/storage/logs/laravel.log`
2. راجع رسائل الخطأ في Terminal
3. تأكد من صحة البيانات في القاعدة القديمة
