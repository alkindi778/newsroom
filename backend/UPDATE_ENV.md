# ⚙️ إعدادات Gemini في ملف .env

## 📝 افتح ملف .env وأضف أو حدّث هذه الأسطر:

```env
# Google Gemini AI - Translation System
GEMINI_API_KEY=your-api-key-here
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
GEMINI_MODEL=gemini-flash-latest
```

---

## 🔑 خطوات التطبيق:

### 1. افتح ملف `.env`
الموجود في: `c:\xampp\htdocs\newsroom\backend\.env`

### 2. ابحث عن قسم GEMINI
إذا كان موجود، حدّثه. إذا لم يكن موجود، أضف الأسطر أعلاه في نهاية الملف.

### 3. ضع API Key الخاص بك
استبدل `your-api-key-here` بمفتاح الـ API الحقيقي:
```env
GEMINI_API_KEY=AIzaSyBl9f...  (المفتاح الكامل)
```

### 4. تأكد من اسم الموديل
```env
GEMINI_MODEL=gemini-flash-latest  ✅
```

---

## 🧪 بعد التحديث - اختبر النظام

```bash
php test_translation.php
```

يجب أن ترى:
```
✅ الاتصال ناجح!
✅ الترجمة ناجحة!
```

---

## 🚀 ثم شغّل Queue Worker

```bash
php artisan queue:work
```

---

## 💡 نصيحة

إذا كانت `GEMINI_API_KEY` موجودة بالفعل وصحيحة، فقط تأكد من:
```env
GEMINI_MODEL=gemini-flash-latest
```

وليس:
```env
GEMINI_MODEL=gemini-2.5-flash  ❌ (خطأ)
```

---

## ✅ الملف الكامل يجب أن يحتوي على:

```env
# ... إعدادات أخرى ...

# Google Gemini AI Configuration
GEMINI_API_KEY=AIzaSyBl9f...
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
GEMINI_MODEL=gemini-flash-latest

# Translation System Settings (اختياري)
TRANSLATION_ENABLED=true
TRANSLATION_AUTO_ON_CREATE=true
TRANSLATION_AUTO_ON_UPDATE=true
```

---

احفظ الملف ثم جرّب `php test_translation.php`
