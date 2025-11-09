export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const { canUseAnalytics } = useCookieConsentState()

  // Google Tag Manager ID (you should add this to .env)
  const gtmId = (config as any).public?.gtmId || 'GTM-XXXXXXX'

  // Initialize dataLayer
  if (process.client && !window.dataLayer) {
    window.dataLayer = window.dataLayer || []
  }

  // Function to push events to dataLayer
  const gtag = (...args: any[]) => {
    if (process.client && window.dataLayer) {
      window.dataLayer.push(args)
    }
  }

  // Initialize GTM only if analytics consent is given
  const initGTM = () => {
    if (!canUseAnalytics.value) {
      console.log('GTM not initialized: Analytics consent not given')
      return
    }

    if (process.client && !document.querySelector(`script[src*="googletagmanager.com/gtm.js?id=${gtmId}"]`)) {
      // Add GTM script
      const script = document.createElement('script')
      script.async = true
      script.src = `https://www.googletagmanager.com/gtm.js?id=${gtmId}`
      document.head.appendChild(script)

      // Add GTM noscript iframe
      const noscript = document.createElement('noscript')
      const iframe = document.createElement('iframe')
      iframe.src = `https://www.googletagmanager.com/ns.html?id=${gtmId}`
      iframe.height = '0'
      iframe.width = '0'
      iframe.style.display = 'none'
      iframe.style.visibility = 'hidden'
      noscript.appendChild(iframe)
      document.body.insertBefore(noscript, document.body.firstChild)

      // Initialize dataLayer
      window.dataLayer = window.dataLayer || []
      window.dataLayer.push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      })

      console.log('GTM initialized successfully')
    }
  }

  // Initialize GTM if consent is already given
  if (canUseAnalytics.value) {
    initGTM()
  }

  // Listen for consent changes
  if (process.client) {
    watch(canUseAnalytics, (newValue) => {
      if (newValue) {
        initGTM()
      } else {
        // Disable GTM tracking
        if (window.dataLayer) {
          window.dataLayer.push({
            event: 'consent_revoked',
            analytics_consent: false
          })
        }
      }
    })
  }

  // Provide gtag function globally
  return {
    provide: {
      gtag,
      gtm: {
        init: initGTM,
        push: (...args: any[]) => {
          if (process.client && window.dataLayer && canUseAnalytics.value) {
            window.dataLayer.push(...args)
          }
        }
      }
    }
  }
})

// Type augmentation for window
declare global {
  interface Window {
    dataLayer: any[]
  }
}
