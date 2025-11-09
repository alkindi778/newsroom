# دليل إعداد Google Tag Manager & Consent Mode

## 📋 نظرة عامة

تم إعداد نظام Cookie Consent متكامل مع دعم كامل لـ:
- ✅ Google Tag Manager (GTM)
- ✅ Google Consent Mode v2 (إلزامي للامتثال لـ GDPR)
- ✅ Google Analytics 4 (GA4)
- ✅ إدارة موافقة المستخدم

---

## 🚀 الخطوة 1: إنشاء حساب Google Tag Manager

### أ. إنشاء الحساب
1. اذهب إلى: [https://tagmanager.google.com](https://tagmanager.google.com)
2. انقر على **"Create Account"**
3. املأ البيانات:
   - **Account Name**: اسم موقعك (مثل: Newsroom)
   - **Country**: اختر البلد
   - **Container name**: رابط موقعك (مثل: newsroom.sa)
   - **Target platform**: اختر **Web**

### ب. الحصول على GTM ID
بعد إنشاء الـ Container، ستحصل على:
- **GTM ID** بصيغة: `GTM-XXXXXXX`
- احفظه، ستحتاجه في الخطوة التالية

---

## 🔧 الخطوة 2: إعداد المشروع

### إضافة GTM ID إلى ملف البيئة

افتح ملف `.env` في مجلد `frontend` وأضف:

```env
NUXT_PUBLIC_GTM_ID=GTM-XXXXXXX
```

استبدل `GTM-XXXXXXX` بالـ ID الحقيقي من Google Tag Manager.

---

## 📊 الخطوة 3: إعداد Google Analytics 4

### أ. إنشاء حساب GA4
1. اذهب إلى: [https://analytics.google.com](https://analytics.google.com)
2. انقر على **"Start measuring"**
3. أنشئ Property جديد
4. احصل على **Measurement ID** بصيغة: `G-XXXXXXXXXX`

### ب. ربط GA4 مع GTM

1. ارجع إلى Google Tag Manager
2. اذهب إلى **Tags** > **New**
3. اختر **Google Analytics: GA4 Configuration**
4. أدخل الـ **Measurement ID**
5. في **Triggering**، اختر **Consent Initialization - All Pages**
6. انقر على **Save**

### ج. إعداد Consent Mode في GA4 Tag

في نفس الـ Tag السابق:
1. اذهب إلى **Advanced Settings** > **Consent Settings**
2. فعّل **Consent Overview**
3. اضبط الإعدادات كالتالي:
   - **Built-In Variables**: ✅
   - **Analytics Storage**: Required
   - **Ad Storage**: Not Required

---

## 🎯 الخطوة 4: إعداد Triggers للـ Consent

### إنشاء Trigger للموافقة على الكوكيز

في Google Tag Manager:

1. اذهب إلى **Triggers** > **New**
2. اختر **Custom Event**
3. Event name: `consent_update`
4. احفظه باسم: `Cookie Consent Update`

### ربط GA4 بالـ Trigger

1. ارجع إلى GA4 Tag
2. في **Triggering**، أضف الـ Trigger الجديد
3. احفظ التغييرات

---

## ✅ الخطوة 5: الاختبار

### أ. اختبار محلي

1. شغّل المشروع:
```bash
npm run dev
```

2. افتح Developer Console في المتصفح:
```javascript
// تحقق من وجود dataLayer
console.log(window.dataLayer)

// يجب أن ترى:
// [{consent: "default", ...}, ...]
```

3. اقبل الكوكيز في البانر
4. تحقق من Console مرة أخرى:
```javascript
// يجب أن ترى:
// GTM initialized successfully
```

### ب. اختبار GTM Preview Mode

1. في Google Tag Manager، انقر على **Preview**
2. أدخل URL موقعك المحلي: `http://localhost:3000`
3. اقبل الكوكيز في البانر
4. في GTM Debugger، تحقق من:
   - ✅ Consent default loaded
   - ✅ GTM container loaded
   - ✅ consent_update event fired
   - ✅ GA4 tag fired

### ج. اختبار GA4 Real-time

1. اذهب إلى Google Analytics
2. افتح **Reports** > **Realtime**
3. افتح موقعك في تاب آخر
4. يجب أن ترى نفسك في التقرير

---

## 🔒 الخطوة 6: التحقق من Consent Mode v2

### استخدام Google Tag Assistant

1. ثبّت إضافة: [Tag Assistant](https://chrome.google.com/webstore/detail/tag-assistant-legacy-by-g/kejbdjndbnbjgmefkgdddjlbokphdefk)
2. افتح موقعك
3. انقر على الإضافة
4. يجب أن ترى:
   - ✅ **Consent Mode**: Active
   - ✅ **Default State**: All denied
   - ✅ **After consent**: Granted based on user choice

---

## 📱 الخطوة 7: النشر (Production)

### قبل النشر:

1. في GTM، انقر على **Submit**
2. أضف وصف للنسخة
3. انقر على **Publish**

### بعد النشر:

1. حدّث ملف `.env.production`:
```env
NUXT_PUBLIC_GTM_ID=GTM-XXXXXXX
NUXT_PUBLIC_API_BASE=https://yourdomain.com/api/v1
NUXT_PUBLIC_SITE_URL=https://yourdomain.com
```

2. أعد بناء المشروع:
```bash
npm run build
```

---

## 🎨 التخصيص المتقدم

### تتبع أحداث مخصصة

في أي مكان في كود Vue:

```typescript
<script setup lang="ts">
const { $gtm } = useNuxtApp()

// مثال: تتبع نقر زر
const trackButtonClick = () => {
  $gtm.push({
    event: 'button_click',
    button_name: 'subscribe',
    page_location: window.location.pathname
  })
}

// مثال: تتبع قراءة مقال
const trackArticleRead = (articleId: string, title: string) => {
  $gtm.push({
    event: 'article_read',
    article_id: articleId,
    article_title: title,
    read_percentage: 100
  })
}
</script>
```

### إنشاء Tags مخصصة في GTM

1. في GTM، اذهب إلى **Tags** > **New**
2. اختر **Custom HTML** أو **Custom Image**
3. أضف الكود المخصص
4. اختر Trigger: **Custom Event** بالاسم الذي استخدمته (مثل: `button_click`)
5. احفظ وانشر

---

## 🛡️ الخصوصية والأمان

### الملفات التي تم إنشاؤها:

1. **`plugins/google-consent.client.ts`**
   - يتحكم في Google Consent Mode v2
   - يُحمّل قبل GTM

2. **`plugins/gtm.client.ts`**
   - يُحمّل GTM فقط بعد موافقة المستخدم
   - يتتبع تغييرات الموافقة

3. **`composables/useCookieConsent.ts`**
   - يدير موافقة المستخدم في localStorage
   - يرسل إشارات إلى Google Consent Mode

4. **`components/CookieConsentBanner.vue`**
   - واجهة المستخدم للموافقة
   - متوافق مع GDPR

### الامتثال للقوانين:

✅ **GDPR** (الاتحاد الأوروبي)
✅ **CCPA** (كاليفورنيا)
✅ **LGPD** (البرازيل)
✅ Google Consent Mode v2

---

## 🐛 حل المشاكل الشائعة

### المشكلة: GTM لا يُحمّل

**الحل:**
1. تأكد من أن GTM ID صحيح في `.env`
2. تأكد من قبول الكوكيز في البانر
3. افتح Console وابحث عن أخطاء

### المشكلة: GA4 لا يتتبع الزيارات

**الحل:**
1. تأكد من أن Measurement ID صحيح في GTM
2. تحقق من أن GA4 Tag مرتبط بالـ Triggers الصحيحة
3. انتظر 24-48 ساعة لظهور البيانات

### المشكلة: Consent Mode لا يعمل

**الحل:**
1. تأكد من تحميل `google-consent.client.ts` قبل `gtm.client.ts`
2. افتح Console واكتب: `console.log(window.dataLayer)`
3. يجب أن ترى `consent: "default"` في أول عنصر

---

## 📚 موارد إضافية

- [Google Tag Manager Documentation](https://support.google.com/tagmanager)
- [Google Consent Mode v2](https://support.google.com/analytics/answer/9976101)
- [GA4 Documentation](https://support.google.com/analytics/answer/10089681)
- [GDPR Compliance](https://gdpr.eu/)

---

## ✨ ملخص سريع

```bash
# 1. أضف GTM ID في .env
NUXT_PUBLIC_GTM_ID=GTM-XXXXXXX

# 2. شغّل المشروع
npm run dev

# 3. اقبل الكوكيز في البانر
# 4. افتح GTM Preview Mode للاختبار
# 5. اربط GA4 في GTM
# 6. انشر التغييرات في GTM
```

**انتهى! 🎉**

الآن لديك نظام Cookie Consent متكامل مع Google Tag Manager و Consent Mode v2.
