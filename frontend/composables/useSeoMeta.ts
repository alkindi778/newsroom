/**
 * Composable موحد لإدارة SEO والميتا تاجز
 * يتبع أفضل ممارسات Nuxt 3
 */

interface SeoMetaOptions {
  title: string
  description: string
  image?: string
  url?: string
  type?: 'website' | 'article' | 'profile'
  author?: string
  publishedTime?: string
  modifiedTime?: string
  keywords?: string
  section?: string
  locale?: string
  siteName?: string
}

export const useAppSeoMeta = () => {
  const config = useRuntimeConfig()
  const route = useRoute()
  const settingsStore = useSettingsStore()

  /**
   * تعيين SEO Meta Tags للصفحة
   */
  const setSeoMeta = (options: SeoMetaOptions) => {
    const {
      title,
      description,
      image,
      url,
      type = 'website',
      author,
      publishedTime,
      modifiedTime,
      keywords,
      section,
      locale,
      siteName
    } = options

    // الحصول على البيانات من إعدادات Backend
    const defaultSiteName = settingsStore.getSetting('site_name')
    const finalSiteName = siteName || defaultSiteName
    
    const siteLocale = locale || settingsStore.getSetting('site_locale')
    const twitterHandle = settingsStore.getSetting('twitter_handle')
    const defaultOgImage = settingsStore.getSetting('default_og_image')

    // بناء الـ URL الكامل
    const siteUrl = (config as any).public.siteUrl
    const fullUrl = url ? `${siteUrl}${url}` : `${siteUrl}${route.fullPath}`
    
    // بناء الصورة الكاملة
    let fullImage: string
    
    if (image) {
      // إذا كانت الصورة URL كامل
      fullImage = image.startsWith('http') ? image : `${siteUrl}${image}`
    } else if (defaultOgImage) {
      // الصورة الافتراضية من Backend (تكون path نسبي أو URL كامل)
      fullImage = defaultOgImage.startsWith('http') 
        ? defaultOgImage 
        : defaultOgImage.startsWith('/') 
          ? `${siteUrl}${defaultOgImage}`
          : `${(config as any).public.apiBase.replace('/api/v1', '')}/storage/${defaultOgImage}`
    } else {
      // fallback أخير
      fullImage = `${siteUrl}/og-image.jpg`
    }

    // استخدام useSeoMeta للـ SEO tags
    useSeoMeta({
      title,
      description,
      keywords,
      author,
      
      // Open Graph
      ogTitle: title,
      ogDescription: description,
      ogImage: fullImage,
      ogImageAlt: title,
      ogUrl: fullUrl,
      ogType: type,
      ogSiteName: finalSiteName,
      ogLocale: siteLocale,
      
      // Twitter Card
      twitterCard: 'summary_large_image',
      twitterTitle: title,
      twitterDescription: description,
      twitterImage: fullImage,
      twitterImageAlt: title,
      twitterSite: twitterHandle,
      twitterCreator: twitterHandle,
      
      // Article specific (إذا كان مقال)
      ...(type === 'article' && {
        articlePublishedTime: publishedTime,
        articleModifiedTime: modifiedTime,
        articleAuthor: author ? [author] : undefined,
        articleSection: section
      })
    })

    // استخدام useHead للإعدادات الإضافية
    useHead({
      htmlAttrs: {
        lang: 'ar',
        dir: 'rtl'
      },
      link: [
        { rel: 'canonical', href: fullUrl }
      ]
    })
  }

  /**
   * SEO للصفحة الرئيسية
   */
  const setHomeSeoMeta = () => {
    const siteSlogan = settingsStore.getSetting('site_slogan')
    const siteDescription = settingsStore.getSetting('site_description')

    // استخدام "الرئيسية" للصفحة الرئيسية
    // سيتم إضافة اسم الموقع تلقائياً من titleTemplate: "الرئيسية - نبض الشارع"
    setSeoMeta({
      title: 'الرئيسية',
      description: siteDescription,
      url: '/',
      type: 'website'
    })
  }

  /**
   * SEO لصفحة المقال
   */
  const setArticleSeoMeta = (article: any) => {
    const siteName = settingsStore.getSetting('site_name')
    
    setSeoMeta({
      title: article.title,
      description: article.meta_description || article.excerpt,
      image: article.image,
      url: `/news/${article.slug}`,
      type: 'article',
      author: article.author?.name || siteName,
      publishedTime: article.published_at,
      modifiedTime: article.updated_at,
      keywords: Array.isArray(article.keywords) 
        ? article.keywords.join(', ') 
        : article.keywords,
      section: article.category?.name
    })
  }

  /**
   * SEO لصفحة مقال الرأي
   */
  const setOpinionSeoMeta = (opinion: any) => {
    setSeoMeta({
      title: opinion.title,
      description: opinion.meta_description || opinion.excerpt,
      image: opinion.image,
      url: `/opinions/${opinion.slug}`,
      type: 'article',
      author: opinion.writer?.name,
      publishedTime: opinion.published_at,
      modifiedTime: opinion.updated_at,
      keywords: opinion.keywords,
      section: 'مقالات الرأي'
    })
  }

  /**
   * SEO لصفحة القسم
   */
  const setCategorySeoMeta = (category: any) => {
    setSeoMeta({
      title: category.name,
      description: `تصفح آخر الأخبار والتحديثات في قسم ${category.name}`,
      url: `/category/${category.slug}`,
      type: 'website',
      section: category.name
    })
  }

  /**
   * SEO لصفحة الكاتب
   */
  const setWriterSeoMeta = (writer: any) => {
    setSeoMeta({
      title: `${writer.name} - كاتب الرأي`,
      description: writer.bio || `اقرأ جميع مقالات ${writer.name}`,
      image: writer.image,
      url: `/writers/${writer.slug}`,
      type: 'profile',
      author: writer.name
    })
  }

  return {
    setSeoMeta,
    setHomeSeoMeta,
    setArticleSeoMeta,
    setOpinionSeoMeta,
    setCategorySeoMeta,
    setWriterSeoMeta
  }
}
