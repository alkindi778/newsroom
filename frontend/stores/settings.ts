import { defineStore } from 'pinia'

// Helper function to fetch from API
const apiFetch = async (endpoint: string, options: any = {}): Promise<any> => {
  const config = useRuntimeConfig()
  const apiBase = (config as any).public.apiBase
  
  const response = await $fetch(`${apiBase}${endpoint}`, {
    ...options,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers
    }
  })
  
  return response as any
}

interface SiteSettings {
  // General
  site_name: string
  site_name_en: string
  site_slogan: string
  site_description: string
  site_keywords: string
  site_logo: string
  site_logo_width: string
  site_favicon: string
  
  // SEO
  site_locale: string
  theme_color: string
  default_og_image: string
  twitter_handle: string
  seo_title_separator: string
  seo_google_analytics: string
  seo_google_verification: string
  
  // Organization
  org_founding_date: string
  org_area_served: string
  org_address_country: string
  org_address_city: string
  
  // Contact
  contact_email: string
  contact_phone: string
  contact_address: string
  
  // Social
  social_facebook: string
  social_twitter: string
  social_instagram: string
  social_youtube: string
  social_tiktok?: string
  social_telegram?: string
}

export const useSettingsStore = defineStore('settings', {
  state: () => ({
    settings: {} as SiteSettings,
    grouped: {} as Record<string, any>,
    loading: false,
    error: null as string | null,
    lastFetch: null as number | null
  }),

  getters: {
    /**
     * Get setting by key
     */
    getSetting: (state) => (key: string, defaultValue: any = null) => {
      return state.settings[key as keyof SiteSettings] || defaultValue
    },

    /**
     * Get settings by group
     */
    getGroupSettings: (state) => (group: string) => {
      return state.grouped[group] || {}
    },

    /**
     * Check if settings are loaded
     */
    isLoaded: (state) => {
      return Object.keys(state.settings).length > 0
    },

    /**
     * Check if cache is valid (5 minutes)
     */
    isCacheValid: (state) => {
      if (!state.lastFetch) return false
      const fiveMinutes = 5 * 60 * 1000
      return Date.now() - state.lastFetch < fiveMinutes
    }
  },

  actions: {
    /**
     * Fetch all settings from API
     */
    async fetchSettings(force = false) {
      // Skip if already loaded and cache is valid
      if (!force && this.isLoaded && this.isCacheValid) {
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await apiFetch('/settings')
        
        if (response.success) {
          this.settings = response.data.flat
          this.grouped = response.data.grouped
          this.lastFetch = Date.now()
          
          // Store in localStorage for persistence
          if (process.client) {
            localStorage.setItem('site_settings', JSON.stringify(this.settings))
            localStorage.setItem('site_settings_time', String(this.lastFetch))
          }
        }
      } catch (error: any) {
        this.error = error.message || 'حدث خطأ في جلب الإعدادات'
        console.error('Error fetching settings:', error)
        
        // Load from localStorage as fallback
        if (process.client) {
          const cached = localStorage.getItem('site_settings')
          if (cached) {
            this.settings = JSON.parse(cached)
          }
        }
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch settings by group
     */
    async fetchGroupSettings(group: string) {
      try {
        const response = await apiFetch(`/settings/group/${group}`)
        
        if (response.success) {
          this.grouped[group] = response.data
        }
      } catch (error: any) {
        console.error(`Error fetching ${group} settings:`, error)
      }
    },

    /**
     * Restore from localStorage on init
     */
    restoreFromCache() {
      if (process.client) {
        const cached = localStorage.getItem('site_settings')
        const cacheTime = localStorage.getItem('site_settings_time')
        
        if (cached && cacheTime) {
          this.settings = JSON.parse(cached)
          this.lastFetch = parseInt(cacheTime)
        }
      }
    },

    /**
     * Clear cache
     */
    clearCache() {
      this.settings = {} as SiteSettings
      this.grouped = {}
      this.lastFetch = null
      
      if (process.client) {
        localStorage.removeItem('site_settings')
        localStorage.removeItem('site_settings_time')
      }
    }
  }
})
