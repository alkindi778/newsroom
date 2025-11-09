/**
 * Google Consent Mode v2 Plugin
 * 
 * يوفر دعم لـ Google Consent Mode v2 للامتثال لقوانين الخصوصية GDPR
 * يجب تحميله قبل Google Tag Manager
 */

export default defineNuxtPlugin(() => {
  if (!process.client) return

  // Initialize gtag function
  function gtag(...args: any[]) {
    window.dataLayer = window.dataLayer || []
    window.dataLayer.push(arguments)
  }

  // Set default consent state (denied by default)
  gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'denied',
    'personalization_storage': 'denied',
    'security_storage': 'granted', // Always granted for security
    'wait_for_update': 500 // Wait 500ms for user consent before loading
  })

  // Set additional consent parameters for EEA/UK users
  gtag('set', 'ads_data_redaction', true)
  gtag('set', 'url_passthrough', true)

  // Provide consent update function
  return {
    provide: {
      updateConsent: (preferences: {
        analytics: boolean
        marketing: boolean
        preferences: boolean
      }) => {
        gtag('consent', 'update', {
          'ad_storage': preferences.marketing ? 'granted' : 'denied',
          'ad_user_data': preferences.marketing ? 'granted' : 'denied',
          'ad_personalization': preferences.marketing ? 'granted' : 'denied',
          'analytics_storage': preferences.analytics ? 'granted' : 'denied',
          'functionality_storage': 'granted',
          'personalization_storage': preferences.preferences ? 'granted' : 'denied',
        })

        // Push event to dataLayer
        window.dataLayer = window.dataLayer || []
        window.dataLayer.push({
          event: 'consent_update',
          consent_preferences: preferences
        })
      }
    }
  }
})

// Type augmentation
declare global {
  interface Window {
    dataLayer: any[]
  }
}
