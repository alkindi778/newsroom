export default defineEventHandler(async (event) => {
  try {
    const config = useRuntimeConfig()
    
    // جلب البيانات من API
    const response = await $fetch(`${config.public.apiBaseUrl}/api/v1/manifest`)
    
    // إرجاع manifest.json مع headers صحيحة
    setHeader(event, 'Content-Type', 'application/manifest+json')
    setHeader(event, 'Cache-Control', 'public, max-age=3600') // cache لمدة ساعة
    
    return response
  } catch (error) {
    console.error('Error fetching manifest:', error)
    
    // fallback إلى قيم افتراضية
    return {
      name: 'Newsroom',
      short_name: 'Newsroom',
      description: 'News Platform',
      start_url: '/',
      display: 'standalone',
      background_color: '#ffffff',
      theme_color: '#000000',
      orientation: 'portrait',
      lang: 'ar',
      dir: 'rtl',
      icons: [
        {
          src: '/favicon.ico',
          sizes: '48x48',
          type: 'image/x-icon'
        },
        {
          src: '/icon-192x192.png',
          sizes: '192x192',
          type: 'image/png',
          purpose: 'any maskable'
        },
        {
          src: '/icon-512x512.png',
          sizes: '512x512',
          type: 'image/png',
          purpose: 'any maskable'
        },
        {
          src: '/badge-72x72.png',
          sizes: '72x72',
          type: 'image/png'
        }
      ],
      categories: ['news', 'media']
    }
  }
})
