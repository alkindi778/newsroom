export const API_CLIENT_HEADER = 'X-Aden-Client-Token'
export const API_CLIENT_TOKEN = 'aden-nr-v1-9f8e7d6c5b4a3f2e1d0c9b8a7f6e5d4c'
const API_CRYPTO_SECRET = 'aden-newsroom-enc-key-2026-x9a8b7c6d5e4f3'

export function getApiSecurityHeaders(): Record<string, string> {
  return {
    [API_CLIENT_HEADER]: API_CLIENT_TOKEN
  }
}

function base64ToBytes(b64: string): Uint8Array {
  if (typeof Buffer !== 'undefined') {
    return new Uint8Array(Buffer.from(b64, 'base64'))
  }
  const binString = atob(b64)
  const len = binString.length
  const bytes = new Uint8Array(len)
  for (let i = 0; i < len; i++) {
    bytes[i] = binString.charCodeAt(i)
  }
  return bytes
}

/**
 * فك تشفير البيانات القادمة من الـ API تلقائياً
 */
export function decryptApiData<T = any>(response: any): T {
  if (typeof response === 'string') {
    try {
      const parsed = JSON.parse(response)
      if (parsed && typeof parsed === 'object' && parsed._enc === 1 && typeof parsed.d === 'string') {
        return decryptApiData<T>(parsed)
      }
    } catch {
      return response as unknown as T
    }
  }

  if (!response || typeof response !== 'object' || response._enc !== 1 || typeof response.d !== 'string') {
    return response as T
  }

  try {
    const dotIdx = response.d.indexOf('.')
    if (dotIdx === -1) return response as T

    const iv = response.d.slice(0, dotIdx)
    const cipherB64 = response.d.slice(dotIdx + 1)
    const key = `${API_CRYPTO_SECRET}:${iv}`
    const keyLen = key.length

    // Key-Scheduling Algorithm (KSA)
    const s = new Uint8Array(256)
    for (let i = 0; i < 256; i++) s[i] = i

    let j = 0
    for (let i = 0; i < 256; i++) {
      j = (j + s[i]! + key.charCodeAt(i % keyLen)) & 0xff
      const tmp = s[i]!
      s[i] = s[j]!
      s[j] = tmp
    }

    // Pseudo-Random Generation Algorithm (RC4-drop256)
    let i = 0
    j = 0
    for (let k = 0; k < 256; k++) {
      i = (i + 1) & 0xff
      j = (j + s[i]!) & 0xff
      const tmp = s[i]!
      s[i] = s[j]!
      s[j] = tmp
    }

    const data = base64ToBytes(cipherB64)
    const len = data.length
    const out = new Uint8Array(len)
    for (let k = 0; k < len; k++) {
      i = (i + 1) & 0xff
      j = (j + s[i]!) & 0xff
      const tmp = s[i]!
      s[i] = s[j]!
      s[j] = tmp
      const t = (s[i]! + s[j]!) & 0xff
      out[k] = data[k]! ^ s[t]!
    }

    const jsonStr = new TextDecoder('utf-8').decode(out)
    return JSON.parse(jsonStr) as T
  } catch (err) {
    console.error('[API Crypto] Failed to decrypt response:', err)
    return response as T
  }
}
