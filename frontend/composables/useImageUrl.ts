export const useImageUrl = () => {
  const config = useRuntimeConfig()
  
  // الحصول على رابط الصورة الكامل
  const getImageUrl = (imagePath?: string, size: 'thumbnail' | 'medium' | 'large' | 'original' = 'original'): string => {
    if (!imagePath) {
      return '/images/placeholder.jpg'
    }
    
    // إذا كان رابط كامل (من Backend asset() أو Media Library getUrl())
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath
    }

    // الحصول على base URL ديناميكياً من config
    const apiBase = (config as any).public?.apiBase || ''
    const baseUrl = apiBase.replace('/api/v1', '').replace('/api', '')

    // إذا كان رابط نسبي يبدأ بـ /
    if (imagePath.startsWith('/')) {
      return `${baseUrl}${imagePath}`
    }

    // رابط افتراضي - إضافة /storage/ للمسارات البسيطة
    return `${baseUrl}/storage/${imagePath}`
  }

  // صورة placeholder
  const placeholderImage = '/images/placeholder.jpg'

  return {
    getImageUrl,
    placeholderImage
  }
}
