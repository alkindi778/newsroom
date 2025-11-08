/**
 * Composable لإنشاء Schema.org markup
 */
export const useSeoSchema = () => {
  const config = useRuntimeConfig()
  const route = useRoute()
  const settingsStore = useSettingsStore()

  /**
   * Schema للمقالة (NewsArticle)
   */
  const createArticleSchema = (article: any) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'NewsArticle',
      headline: article.title,
      description: article.excerpt || article.meta_description,
      image: article.image_url || article.image,
      datePublished: article.published_at || article.created_at,
      dateModified: article.updated_at,
      author: {
        '@type': 'Person',
        name: article.user?.name || 'غرفة الأخبار'
      },
      publisher: {
        '@type': 'Organization',
        name: 'غرفة الأخبار',
        logo: {
          '@type': 'ImageObject',
          url: `${(config as any).public.siteUrl}/logo.png`
        }
      },
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': `${(config as any).public.siteUrl}${route.fullPath}`
      },
      articleSection: article.category?.name,
      keywords: article.keywords,
      inLanguage: 'ar'
    }
  }

  /**
   * Schema لمقالة الرأي (OpinionNewsArticle)
   */
  const createOpinionSchema = (opinion: any) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'OpinionNewsArticle',
      headline: opinion.title,
      description: opinion.excerpt || opinion.meta_description,
      image: opinion.image_url || opinion.image,
      datePublished: opinion.published_at || opinion.created_at,
      dateModified: opinion.updated_at,
      author: {
        '@type': 'Person',
        name: opinion.writer?.name,
        description: opinion.writer?.bio,
        image: opinion.writer?.image_url
      },
      publisher: {
        '@type': 'Organization',
        name: 'غرفة الأخبار',
        logo: {
          '@type': 'ImageObject',
          url: `${(config as any).public.siteUrl}/logo.png`
        }
      },
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': `${(config as any).public.siteUrl}${route.fullPath}`
      },
      keywords: opinion.keywords,
      inLanguage: 'ar'
    }
  }

  /**
   * Schema للمنظمة (Organization)
   */
  const createOrganizationSchema = () => {
    const siteName = settingsStore.getSetting('site_name', 'غرفة الأخبار')
    const siteNameEn = settingsStore.getSetting('site_name_en', 'Newsroom')
    const siteSlogan = settingsStore.getSetting('site_slogan', 'نبض الشارع - أخبارك من المصدر')
    const siteDescription = settingsStore.getSetting('site_description', 'منصة إخبارية شاملة تقدم أحدث الأخبار والتحليلات من مصادر موثوقة')
    const siteLogo = settingsStore.getSetting('site_logo', '/logo.png')
    
    const foundingDate = settingsStore.getSetting('org_founding_date', '2024')
    const areaServed = settingsStore.getSetting('org_area_served', 'اليمن')
    const addressCountry = settingsStore.getSetting('org_address_country', 'YE')
    const addressCity = settingsStore.getSetting('org_address_city', 'عدن')
    
    const contactPhone = settingsStore.getSetting('contact_phone', '+967-xxx-xxx-xxx')
    
    const socialLinks = [
      settingsStore.getSetting('social_facebook'),
      settingsStore.getSetting('social_twitter'),
      settingsStore.getSetting('social_instagram'),
      settingsStore.getSetting('social_youtube'),
      settingsStore.getSetting('social_telegram'),
      settingsStore.getSetting('social_tiktok')
    ].filter(Boolean)
    
    return {
      '@context': 'https://schema.org',
      '@type': 'NewsMediaOrganization',
      name: siteName,
      alternateName: [siteNameEn, siteSlogan],
      url: (config as any).public.siteUrl,
      logo: {
        '@type': 'ImageObject',
        url: `${(config as any).public.siteUrl}${siteLogo}`,
        width: 250,
        height: 60
      },
      description: siteDescription,
      slogan: siteSlogan,
      foundingDate: foundingDate,
      areaServed: areaServed,
      sameAs: socialLinks,
      contactPoint: {
        '@type': 'ContactPoint',
        telephone: contactPhone,
        contactType: 'customer service',
        availableLanguage: ['Arabic', 'ar'],
        areaServed: addressCountry
      },
      address: {
        '@type': 'PostalAddress',
        addressCountry: addressCountry,
        addressLocality: addressCity
      }
    }
  }

  /**
   * Schema للموقع (WebSite)
   */
  const createWebSiteSchema = () => {
    const siteName = settingsStore.getSetting('site_name', 'غرفة الأخبار')
    
    return {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      name: siteName,
      url: (config as any).public.siteUrl,
      potentialAction: {
        '@type': 'SearchAction',
        target: `${(config as any).public.siteUrl}/search?q={search_term_string}`,
        'query-input': 'required name=search_term_string'
      }
    }
  }

  /**
   * Schema لصفحة الكاتب (Person)
   */
  const createWriterSchema = (writer: any) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'Person',
      name: writer.name,
      description: writer.bio,
      image: writer.image_url,
      jobTitle: writer.specialization || 'كاتب',
      url: `${(config as any).public.siteUrl}/writers/${writer.slug}`,
      sameAs: [
        writer.facebook_url,
        writer.twitter_url,
        writer.instagram_url,
        writer.linkedin_url
      ].filter(Boolean)
    }
  }

  /**
   * Schema لقائمة الأخبار (ItemList)
   */
  const createArticleListSchema = (articles: any[]) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'ItemList',
      itemListElement: articles.map((article, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        item: {
          '@type': 'NewsArticle',
          headline: article.title,
          image: article.image_url || article.image,
          datePublished: article.published_at,
          author: {
            '@type': 'Person',
            name: article.user?.name || 'غرفة الأخبار'
          },
          url: `${(config as any).public.siteUrl}/news/${article.slug}`
        }
      }))
    }
  }

  /**
   * Schema للتصنيف (CollectionPage)
   */
  const createCategorySchema = (category: any, articles: any[]) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'CollectionPage',
      name: category.name,
      description: `جميع الأخبار في قسم ${category.name}`,
      url: `${(config as any).public.siteUrl}/category/${category.slug}`,
      mainEntity: {
        '@type': 'ItemList',
        itemListElement: articles.map((article, index) => ({
          '@type': 'ListItem',
          position: index + 1,
          url: `${(config as any).public.siteUrl}/news/${article.slug}`
        }))
      }
    }
  }

  /**
   * Schema لـ Breadcrumb
   */
  const createBreadcrumbSchema = (items: Array<{ name: string; url: string }>) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: items.map((item, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        name: item.name,
        item: `${(config as any).public.siteUrl}${item.url}`
      }))
    }
  }

  /**
   * إضافة Schema إلى الصفحة
   */
  const setSchema = (schema: any) => {
    useHead({
      script: [
        {
          type: 'application/ld+json',
          innerHTML: JSON.stringify(schema)
        }
      ]
    })
  }

  /**
   * إضافة عدة schemas
   */
  const setMultipleSchemas = (schemas: any[]) => {
    useHead({
      script: schemas.map(schema => ({
        type: 'application/ld+json',
        innerHTML: JSON.stringify(schema)
      }))
    })
  }

  return {
    createArticleSchema,
    createOpinionSchema,
    createOrganizationSchema,
    createWebSiteSchema,
    createWriterSchema,
    createArticleListSchema,
    createCategorySchema,
    createBreadcrumbSchema,
    setSchema,
    setMultipleSchemas
  }
}
