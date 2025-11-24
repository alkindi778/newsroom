/**
 * ✨ Nuxt 4.2: Performance Monitoring
 * مراقبة أداء التطبيق
 */

export const usePerformance = () => {
  /**
   * قياس وقت تحميل الصفحة
   */
  const measurePageLoad = () => {
    if (process.client && window.performance) {
      const perfData = window.performance.timing
      const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart
      const connectTime = perfData.responseEnd - perfData.requestStart
      const renderTime = perfData.domComplete - perfData.domLoading

      console.log('📊 Performance Metrics:', {
        pageLoadTime: `${pageLoadTime}ms`,
        connectTime: `${connectTime}ms`,
        renderTime: `${renderTime}ms`
      })

      return {
        pageLoadTime,
        connectTime,
        renderTime
      }
    }
    return null
  }

  /**
   * قياس وقت تنفيذ function
   */
  const measureFunction = async (name: string, fn: Function) => {
    const start = performance.now()
    const result = await fn()
    const end = performance.now()
    const duration = end - start


    
    return { result, duration }
  }

  /**
   * Web Vitals Monitoring
   */
  const measureWebVitals = () => {
    if (process.client) {
      // Largest Contentful Paint (LCP)
      new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {

        }
      }).observe({ entryTypes: ['largest-contentful-paint'] })

      // First Input Delay (FID)
      new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {

        }
      }).observe({ entryTypes: ['first-input'] })

      // Cumulative Layout Shift (CLS)
      new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {

        }
      }).observe({ entryTypes: ['layout-shift'] })
    }
  }

  return {
    measurePageLoad,
    measureFunction,
    measureWebVitals
  }
}
