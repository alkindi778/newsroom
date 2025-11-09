import { ref, computed } from 'vue'

export type CookieConsentType = 'necessary' | 'analytics' | 'marketing' | 'preferences'

interface CookiePreferences {
  necessary: boolean
  analytics: boolean
  marketing: boolean
  preferences: boolean
}

const COOKIE_CONSENT_KEY = 'cookie_consent'
const COOKIE_CONSENT_TIMESTAMP = 'cookie_consent_timestamp'
const CONSENT_EXPIRY_DAYS = 365

export const useCookieConsent = () => {
  const cookieConsent = ref<CookiePreferences>({
    necessary: true, // Always true
    analytics: false,
    marketing: false,
    preferences: false,
  })

  const hasConsent = ref(false)
  const showBanner = ref(false)

  /**
   * Load consent from localStorage
   */
  const loadConsent = () => {
    if (process.client) {
      const stored = localStorage.getItem(COOKIE_CONSENT_KEY)
      const timestamp = localStorage.getItem(COOKIE_CONSENT_TIMESTAMP)
      
      if (stored && timestamp) {
        const consentDate = new Date(timestamp)
        const now = new Date()
        const daysDiff = Math.floor((now.getTime() - consentDate.getTime()) / (1000 * 60 * 60 * 24))
        
        // Check if consent is still valid
        if (daysDiff < CONSENT_EXPIRY_DAYS) {
          cookieConsent.value = JSON.parse(stored)
          hasConsent.value = true
          showBanner.value = false
          return
        }
      }
      
      // No valid consent found
      showBanner.value = true
      hasConsent.value = false
    }
  }

  /**
   * Save consent to localStorage
   */
  const saveConsent = (preferences: CookiePreferences) => {
    if (process.client) {
      cookieConsent.value = { ...preferences, necessary: true }
      localStorage.setItem(COOKIE_CONSENT_KEY, JSON.stringify(cookieConsent.value))
      localStorage.setItem(COOKIE_CONSENT_TIMESTAMP, new Date().toISOString())
      hasConsent.value = true
      showBanner.value = false
      
      // Update Google Consent Mode v2
      if (typeof window !== 'undefined') {
        try {
          const nuxtApp = useNuxtApp()
          const updateConsent = (nuxtApp as any).$updateConsent
          if (typeof updateConsent === 'function') {
            updateConsent({
              analytics: preferences.analytics,
              marketing: preferences.marketing,
              preferences: preferences.preferences
            })
          }
        } catch (error) {
          console.warn('Failed to update consent mode:', error)
        }
      }
      
      // Trigger Google Tag Manager event
      if (window.dataLayer) {
        window.dataLayer.push({
          event: 'cookie_consent_update',
          cookie_preferences: cookieConsent.value
        })
      }
    }
  }

  /**
   * Accept all cookies
   */
  const acceptAll = () => {
    saveConsent({
      necessary: true,
      analytics: true,
      marketing: true,
      preferences: true,
    })
  }

  /**
   * Accept only necessary cookies
   */
  const acceptNecessary = () => {
    saveConsent({
      necessary: true,
      analytics: false,
      marketing: false,
      preferences: false,
    })
  }

  /**
   * Accept custom preferences
   */
  const acceptCustom = (preferences: Partial<CookiePreferences>) => {
    saveConsent({
      necessary: true,
      analytics: preferences.analytics ?? false,
      marketing: preferences.marketing ?? false,
      preferences: preferences.preferences ?? false,
    })
  }

  /**
   * Reset consent
   */
  const resetConsent = () => {
    if (process.client) {
      localStorage.removeItem(COOKIE_CONSENT_KEY)
      localStorage.removeItem(COOKIE_CONSENT_TIMESTAMP)
      hasConsent.value = false
      showBanner.value = true
      cookieConsent.value = {
        necessary: true,
        analytics: false,
        marketing: false,
        preferences: false,
      }
    }
  }

  /**
   * Check if specific cookie type is allowed
   */
  const isAllowed = (type: CookieConsentType): boolean => {
    return cookieConsent.value[type]
  }

  /**
   * Check if analytics is allowed
   */
  const canUseAnalytics = computed(() => cookieConsent.value.analytics)

  /**
   * Check if marketing is allowed
   */
  const canUseMarketing = computed(() => cookieConsent.value.marketing)

  return {
    cookieConsent,
    hasConsent,
    showBanner,
    loadConsent,
    saveConsent,
    acceptAll,
    acceptNecessary,
    acceptCustom,
    resetConsent,
    isAllowed,
    canUseAnalytics,
    canUseMarketing,
  }
}

// Global state management
let globalState: ReturnType<typeof useCookieConsent> | null = null

export const useCookieConsentState = () => {
  if (!globalState) {
    globalState = useCookieConsent()
  }
  return globalState
}
