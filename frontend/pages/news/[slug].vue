<template>
  <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 md:py-8">
    <!-- Loading State -->
    <LoadingSpinner v-if="loading" type="bars" size="lg" fullScreen text="جاري تحميل الخبر..." />

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h2>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <NuxtLink to="/" class="text-blue-600 hover:text-blue-700 font-semibold">
        العودة للرئيسية
      </NuxtLink>
    </div>

    <!-- Article Content -->
    <div v-else-if="article" class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
        <nav class="mb-4 md:mb-6 text-xs sm:text-sm px-2 sm:px-0">
        <ol class="flex items-center gap-1 sm:gap-2 text-gray-600">
          <li><NuxtLink to="/" class="hover:text-blue-600">الرئيسية</NuxtLink></li>
          <li>/</li>
          <li v-if="article.category">
            <NuxtLink :to="`/category/${article.category.slug}`" class="hover:text-blue-600">
              {{ article.category.name }}
            </NuxtLink>
          </li>
          <li v-if="article.category">/</li>
          <li class="text-gray-900 font-semibold truncate">{{ article.title }}</li>
        </ol>
      </nav>

      <!-- Homepage Top Advertisement (will also show here) -->
      <div class="my-8">
        <AdvertisementZone position="homepage_top" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Article Top Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_top" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- Article Header -->
      <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- القسم -->
        <NuxtLink 
          v-if="article.category" 
          :to="`/category/${article.category.slug}`"
          class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-br-lg font-semibold text-sm transition-colors"
        >
          {{ article.category.name }}
        </NuxtLink>

        <!-- العنوان -->
        <div class="p-4 sm:p-6 md:p-12">
          <!-- العنوان الفرعي -->
          <h2 v-if="article.subtitle" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl text-blue-600 mb-3 md:mb-4 leading-relaxed font-semibold">
            {{ article.subtitle }}
          </h2>

          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4 md:mb-6 leading-tight">
            {{ article.title }}
          </h1>

          <!-- المقتطف -->
          <p v-if="article.excerpt" class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-gray-700 mb-6 md:mb-8 leading-relaxed font-medium">
            {{ article.excerpt }}
          </p>

          <!-- معلومات الخبر -->
          <div class="flex flex-wrap items-center gap-4 text-base text-gray-600 pb-6 border-b border-gray-200">
            <!-- التاريخ -->
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ formatDate(article.published_at, 'full') }}</span>
            </div>
          </div>
        </div>

        <!-- الصورة الرئيسية -->
        <div class="w-full">
          <img 
            :src="getImageUrl(article.image)" 
            :alt="article.title"
            loading="eager"
            class="w-full h-auto object-contain"
          />
        </div>

        <!-- المحتوى -->
        <div class="p-4 sm:p-6 md:p-12 lg:p-16">
          <!-- مصدر الخبر -->
          <div v-if="article.source" class="mb-8 pb-4 border-b border-gray-200">
            <p class="text-lg font-medium text-cyan-600">{{ article.source }}</p>
          </div>

          <div 
            class="prose prose-sm sm:prose-base md:prose-lg lg:prose-xl max-w-none text-right leading-loose article-content"
            style="font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            v-html="article.content"
          ></div>

          <!-- Article Middle Advertisement -->
          <div class="my-12">
            <AdvertisementZone position="article_middle" page="article" :auto-rotate="false" :show-dots="false" />
          </div>

          <!-- أزرار المشاركة -->
          <div class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-4">شارك الخبر:</h3>
            <div class="flex flex-wrap items-center gap-3">
              <!-- فيسبوك -->
              <a 
                :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(articleUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 py-2 bg-[#1877F2] hover:bg-[#145dbf] text-white rounded-lg transition-colors duration-200"
                title="شارك على فيسبوك"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span class="text-sm font-semibold">فيسبوك</span>
              </a>

              <!-- تويتر/X -->
              <a 
                :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(articleUrl)}&text=${encodeURIComponent(article.title)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 py-2 bg-[#000000] hover:bg-[#333333] text-white rounded-lg transition-colors duration-200"
                title="شارك على X"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span class="text-sm font-semibold">X</span>
              </a>

              <!-- واتساب -->
              <a 
                :href="`https://api.whatsapp.com/send?text=${encodeURIComponent(article.title + ' - ' + articleUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 py-2 bg-[#25D366] hover:bg-[#1da851] text-white rounded-lg transition-colors duration-200"
                title="شارك على واتساب"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                <span class="text-sm font-semibold">واتساب</span>
              </a>

              <!-- تيليجرام -->
              <a 
                :href="`https://t.me/share/url?url=${encodeURIComponent(articleUrl)}&text=${encodeURIComponent(article.title)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 py-2 bg-[#0088cc] hover:bg-[#0077b5] text-white rounded-lg transition-colors duration-200"
                title="شارك على تيليجرام"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                <span class="text-sm font-semibold">تيليجرام</span>
              </a>

              <!-- نسخ الرابط -->
              <button 
                @click="copyArticleUrl"
                class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200"
                title="نسخ الرابط"
              >
                <svg v-if="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold">{{ copied ? 'تم النسخ!' : 'نسخ الرابط' }}</span>
              </button>
            </div>
          </div>

          <!-- الكلمات الدلالية -->
          <div v-if="article.keywords" class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-4">الكلمات الدلالية:</h3>
            <div class="flex flex-wrap gap-3">
              <NuxtLink 
                v-for="keyword in keywords" 
                :key="keyword"
                :to="`/search?q=${encodeURIComponent(keyword)}`"
                class="px-4 py-2 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-full text-base text-gray-700 transition-all duration-200 cursor-pointer inline-block"
              >
                #{{ keyword }}
              </NuxtLink>
            </div>
          </div>
        </div>
      </article>

      <!-- Article Bottom Advertisement -->
      <div class="my-8">
        <AdvertisementZone position="article_bottom" page="article" :auto-rotate="false" :show-dots="false" />
      </div>

      <!-- أخبار ذات صلة -->
      <section v-if="relatedArticles.length > 0" class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">أخبار ذات صلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NewsCard 
            v-for="relatedArticle in relatedArticles" 
            :key="relatedArticle.id" 
            :article="relatedArticle" 
          />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import AdvertisementZone from '~/components/AdvertisementZone.vue'

const route = useRoute()
const articlesStore = useArticlesStore()
const config = useRuntimeConfig()
const apiBase = ((config as any).public?.apiBase || '/api/v1') as string

const { getImageUrl } = useImageUrl()
const { formatDate } = useDateFormat()
const { setArticleSeoMeta } = useAppSeoMeta()
const { processArticleText } = useHtmlEntities()

const slug = computed(() => route.params.slug as string)

// جلب الخبر مع SSR
const { data: articleData, error: fetchError } = await useAsyncData(
  `article-${slug.value}`,
  async () => {
    const response = await $fetch<any>(`${apiBase}/articles/${slug.value}`)
    return response?.data || null
  },
  {
    watch: [slug]
  }
)

const article = computed(() => {
  const data = articleData.value
  
  if (!data) return data

  // معالجة HTML entities
  const processedData = processArticleText(data)
  
  // Debugging logs
  if (data && process.client) {
    console.log('🔍 Article Data:', {
      id: data.id,
      title: data.title,
      processed_title: processedData.title,
      image: data.image,
      content_length: data.content?.length || 0
    })
    
    // استخراج الصور من المحتوى
    const imgRegex = /<img[^>]+src="([^">]+)"/g
    const matches = [...(data.content || '').matchAll(imgRegex)]
    const imageSrcs = matches.map(m => m[1])
    
    console.log('🖼️ Images in content:', imageSrcs)
    console.log('📊 Total images:', imageSrcs.length)
    
    // فحص الصور بعد التحميل
    setTimeout(() => {
      const imgs = document.querySelectorAll('.article-content img')
      console.log('🎨 DOM Images after render:', imgs.length)
      imgs.forEach((img: any, index) => {
        console.log(`  Image ${index + 1}:`, {
          src: img.src,
          complete: img.complete,
          naturalWidth: img.naturalWidth,
          naturalHeight: img.naturalHeight,
          error: img.complete && img.naturalWidth === 0
        })
      })
    }, 1000)
  }
  
  return processedData
})

const loading = ref(false)
const error = computed(() => fetchError.value?.message || null)
const relatedArticles = computed(() => articlesStore.articles.slice(0, 3))

// الكلمات الدلالية
const keywords = computed(() => {
  if (!article.value?.keywords) return []
  // Check if keywords is already an array
  if (Array.isArray(article.value.keywords)) {
    return article.value.keywords
  }
  // If string, split it
  return article.value.keywords.split(',').map((k: string) => k.trim())
})

// رابط المقال
const articleUrl = computed(() => {
  if (process.client) {
    return window.location.href
  }
  // في حالة SSR، نستخدم siteUrl من config أو نبني الرابط النسبي
  const siteUrl = (config as any).public?.siteUrl || ''
  return siteUrl ? `${siteUrl}/news/${slug.value}` : `/news/${slug.value}`
})

// حالة نسخ الرابط
const copied = ref(false)

// دالة نسخ رابط المقال
const copyArticleUrl = async () => {
  if (process.client && navigator.clipboard) {
    try {
      await navigator.clipboard.writeText(articleUrl.value)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (err) {
      console.error('Failed to copy:', err)
    }
  }
}

// SEO Meta Tags - يتم تحديثها عند تغيير المقال
watchEffect(() => {
  if (article.value) {
    setArticleSeoMeta(article.value)
  }
})

// إضافة Schema مباشرة لضمان server-side rendering
useHead(() => {
  if (!article.value) return {}
  
  const config = useRuntimeConfig()
  const settingsStore = useSettingsStore()
  const siteUrl = (config as any).public.siteUrl
  const siteName = settingsStore.getSetting('site_name')
  const articleUrl = `${siteUrl}/news/${article.value.slug}`
  
  const imagePath = article.value.image || article.value.image_url
  let articleImage = ''
  if (imagePath) {
    if (imagePath.startsWith('http')) {
      articleImage = imagePath
    } else {
      const cleanPath = imagePath.startsWith('/storage/') ? imagePath.substring(9) : imagePath
      articleImage = `${siteUrl}/storage/${cleanPath}`
    }
  }

  const newsSchema: any = {
    '@context': 'https://schema.org',
    '@type': 'NewsArticle',
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': articleUrl
    },
    headline: article.value.title,
    description: article.value.meta_description || article.value.excerpt,
    datePublished: article.value.published_at,
    dateModified: article.value.updated_at || article.value.published_at,
    author: {
      '@type': 'NewsMediaOrganization',
      name: article.value.author?.name || siteName
    },
    publisher: {
      '@type': 'NewsMediaOrganization',
      name: siteName
    }
  }

  if (articleImage) {
    newsSchema.image = {
      '@type': 'ImageObject',
      url: articleImage
    }
  }

  const websiteSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: siteName,
    url: siteUrl,
    potentialAction: {
      '@type': 'SearchAction',
      target: {
        '@type': 'EntryPoint',
        urlTemplate: `${siteUrl}/search?q={search_term_string}`
      },
      'query-input': {
        '@type': 'PropertyValueSpecification',
        valueRequired: 'http://schema.org/True',  
        valueName: 'search_term_string'
      }
    }
  }

  return {
    script: [
      {
        type: 'application/ld+json',
        children: JSON.stringify(newsSchema)
      } as any,
      {
        type: 'application/ld+json', 
        children: JSON.stringify(websiteSchema)
      } as any
    ]
  }
})

// زيادة عدد المشاهدات وجلب أخبار ذات صلة بعد التحميل
onMounted(async () => {
  if (article.value) {
    // زيادة عدد المشاهدات
    articlesStore.incrementViews(article.value.id)
    
    // جلب أخبار ذات صلة
    if (article.value.category_id) {
      await articlesStore.fetchArticles({
        category: article.value.category_id,
        per_page: 3
      })
    }
  }
})

// Scroll to top عند تغيير الصفحة
watch(slug, () => {
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
</script>

<style scoped>
/* تنسيق المحتوى */
:deep(.prose) {
  color: #1f2937;
  line-height: 2;
  direction: rtl;
  text-align: right;
  font-size: 1.375rem;
}

:deep(.prose h1) {
  font-size: 2.75rem;
  font-weight: 800;
  color: #111827;
  margin-top: 3rem;
  margin-bottom: 2rem;
  line-height: 1.3;
  letter-spacing: -0.025em;
}

:deep(.prose h2) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #111827;
  margin-top: 3rem;
  margin-bottom: 1.5rem;
  line-height: 1.35;
  letter-spacing: -0.02em;
}

:deep(.prose h3) {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
  line-height: 1.4;
}

:deep(.prose h4) {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-top: 2rem;
  margin-bottom: 1rem;
  line-height: 1.45;
}

:deep(.prose p) {
  margin-bottom: 1.75rem;
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2.1;
  text-align: justify;
  word-spacing: 0.05em;
  letter-spacing: 0.01em;
  white-space: pre-line; /* للحفاظ على الفواصل في النصوص القديمة */
}

:deep(.prose p:first-of-type) {
  margin-top: 0;
  font-size: 1.5rem;
  line-height: 2;
  color: #111827;
}

:deep(.prose p + p) {
  margin-top: 0;
}

:deep(.prose strong) {
  font-weight: 700;
  color: #111827;
}

:deep(.prose em) {
  font-style: italic;
}

:deep(.prose a) {
  color: #2563eb;
  text-decoration: underline;
  transition: color 0.2s;
}

:deep(.prose a:hover) {
  color: #1d4ed8;
}

:deep(.prose ul) {
  list-style-type: disc;
  margin-right: 2rem;
  margin-bottom: 1.5rem;
}

:deep(.prose ul li) {
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2;
  margin-top: 0.75rem;
  padding-right: 0.5rem;
}

:deep(.prose ol) {
  list-style-type: decimal;
  margin-right: 2rem;
  margin-bottom: 2rem;
}

:deep(.prose ol li) {
  font-size: 1.375rem;
  color: #1f2937;
  line-height: 2;
  margin-top: 0.75rem;
  padding-right: 0.5rem;
}

:deep(.prose blockquote) {
  border-right: 4px solid #2563eb;
  padding-right: 1.5rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
  background-color: #eff6ff;
  border-radius: 0.5rem;
}

:deep(.prose blockquote p) {
  font-size: 1.5rem;
  color: #374151;
  font-style: italic;
  font-weight: 500;
  line-height: 2;
}

:deep(.prose img) {
  border-radius: 0.5rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  width: 100%;
}

:deep(.prose hr) {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
  border-color: #d1d5db;
}

:deep(.prose table) {
  width: 100%;
  margin-top: 2rem;
  margin-bottom: 2rem;
  border-collapse: collapse;
}

:deep(.prose th) {
  background-color: #f3f4f6;
  padding: 0.75rem 1rem;
  text-align: right;
  font-weight: 700;
  border: 1px solid #d1d5db;
}

:deep(.prose td) {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
}

:deep(.prose pre) {
  background-color: #111827;
  color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

:deep(.prose code) {
  background-color: #f3f4f6;
  color: #dc2626;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 1.125rem;
}
</style>
