// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },
  
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt'
  ],

  // إعدادات Pinia
  pinia: {
    storesDirs: ['./stores/**'],
  },

  // إخفاء تحذيرات Vue
  vue: {
    compilerOptions: {
      isCustomElement: (tag) => false,
    },
  },

  // إعدادات build
  vite: {
    vue: {
      script: {
        defineModel: true,
      },
    },
    server: {
      // تقليل fetch errors أثناء التطوير
      hmr: {
        overlay: false
      }
    }
  },

  // إعدادات Nitro للتحكم في fetch behavior
  nitro: {
    // تحسين معالجة الأخطاء
    experimental: {
      asyncContext: true
    },
    // Route rules لتحسين الأداء
    routeRules: {
      '/api/**': { 
        cors: true,
        headers: {
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Methods': 'GET,HEAD,PUT,PATCH,POST,DELETE',
          'Access-Control-Allow-Headers': 'Content-Type'
        }
      }
    },
    // Suppress common development errors
    logLevel: 'warn'
  },

  // ✨ Nuxt 4.2: ميزات تجريبية جديدة
  experimental: {
    payloadExtraction: false,
    // ✨ TypeScript Plugin للـ IDE محسّن
    typescriptPlugin: true,
    // ✨ Async Data Handler Extraction (للـ Static sites)
    extractAsyncDataHandlers: false, // فعّل عند استخدام generate
    // ✨ Vite Environment API (تجريبي)
    viteEnvironmentApi: false, // للتجربة في المستقبل
  },

  // تحسين معالجة الأخطاء
  devServer: {
    port: 3000,
    host: '0.0.0.0'
  },

  // TailwindCSS configuration
  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: 'tailwind.config.js',
    exposeConfig: false,
    viewer: true,
  },

  // CSS Files
  css: [
    '@fortawesome/fontawesome-free/css/all.min.css'
  ],

  // Runtime config للاتصال بالـ Backend
  runtimeConfig: {
    public: {
      // استخدام IP الجهاز للوصول من الهاتف
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://192.168.1.107/newsroom/backend/public/api/v1',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'http://192.168.1.107:3000',
      // Google Tag Manager ID
      gtmId: process.env.NUXT_PUBLIC_GTM_ID || 'GTM-XXXXXXX'
    }
  },

  // إعدادات التطبيق - الحد الأدنى فقط، الباقي من Backend
  app: {
    head: {
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1',
      htmlAttrs: {
        lang: 'ar',
        dir: 'rtl'
      },
      meta: [
        // Meta tags أساسية فقط
        { name: 'format-detection', content: 'telephone=no' },
      ],
      link: [
        // Links أساسية فقط
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  }
})