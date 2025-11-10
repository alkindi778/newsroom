/**
 * Sitemap XML Generator
 * يولد خريطة الموقع تلقائياً لمحركات البحث
 */
export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.siteUrl
  const apiBase = config.public.apiBase

  try {
    // جلب الإعدادات للحصول على اسم الموقع
    const settingsResponse = await $fetch(`${apiBase}/settings`)
    const siteName = (settingsResponse as any).data?.flat?.site_name || ''

    // جلب جميع الأخبار المنشورة
    const articlesResponse = await $fetch(`${apiBase}/articles`, {
      params: {
        per_page: 1000,
        is_published: 1
      }
    })

    // جلب الأقسام
    const categoriesResponse = await $fetch(`${apiBase}/categories`)

    const articles = (articlesResponse as any).data || []
    const categories = (categoriesResponse as any).data || []

    // بناء XML
    const urls = [
      // الصفحة الرئيسية
      {
        loc: baseUrl,
        changefreq: 'hourly',
        priority: 1.0,
        lastmod: new Date().toISOString()
      },
      // صفحة الأخبار
      {
        loc: `${baseUrl}/news`,
        changefreq: 'hourly',
        priority: 0.9,
        lastmod: new Date().toISOString()
      },
      // صفحة مقالات الرأي
      {
        loc: `${baseUrl}/opinions`,
        changefreq: 'daily',
        priority: 0.8,
        lastmod: new Date().toISOString()
      },
      // صفحة الكُتاب
      {
        loc: `${baseUrl}/writers`,
        changefreq: 'weekly',
        priority: 0.7,
        lastmod: new Date().toISOString()
      },
      // صفحة من نحن
      {
        loc: `${baseUrl}/about`,
        changefreq: 'monthly',
        priority: 0.5,
        lastmod: new Date().toISOString()
      },
      // صفحة اتصل بنا
      {
        loc: `${baseUrl}/contact`,
        changefreq: 'monthly',
        priority: 0.5,
        lastmod: new Date().toISOString()
      }
    ]

    // إضافة الأقسام
    categories.forEach((category: any) => {
      urls.push({
        loc: `${baseUrl}/category/${category.slug}`,
        changefreq: 'daily',
        priority: 0.8,
        lastmod: category.updated_at || new Date().toISOString()
      })
    })

    // إضافة الأخبار
    const newsUrls = articles.map((article: any) => {
      // الحصول على الصورة المميزة
      let imageUrl = null
      if (article.featured_image) {
        if (article.featured_image.startsWith('http')) {
          imageUrl = article.featured_image
        } else {
          // إزالة "media/" من بداية المسار إذا كان موجوداً
          const imagePath = article.featured_image.replace(/^media\//, '')
          imageUrl = `${apiBase.replace('/api/v1', '')}/storage/${imagePath}`
        }
      } else if (article.image) {
        if (article.image.startsWith('http')) {
          imageUrl = article.image
        } else {
          // إزالة "media/" من بداية المسار إذا كان موجوداً
          const imagePath = article.image.replace(/^media\//, '')
          imageUrl = `${apiBase.replace('/api/v1', '')}/storage/${imagePath}`
        }
      }

      return {
        loc: `${baseUrl}/news/${article.slug}`,
        changefreq: 'weekly',
        priority: 0.7,
        lastmod: article.updated_at || article.published_at || new Date().toISOString(),
        title: article.title,
        image: imageUrl,
        imageCaption: article.subtitle || article.excerpt,
        isNews: true
      }
    })

    urls.push(...newsUrls)

    // توليد XML
    const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
${urls.map((url: any) => {
      let xmlUrl = `  <url>
    <loc>${url.loc}</loc>
    <lastmod>${url.lastmod}</lastmod>
    <changefreq>${url.changefreq}</changefreq>
    <priority>${url.priority}</priority>`

      // إضافة News Sitemap Tags للمقالات
      if (url.isNews && url.title) {
        const escapedTitle = url.title.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        const escapedSiteName = siteName.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        xmlUrl += `
    <news:news>
      <news:publication>
        <news:name>${escapedSiteName}</news:name>
        <news:language>ar</news:language>
      </news:publication>
      <news:publication_date>${url.lastmod}</news:publication_date>
      <news:title>${escapedTitle}</news:title>
    </news:news>`
      }

      // إضافة Image Sitemap Tags
      if (url.image) {
        const escapedTitle = url.title ? url.title.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') : ''
        const escapedCaption = url.imageCaption ? url.imageCaption.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') : ''
        
        xmlUrl += `
    <image:image>
      <image:loc>${url.image}</image:loc>`
        
        if (escapedTitle) {
          xmlUrl += `
      <image:title>${escapedTitle}</image:title>`
        }
        
        if (escapedCaption) {
          xmlUrl += `
      <image:caption>${escapedCaption}</image:caption>`
        }
        
        xmlUrl += `
    </image:image>`
      }

      xmlUrl += `
  </url>`
      
      return xmlUrl
    }).join('\n')}
</urlset>`

    // تعيين headers
    setHeader(event, 'Content-Type', 'application/xml')
    setHeader(event, 'Cache-Control', 'public, max-age=3600') // Cache لمدة ساعة

    return sitemap
  } catch (error) {
    console.error('Error generating sitemap:', error)
    
    // إرجاع sitemap أساسي في حالة الخطأ
    const basicSitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>${baseUrl}</loc>
    <lastmod>${new Date().toISOString()}</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>`

    setHeader(event, 'Content-Type', 'application/xml')
    return basicSitemap
  }
})
