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
          : `${siteUrl}/storage/${defaultOgImage}`
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
    const siteDescription = settingsStore.getSetting('site_description')
    const siteName = settingsStore.getSetting('site_name')

    // استخدام اسم الموقع فقط كعنوان للصفحة الرئيسية بدون أي إضافات
    const homeTitle = siteName || ''

    setSeoMeta({
      title: homeTitle,
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
    const siteLogo = settingsStore.getSetting('site_logo')
    const siteUrl = (config as any).public.siteUrl
    const baseApiUrl = (config as any).public.apiBase.replace('/api/v1', '')
    const articleUrl = `${siteUrl}/news/${article.slug}`

    const imagePath = article.image || article.image_url
    let articleImage = ''

    if (imagePath) {
      articleImage = imagePath.startsWith('http')
        ? imagePath
        : `${siteUrl}/storage/${imagePath}`
      console.log('Article Image Path:', imagePath, '-> Full URL:', articleImage)
    } else {
      console.log('No article image found, will use fallback')
    }

    let publisherLogo = ''
    if (siteLogo) {
      publisherLogo = siteLogo.startsWith('http')
        ? siteLogo
        : `${siteUrl}/storage/${siteLogo}`
    }

    setSeoMeta({
      title: article.title,
      description: article.meta_description || article.excerpt,
      image: articleImage,
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

    // Structured Data (JSON-LD) لمقالات الأخبار
    const newsArticleSchema: any = {
      '@context': 'https://schema.org',
      '@type': 'NewsArticle',
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': articleUrl
      },
      headline: article.title,
      datePublished: article.published_at,
      dateModified: article.updated_at || article.published_at,
      author: {
        '@type': 'Person',
        name: article.author?.name || siteName
      },
      publisher: {
        '@type': 'Organization',
        name: siteName
      }
    }

    if (articleImage) {
      newsArticleSchema.image = {
        '@type': 'ImageObject',
        url: articleImage
      }
    }

    if (publisherLogo) {
      newsArticleSchema.publisher.logo = {
        '@type': 'ImageObject',
        url: publisherLogo
      }
    }

    const breadcrumbItems: any[] = [
      {
        '@type': 'ListItem',
        position: 1,
        item: {
          '@id': siteUrl,
          name: siteName
        }
      }
    ]

    if (article.category) {
      breadcrumbItems.push({
        '@type': 'ListItem',
        position: 2,
        item: {
          '@id': `${siteUrl}/category/${article.category.slug}`,
          name: article.category.name
        }
      })
    }

    breadcrumbItems.push({
      '@type': 'ListItem',
      position: breadcrumbItems.length + 1,
      item: {
        '@id': articleUrl,
        name: article.title
      }
    })

    const breadcrumbSchema = {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbItems
    }

    useHead({
      script: [
        {
          type: 'application/ld+json',
          // استخدام children لتمرير JSON-LD، مع cast لتجاوز قيود النوع في TS
          children: JSON.stringify(newsArticleSchema)
        } as any,
        {
          type: 'application/ld+json',
          children: JSON.stringify(breadcrumbSchema)
        } as any
      ]
    })
  }

  /**
   * SEO لصفحة مقال الرأي
   */
  const setOpinionSeoMeta = (opinion: any) => {
    const siteName = settingsStore.getSetting('site_name')
    const siteLogo = settingsStore.getSetting('site_logo')
    const siteUrl = (config as any).public.siteUrl
    const baseApiUrl = (config as any).public.apiBase.replace('/api/v1', '')
    const opinionUrl = `${siteUrl}/opinions/${opinion.slug}`

    const imagePath = opinion.image || opinion.image_url
    let opinionImage = ''

    if (imagePath) {
      opinionImage = imagePath.startsWith('http')
        ? imagePath
        : `${siteUrl}/storage/${imagePath}`
    }

    let publisherLogo = ''
    if (siteLogo) {
      publisherLogo = siteLogo.startsWith('http')
        ? siteLogo
        : `${siteUrl}/storage/${siteLogo}`
    }

    setSeoMeta({
      title: opinion.title,
      description: opinion.meta_description || opinion.excerpt,
      image: opinion.image,
      url: `/opinions/${opinion.slug}`,
      type: 'article',
      author: opinion.writer?.name || siteName,
      publishedTime: opinion.published_at,
      modifiedTime: opinion.updated_at,
      keywords: opinion.keywords,
      section: 'مقالات الرأي'
    })

    const opinionArticleSchema: any = {
      '@context': 'https://schema.org',
      '@type': 'NewsArticle',
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': opinionUrl
      },
      headline: opinion.title,
      datePublished: opinion.published_at,
      dateModified: opinion.updated_at || opinion.published_at,
      author: {
        '@type': 'Person',
        name: opinion.writer?.name || siteName
      },
      publisher: {
        '@type': 'Organization',
        name: siteName
      }
    }

    if (opinionImage) {
      opinionArticleSchema.image = {
        '@type': 'ImageObject',
        url: opinionImage
      }
    }

    if (publisherLogo) {
      opinionArticleSchema.publisher.logo = {
        '@type': 'ImageObject',
        url: publisherLogo
      }
    }

    const breadcrumbItems: any[] = [
      {
        '@type': 'ListItem',
        position: 1,
        item: {
          '@id': siteUrl,
          name: siteName
        }
      },
      {
        '@type': 'ListItem',
        position: 2,
        item: {
          '@id': `${siteUrl}/opinions`,
          name: 'مقالات الرأي'
        }
      },
      {
        '@type': 'ListItem',
        position: 3,
        item: {
          '@id': opinionUrl,
          name: opinion.title
        }
      }
    ]

    const breadcrumbSchema = {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbItems
    }

    useHead({
      script: [
        {
          type: 'application/ld+json',
          children: JSON.stringify(opinionArticleSchema)
        } as any,
        {
          type: 'application/ld+json',
          children: JSON.stringify(breadcrumbSchema)
        } as any
      ]
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
