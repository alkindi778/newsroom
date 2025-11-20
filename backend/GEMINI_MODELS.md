# Gemini Model Names Reference

## ✅ النماذج الصحيحة (Correct Models)

### Gemini 2.0 (Latest - Recommended)
- `gemini-2.0-flash-exp` ⭐ (Experimental - أسرع وأحدث)
- `gemini-2.0-flash-thinking-exp` (مع تفكير موسع)

### Gemini 1.5 (Stable)
- `gemini-1.5-flash` ✅ (سريع ومستقر)
- `gemini-1.5-flash-8b` (خفيف جداً)
- `gemini-1.5-pro` (أقوى لكن أبطأ)

### Gemini 1.0 (Legacy)
- `gemini-pro` (قديم)
- `gemini-pro-vision` (للصور)

---

## ⚙️ تغيير الموديل

### في .env:
```env
# جرّب هذا أولاً (الأكثر استقراراً)
GEMINI_MODEL=gemini-1.5-flash

# أو هذا (الأحدث - تجريبي)
GEMINI_MODEL=gemini-2.0-flash-exp

# أو هذا (الأقوى)
GEMINI_MODEL=gemini-1.5-pro
```

---

## 🔍 المشكلة المحتملة

إذا كان لديك في `.env`:
```env
GEMINI_MODEL=gemini-2.5-flash  ❌ خطأ - هذا الموديل غير موجود!
```

يجب أن يكون:
```env
GEMINI_MODEL=gemini-2.0-flash-exp  ✅ صحيح
# أو
GEMINI_MODEL=gemini-1.5-flash  ✅ صحيح (موصى به)
```

---

## 📝 التوصية

استخدم `gemini-1.5-flash` لأنه:
- ✅ مستقر تماماً
- ✅ سريع جداً
- ✅ دقيق في الترجمة
- ✅ مجاني ضمن حدود كبيرة

---

## 🚀 الخطوة التالية

1. افتح `.env`
2. غيّر السطر إلى:
   ```env
   GEMINI_MODEL=gemini-1.5-flash
   ```
3. احفظ الملف
4. جرّب مرة أخرى:
   ```bash
   php test_translation.php
   ```
