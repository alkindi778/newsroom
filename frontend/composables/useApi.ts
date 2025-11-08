export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = ((config as any).public?.apiBase || 'http://localhost:8000/api') as string

  // دالة للاتصال بالـ API باستخدام $fetch
  const apiFetch = async <T>(url: string, options: any = {}): Promise<T | null> => {
    // إنشاء AbortController لإلغاء الطلبات
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 30000) // 30 ثانية

    try {
      // إضافة timestamp لتجنب caching عند الضرورة
      const urlWithTimestamp = options.noCache 
        ? `${url}${url.includes('?') ? '&' : '?'}_t=${Date.now()}`
        : url
      
      const result = await $fetch<T>(urlWithTimestamp, {
        baseURL,
        signal: controller.signal,
        ...options,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Cache-Control': 'no-cache',
          ...options.headers,
        },
        // زيادة timeout لتجنب abort errors
        timeout: 25000, // 25 ثانية
        // تفعيل retry للطلبات الفاشلة
        retry: 1,
        retryDelay: 1000,
        // معالجة الأخطاء
        onRequestError({ error }) {
          if (process.client) {
            console.warn(`[API] Request Error (${url}):`, error.message)
          }
        },
        onResponseError({ response }) {
          if (process.client && response.status !== 404) {
            console.warn(`[API] Response Error (${url}):`, response.status)
          }
        },
      }) as T

      clearTimeout(timeoutId)
      return result
    } catch (error: any) {
      clearTimeout(timeoutId)
      
      // عدم إيقاف التطبيق عند فشل الاتصال
      if (error.code === 'ECONNREFUSED' || error.code === 'ETIMEDOUT') {
        if (process.client) {
          console.warn(`[API] Backend is not available (${url})`)
        }
      } else if (error.name === 'AbortError' || error.message?.includes('aborted') || error.message?.includes('premature')) {
        if (process.client) {
          console.warn(`[API] Request aborted (${url}) - Connection closed`)
        }
      } else if (error.statusCode === 404) {
        // تجاهل أخطاء 404 بصمت
        return null
      } else {
        if (process.client) {
          console.error(`[API] Error (${url}):`, error.message || error)
        }
      }
      return null
    }
  }

  return {
    apiFetch,
    baseURL
  }
}
