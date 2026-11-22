import { API_CLIENT_HEADER, API_CLIENT_TOKEN, decryptApiData } from '~/utils/apiCrypto'

export default defineNuxtPlugin(() => {
  const originalFetch = globalThis.$fetch
  if (!originalFetch || (originalFetch as any).__adenSecured) {
    return
  }

  const securedFetch = originalFetch.create({
    onRequest({ options }) {
      const headers = new Headers((options.headers as HeadersInit) || {})
      if (!headers.has(API_CLIENT_HEADER)) {
        headers.set(API_CLIENT_HEADER, API_CLIENT_TOKEN)
      }
      options.headers = headers
    },
    onResponse({ response }) {
      if (response && response._data) {
        response._data = decryptApiData(response._data)
      }
    }
  })

  ;(securedFetch as any).__adenSecured = true
  globalThis.$fetch = securedFetch as typeof globalThis.$fetch
})
