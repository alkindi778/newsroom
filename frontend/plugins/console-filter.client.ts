export default defineNuxtPlugin(() => {
  if (process.client) {
    // حفظ console.warn الأصلي
    const originalWarn = console.warn
    const originalLog = console.log

    // تصفية تحذيرات معينة
    console.warn = function(...args: any[]) {
      const message = args.join(' ')
      
      // إخفاء تحذيرات Suspense
      if (message.includes('Suspense') && message.includes('experimental')) {
        return
      }
      
      // تمرير باقي التحذيرات
      originalWarn.apply(console, args)
    }

    // تصفية رسائل console.log
    console.log = function(...args: any[]) {
      const message = args.join(' ')
      
      // إخفاء رسائل Pinia store installed
      if (message.includes('store installed')) {
        return
      }

      // إخفاء رسائل Nuxt DevTools
      if (message.includes('Nuxt DevTools') || message.includes('Press Shift')) {
        return
      }
      
      // تمرير باقي الرسائل
      originalLog.apply(console, args)
    }
  }
})
