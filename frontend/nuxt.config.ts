// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxtjs/i18n'
  ],

  // إعدادات الترجمة
  i18n: {
    locales: [
      { code: 'ar', iso: 'ar', file: 'ar.json', name: 'العربية', dir: 'rtl' },
      { code: 'en', iso: 'en-US', file: 'en.json', name: 'English', dir: 'ltr' }
    ],
    defaultLocale: 'ar',
    langDir: 'locales',
    strategy: 'prefix_except_default', // ar (default) -> /, en -> /en
    detectBrowserLanguage: false, // إيقاف الكشف التلقائي - دائماً العربية
    vueI18n: './i18n.config.ts' // ملف تكوين إضافي إذا لزم الأمر
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
    // إزالة console.log في الـ production
    esbuild: {
      drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
    },
    server: {
      // تقليل fetch errors أثناء التطوير
      hmr: {
        overlay: false
      },
      // Proxy للـ storage وAPI في بيئة التطوير
      proxy: {
        '/storage': {
          target: 'http://localhost/newsroom/backend/public',
          changeOrigin: true,
          secure: false
        },
        '/api/v1': {
          target: process.env.NUXT_BACKEND_URL || 'http://localhost/newsroom/backend/public',
          changeOrigin: true,
          secure: false
        }
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

  // Runtime config للاتصال بالـ Backend
  runtimeConfig: {
    // Server-side only (private)
    geminiApiKey: process.env.GEMINI_API_KEY,

    public: {
      // استخدام IP الجهاز للوصول من الهاتف أو localhost للتطوير
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost/newsroom/backend/public/api/v1',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'https://adenlink.cloud',
      // Google Tag Manager ID
      gtmId: process.env.NUXT_PUBLIC_GTM_ID || 'GTM-XXXXXXX'
    }
  },

  // إعدادات التطبيق - الحد الأدنى فقط، الباقي من Backend
  // ملاحظة: lang و dir يتم تحديدهما ديناميكياً في app.vue
  app: {
    head: {
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1',
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