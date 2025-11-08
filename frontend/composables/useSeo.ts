/**
 * Composable لإدارة SEO المتقدم
 */
export const useSeo = () => {
  const config = useRuntimeConfig()
  const route = useRoute()

  /**
   * إنشاء meta tags كاملة للمقالة
   */
  const setArticleSeo = (article: any) => {
    const url = `${(config as any).public.siteUrl}${route.fullPath}`
    const imageUrl = article.image_url || article.image
    
    useSeoMeta({
      title: article.title,
      description: article.meta_description || article.excerpt || '',
      keywords: article.keywords,
      
      // Open Graph
      ogTitle: article.title,
      ogDescription: article.excerpt || article.meta_description || '',
      ogImage: imageUrl,
      ogImageAlt: article.title,
      ogUrl: url,
      ogType: 'article',
      ogSiteName: 'غرفة الأخبار',
      ogLocale: 'ar_SA',
      
      // Twitter Card
      twitterCard: 'summary_large_image',
      twitterTitle: article.title,
      twitterDescription: article.excerpt || article.meta_description || '',
      twitterImage: imageUrl,
      twitterImageAlt: article.title,
      
      // Article specific
      articlePublishedTime: article.published_at,
      articleModifiedTime: article.updated_at,
      articleAuthor: article.user?.name || 'غرفة الأخبار',
      articleSection: article.category?.name,
      articleTag: article.keywords
    })
  }

  /**
   * إنشاء meta tags لمقالة الرأي
   */
  const setOpinionSeo = (opinion: any) => {
    const url = `${(config as any).public.siteUrl}${route.fullPath}`
    const imageUrl = opinion.image_url || opinion.image
    
    useSeoMeta({
      title: opinion.title,
      description: opinion.meta_description || opinion.excerpt || '',
      keywords: opinion.keywords,
      author: opinion.writer?.name,
      
      // Open Graph
      ogTitle: opinion.title,
      ogDescription: opinion.excerpt || opinion.meta_description || '',
      ogImage: imageUrl,
      ogImageAlt: opinion.title,
      ogUrl: url,
      ogType: 'article',
      ogSiteName: 'غرفة الأخبار',
      ogLocale: 'ar_SA',
      
      // Twitter Card
      twitterCard: 'summary_large_image',
      twitterTitle: opinion.title,
      twitterDescription: opinion.excerpt || opinion.meta_description || '',
      twitterImage: imageUrl,
      twitterImageAlt: opinion.title,
      twitterCreator: opinion.writer?.twitter_url,
      
      // Article specific
      articlePublishedTime: opinion.published_at,
      articleModifiedTime: opinion.updated_at,
      articleAuthor: opinion.writer?.name,
      articleTag: opinion.keywords
    })
  }

  /**
   * إنشاء meta tags للصفحة الرئيسية
   */
  const setHomeSeo = () => {
    const settingsStore = useSettingsStore()
    const url = (config as any).public.siteUrl
    
    const siteName = settingsStore.getSetting('site_name', 'غرفة الأخبار')
    const siteSlogan = settingsStore.getSetting('site_slogan', '')
    const siteDescription = settingsStore.getSetting('site_description', 'منصة إخبارية شاملة')
    const siteKeywords = settingsStore.getSetting('site_keywords', 'أخبار، أخبار عربية')
    const seoDefaultImage = settingsStore.getSetting('seo_default_image', '/og-image.jpg')
    
    const title = siteSlogan ? `${siteName} - ${siteSlogan}` : `${siteName} - أحدث الأخبار والتحليلات`
    const ogImage = seoDefaultImage.startsWith('http') ? seoDefaultImage : `${url}/storage/${seoDefaultImage}`
    
    useSeoMeta({
      title: title,
      description: siteDescription,
      keywords: siteKeywords,
      
      // Open Graph
      ogTitle: title,
      ogDescription: siteDescription,
      ogImage: ogImage,
      ogUrl: url,
      ogType: 'website',
      ogSiteName: siteName,
      ogLocale: 'ar_SA',
      
      // Twitter Card
      twitterCard: 'summary_large_image',
      twitterTitle: title,
      twitterDescription: siteDescription,
      twitterImage: ogImage
    })
  }

  /**
   * إنشاء meta tags للتصنيف
   */
  const setCategorySeo = (category: any) => {
    const url = `${(config as any).public.siteUrl}/category/${category.slug}`
    
    useSeoMeta({
      title: `${category.name} - غرفة الأخبار`,
      description: `جميع الأخبار والمقالات في قسم ${category.name}. تابع آخر التحديثات والأحداث المتعلقة بـ ${category.name}.`,
      keywords: `${category.name}, أخبار ${category.name}`,
      
      // Open Graph
      ogTitle: `${category.name} - غرفة الأخبار`,
      ogDescription: `جميع الأخبار والمقالات في قسم ${category.name}`,
      ogUrl: url,
      ogType: 'website',
      ogSiteName: 'غرفة الأخبار',
      ogLocale: 'ar_SA',
      
      // Twitter Card
      twitterCard: 'summary',
      twitterTitle: `${category.name} - غرفة الأخبار`,
      twitterDescription: `جميع الأخبار والمقالات في قسم ${category.name}`
    })
  }

  /**
   * إنشاء meta tags لصفحة الكاتب
   */
  const setWriterSeo = (writer: any) => {
    const url = `${(config as any).public.siteUrl}/writers/${writer.slug}`
    
    useSeoMeta({
      title: `${writer.name} - كاتب في غرفة الأخبار`,
      description: writer.bio || `جميع مقالات الكاتب ${writer.name}`,
      author: writer.name,
      
      // Open Graph
      ogTitle: `${writer.name} - كاتب في غرفة الأخبار`,
      ogDescription: writer.bio || `جميع مقالات الكاتب ${writer.name}`,
      ogImage: writer.image_url,
      ogUrl: url,
      ogType: 'profile',
      ogSiteName: 'غرفة الأخبار',
      ogLocale: 'ar_SA',
      
      // Twitter Card
      twitterCard: 'summary',
      twitterTitle: `${writer.name} - كاتب في غرفة الأخبار`,
      twitterDescription: writer.bio || `جميع مقالات الكاتب ${writer.name}`,
      twitterImage: writer.image_url,
      twitterCreator: writer.twitter_url
    })
  }

  /**
   * إضافة canonical URL
   */
  const setCanonical = (path?: string) => {
    const url = path 
      ? `${(config as any).public.siteUrl}${path}`
      : `${(config as any).public.siteUrl}${route.fullPath}`
    
    useHead({
      link: [
        { rel: 'canonical', href: url }
      ]
    })
  }

  /**
   * إضافة alternate languages
   */
  const setAlternateLanguages = () => {
    useHead({
      link: [
        { rel: 'alternate', hreflang: 'ar', href: `${(config as any).public.siteUrl}${route.fullPath}` },
        { rel: 'alternate', hreflang: 'x-default', href: `${(config as any).public.siteUrl}${route.fullPath}` }
      ]
    })
  }

  /**
   * إضافة robots meta
   */
  const setRobots = (options: { index?: boolean; follow?: boolean } = {}) => {
    const { index = true, follow = true } = options
    
    const content = [
      index ? 'index' : 'noindex',
      follow ? 'follow' : 'nofollow'
    ].join(', ')
    
    useHead({
      meta: [
        { name: 'robots', content }
      ]
    })
  }

  return {
    setArticleSeo,
    setOpinionSeo,
    setHomeSeo,
    setCategorySeo,
    setWriterSeo,
    setCanonical,
    setAlternateLanguages,
    setRobots
  }
}
