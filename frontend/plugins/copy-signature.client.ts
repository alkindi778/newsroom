/**
 * Copy Text Plugin
 * يضيف توقيع تلقائي عند نسخ أي نص من الموقع
 */

export default defineNuxtPlugin(() => {
    // التنفيذ فقط في المتصفح
    if (!import.meta.client) return

    // معالج حدث النسخ
    const handleCopy = (e: ClipboardEvent) => {
        const selection = window.getSelection()

        // تجاهل إذا لم يكن هناك نص محدد أو النص قصير جداً
        if (!selection || selection.toString().trim().length < 20) return

        // الحصول على النص المحدد
        const selectedText = selection.toString().trim()

        // الحصول على الرابط الحالي
        const currentUrl = window.location.href

        // بناء النص مع التوقيع
        const signature = `

_إعلام انتقالـﮯ العاصمة #عدن_
${currentUrl}

صفحتنا على الفيس بوك
www.facebook.com/adenstc1

صفحتنا على تويتر
www.twitter.com/qpwVSfBG8b8pMI1`

        const textWithSignature = `${selectedText}
${signature}`

        // منع السلوك الافتراضي
        e.preventDefault()

        // تعيين النص الجديد في الـ clipboard
        if (e.clipboardData) {
            e.clipboardData.setData('text/plain', textWithSignature)
        }
    }

    // إضافة مستمع الحدث عند تحميل الصفحة
    if (typeof document !== 'undefined') {
        document.addEventListener('copy', handleCopy)
    }
})
