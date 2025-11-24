import { ref, computed } from 'vue'

export interface NotificationPermissionState {
  supported: boolean
  permission: NotificationPermission | null
  subscribed: boolean
  loading: boolean
  error: string | null
}

export const usePushNotifications = () => {
  const { apiFetch } = useApi()
  const config = useRuntimeConfig()

  const state = ref<NotificationPermissionState>({
    supported: false,
    permission: null,
    subscribed: false,
    loading: false,
    error: null
  })

  // التحقق من دعم المتصفح
  const checkSupport = () => {
    if (process.client) {
      state.value.supported = 
        'serviceWorker' in navigator &&
        'PushManager' in window &&
        'Notification' in window
      
      if (state.value.supported) {
        state.value.permission = Notification.permission
      }
    }
    return state.value.supported
  }

  // تحويل base64 إلى Uint8Array
  const urlBase64ToUint8Array = (base64String: string): Uint8Array => {
    const padding = '='.repeat((4 - base64String.length % 4) % 4)
    const base64 = (base64String + padding)
      .replace(/\-/g, '+')
      .replace(/_/g, '/')

    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
  }

  // تسجيل Service Worker
  const registerServiceWorker = async (): Promise<ServiceWorkerRegistration | null> => {
    if (!checkSupport()) {
      state.value.error = 'المتصفح لا يدعم الإشعارات'
      return null
    }

    try {
      const registration = await navigator.serviceWorker.register('/sw.js', {
        scope: '/'
      })

      return registration
    } catch (error) {
      console.error('Service Worker registration failed:', error)
      state.value.error = 'فشل تسجيل Service Worker'
      return null
    }
  }

  // طلب إذن الإشعارات
  const requestPermission = async (): Promise<NotificationPermission> => {
    if (!state.value.supported) {
      throw new Error('الإشعارات غير مدعومة')
    }

    const permission = await Notification.requestPermission()
    state.value.permission = permission
    return permission
  }

  // الاشتراك في الإشعارات
  const subscribe = async (): Promise<boolean> => {
    if (!checkSupport()) {
      return false
    }

    state.value.loading = true
    state.value.error = null

    try {
      // طلب الإذن إذا لم يكن ممنوحاً
      if (state.value.permission !== 'granted') {
        const permission = await requestPermission()
        if (permission !== 'granted') {
          state.value.error = 'تم رفض إذن الإشعارات'
          return false
        }
      }

      // تسجيل Service Worker
      const registration = await registerServiceWorker()
      if (!registration) {
        return false
      }

      // الانتظار حتى يصبح Service Worker جاهزاً
      await navigator.serviceWorker.ready

      // الحصول على مفتاح VAPID العام
      const keyData = await apiFetch<{ public_key: string }>('/push/public-key')
      const publicKey = keyData?.public_key

      if (!publicKey) {
        throw new Error('لم يتم العثور على مفتاح VAPID')
      }

      // الاشتراك في Push Manager
      const applicationServerKey = urlBase64ToUint8Array(publicKey)
      const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey.buffer as ArrayBuffer
      })

      // إرسال الاشتراك إلى الخادم
      const subscriptionData = {
        endpoint: subscription.endpoint,
        keys: {
          p256dh: arrayBufferToBase64(subscription.getKey('p256dh')),
          auth: arrayBufferToBase64(subscription.getKey('auth'))
        }
      }

      const response = await apiFetch<{ success: boolean }>('/push/subscribe', {
        method: 'POST',
        body: subscriptionData
      })

      if (response?.success) {
        state.value.subscribed = true
        
        // حفظ حالة الاشتراك في localStorage
        if (process.client) {
          localStorage.setItem('push_subscribed', 'true')
        }
        
        return true
      }

      return false
    } catch (error: any) {
      console.error('Error subscribing to push notifications:', error)
      state.value.error = error.message || 'فشل الاشتراك في الإشعارات'
      return false
    } finally {
      state.value.loading = false
    }
  }

  // إلغاء الاشتراك
  const unsubscribe = async (): Promise<boolean> => {
    if (!checkSupport()) {
      return false
    }

    state.value.loading = true
    state.value.error = null

    try {
      const registration = await navigator.serviceWorker.ready
      const subscription = await registration.pushManager.getSubscription()

      if (!subscription) {
        state.value.subscribed = false
        return true
      }

      // إلغاء الاشتراك من المتصفح
      await subscription.unsubscribe()

      // إلغاء الاشتراك من الخادم
      await apiFetch('/push/unsubscribe', {
        method: 'POST',
        body: {
          endpoint: subscription.endpoint
        }
      })

      state.value.subscribed = false
      
      // حذف حالة الاشتراك من localStorage
      if (process.client) {
        localStorage.removeItem('push_subscribed')
      }

      return true
    } catch (error: any) {
      console.error('Error unsubscribing from push notifications:', error)
      state.value.error = error.message || 'فشل إلغاء الاشتراك'
      return false
    } finally {
      state.value.loading = false
    }
  }

  // التحقق من حالة الاشتراك
  const checkSubscription = async (): Promise<boolean> => {
    if (!checkSupport()) {
      return false
    }

    try {
      const registration = await navigator.serviceWorker.getRegistration()
      if (!registration) {
        state.value.subscribed = false
        return false
      }

      const subscription = await registration.pushManager.getSubscription()
      state.value.subscribed = !!subscription
      
      return state.value.subscribed
    } catch (error) {
      console.error('Error checking subscription:', error)
      return false
    }
  }

  // إرسال إشعار تجريبي
  const sendTestNotification = async (): Promise<boolean> => {
    try {
      const response = await apiFetch<{ success: boolean }>('/push/test', {
        method: 'POST'
      })
      return response?.success || false
    } catch (error) {
      console.error('Error sending test notification:', error)
      return false
    }
  }

  // تحديث التفضيلات
  const updatePreferences = async (preferences: Record<string, any>): Promise<boolean> => {
    try {
      const registration = await navigator.serviceWorker.ready
      const subscription = await registration.pushManager.getSubscription()

      if (!subscription) {
        throw new Error('لا يوجد اشتراك نشط')
      }

      const response = await apiFetch<{ success: boolean }>('/push/update-preferences', {
        method: 'POST',
        body: {
          endpoint: subscription.endpoint,
          preferences
        }
      })

      return response?.success || false
    } catch (error) {
      console.error('Error updating preferences:', error)
      return false
    }
  }

  // تحويل ArrayBuffer إلى Base64
  const arrayBufferToBase64 = (buffer: ArrayBuffer | null | undefined): string => {
    if (!buffer) return ''
    const bytes = new Uint8Array(buffer)
    let binary = ''
    const len = bytes.byteLength || 0
    for (let i = 0; i < len; i++) {
      const byte = bytes[i]
      if (byte !== undefined) {
        binary += String.fromCharCode(byte)
      }
    }
    return window.btoa(binary)
  }

  // Computed properties
  const canSubscribe = computed(() => {
    return state.value.supported && 
           state.value.permission !== 'denied' && 
           !state.value.subscribed
  })

  const canUnsubscribe = computed(() => {
    return state.value.supported && state.value.subscribed
  })

  const isBlocked = computed(() => {
    return state.value.permission === 'denied'
  })

  // التحقق من الحالة عند التحميل
  if (process.client) {
    checkSupport()
    checkSubscription()
  }

  return {
    state,
    canSubscribe,
    canUnsubscribe,
    isBlocked,
    checkSupport,
    requestPermission,
    subscribe,
    unsubscribe,
    checkSubscription,
    sendTestNotification,
    updatePreferences
  }
}
