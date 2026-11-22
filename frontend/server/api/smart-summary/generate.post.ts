import crypto from 'crypto'
import { API_CLIENT_HEADER, API_CLIENT_TOKEN, decryptApiData } from '../../../utils/apiCrypto'
import { defineEventHandler, readBody, createError } from 'h3'

interface RequestBody {
  content: string
  type?: 'news' | 'opinion' | 'analysis'
  length?: 'short' | 'medium' | 'long'
  language?: 'ar' | 'en'
  onlyCache?: boolean
}

interface CachedSummary {
  summary: string
  timestamp?: number
  type: string
  length: string
  hash?: string
  word_count?: number
  compression_ratio?: number
  original_length?: number
  usage_count?: number
}

interface SummaryResponse {
  success: boolean
  summary?: string
  word_count?: number
  compression_ratio?: number
  original_length?: number
  message?: string
  error_code?: number
}

export default defineEventHandler(async (event): Promise<SummaryResponse> => {
  try {
    if (event.node.req.method !== 'POST') {
      throw createError({
        statusCode: 405,
        statusMessage: 'Method Not Allowed'
      })
    }

    const body = await readBody(event) as RequestBody

    if (!body.content || body.content.trim().length < 100) {
      return {
        success: false,
        message: 'المحتوى مطلوب ويجب أن يكون أطول من 100 حرف',
        error_code: 400
      }
    }

    const cleanedForValidation = cleanHtmlAndMedia(body.content)

    if (cleanedForValidation.length < 50) {
      return {
        success: false,
        message: 'المحتوى لا يحتوي على نص كافٍ للتلخيص',
        error_code: 422
      }
    }

    if (body.content.length > 50000) {
      return {
        success: false,
        message: 'المحتوى طويل جداً (الحد الأقصى 50000 حرف)',
        error_code: 400
      }
    }

    // مفتاح كاش بالإصدار الجديد (v3-concise) لتجاهل الملخصات الطويلة القديمة
    const contentHash = generateContentHash(body.content, body.type || 'news')

    const cachedSummary = await getCachedSummaryFromDB(contentHash)
    if (cachedSummary && cachedSummary.summary) {
      const formattedCached = enforceConciseParagraphs(cachedSummary.summary, body.type || 'news')
      return {
        success: true,
        summary: formattedCached,
        word_count: countWords(formattedCached),
        compression_ratio: Math.max(60, Math.round((1 - (formattedCached.replace(/<[^>]*>/g, '').length / cleanedForValidation.length)) * 100)),
        original_length: cleanedForValidation.length
      }
    }

    if (body.onlyCache) {
      return {
        success: false,
        message: 'لا يوجد ملخص محفوظ بعد لهذا المحتوى',
        error_code: 404
      }
    }

    const config = useRuntimeConfig()
    const apiKey = (config.geminiApiKey as string) || process.env.GEMINI_API_KEY || ''

    let generatedText = ''
    if (apiKey) {
      generatedText = await generateWithGemini(cleanedForValidation, body.type || 'news', apiKey)
    }

    if (!generatedText) {
      generatedText = generateConciseExtractiveSummary(cleanedForValidation, body.type || 'news')
    }

    const processedSummary = enforceConciseParagraphs(generatedText, body.type || 'news')

    const originalLength = cleanedForValidation.length
    const summaryLength = processedSummary.replace(/<[^>]*>/g, '').length
    const wordCount = countWords(processedSummary)
    const compressionRatio = Math.max(65, Math.min(95, Math.round((1 - (summaryLength / Math.max(originalLength, 1))) * 100)))

    await saveSummaryToDB({
      content_hash: contentHash,
      original_content_sample: cleanedForValidation.substring(0, 500),
      summary: processedSummary,
      type: body.type || 'news',
      length: 'short',
      word_count: wordCount,
      compression_ratio: compressionRatio,
      original_length: originalLength
    })

    return {
      success: true,
      summary: processedSummary,
      word_count: wordCount,
      compression_ratio: compressionRatio,
      original_length: originalLength
    }
  } catch (error: any) {
    console.error('خطأ في توليد الملخص:', error)

    if (error.statusCode) {
      throw error
    }

    return {
      success: false,
      message: 'حدث خطأ أثناء معالجة الملخص. يرجى المحاولة مرة أخرى.',
      error_code: 500
    }
  }
})

/**
 * تنظيف المحتوى من وسوم HTML والصور والروابط والرموز
 */
function cleanHtmlAndMedia(content: string): string {
  return content
    .replace(/<img[^>]*\/?>/gi, '')
    .replace(/<figure[^>]*>.*?<\/figure>/gis, '')
    .replace(/<picture[^>]*>.*?<\/picture>/gis, '')
    .replace(/<video[^>]*>.*?<\/video>/gis, '')
    .replace(/<iframe[^>]*>.*?<\/iframe>/gis, '')
    .replace(/<\/p>/gi, '\n')
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<[^>]*>/g, ' ')
    .replace(/&amp;nbsp;|&nbsp;|\u00A0/gi, ' ')
    .replace(/&quot;|&ldquo;|&rdquo;/gi, '"')
    .replace(/&#0*39;|&lsquo;|&rsquo;/gi, "'")
    .replace(/&amp;/gi, '&')
    .replace(/\[صورة:.*?\]/g, '')
    .replace(/https?:\/\/[^\s]+/g, '')
    .replace(/[ \t]+/g, ' ')
    .replace(/\n\s*\n+/g, '\n')
    .trim()
}

/**
 * توليد ملخص احترافي ومختصر جداً عبر Gemini AI
 */
async function generateWithGemini(cleanContent: string, type: string, apiKey: string): Promise<string> {
  try {
    const inputSample = cleanContent.length > 4000 ? cleanContent.slice(0, 4000) : cleanContent
    const roleHint = type === 'opinion'
      ? 'لخص مقال الرأي التالي في فقرتين قصيرتين جداً (تبدأ الأولى بـ "يرى الكاتب أن").'
      : 'لخص الخبر التالي بأسلوب صحفي احترافي ومكثف في فقرتين قصيرتين جداً.'

    const prompt = `${roleHint}

الشروط الصارمة:
- إجمالي الملخص بين 35 إلى 45 كلمة كحد أقصى (مختصر جداً ومركّز).
- مقسم إلى فقرتين قصيرتين فقط تفصل بينهما سطر فارغ:
  الفقرة الأولى: جوهر الحدث أو القرار الرئيسي في جملة واحدة مكثفة.
  الفقرة الثانية: أبرز نتيجة أو تفصيل محوري في جملة واحدة موجزة.
- بدون أي عناوين أو نجوم أو شرطات أو مقدمات.

النص:
${inputSample}`

    const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        contents: [{ parts: [{ text: prompt }] }],
        generationConfig: {
          temperature: 0.3,
          maxOutputTokens: 140
        }
      })
    })

    if (!response.ok) {
      return ''
    }

    const data: any = await response.json()
    const text = data?.candidates?.[0]?.content?.parts?.[0]?.text
    return typeof text === 'string' ? text.trim() : ''
  } catch {
    return ''
  }
}

/**
 * توليد ملخص استخلاصي مختصر جداً ومنسق في فقرتين في حال عدم توفر Gemini
 */
function generateConciseExtractiveSummary(cleanContent: string, type: string): string {
  // تقسيم المحتوى إلى جمل أو فقرات ذات معنى
  const rawSegments = cleanContent
    .split(/(?:[.!؟\n]+|،\s+(?=و?(?:أكد|أشار|أوضح|ناقش|شدد|دعا|بحث|استعرض|أعلن|قررت|وجه)))/g)
    .map(s => s.trim())
    .filter(s => s.length >= 25 && !s.startsWith('صادر عن') && !s.includes('﷽'))

  if (rawSegments.length === 0) {
    const words = cleanContent.split(/\s+/).slice(0, 40)
    const mid = Math.ceil(words.length / 2)
    return `${words.slice(0, mid).join(' ')}.\n\n${words.slice(mid).join(' ')}.`
  }

  const trimToWords = (str: string, maxWords: number): string => {
    const words = str.trim().split(/\s+/)
    if (words.length <= maxWords) return str.trim().replace(/[،,.]+$/, '')
    return words.slice(0, maxWords).join(' ').replace(/[،,.]+$/, '')
  }

  let para1 = trimToWords(rawSegments[0]!, 24)
  let para2 = ''

  if (rawSegments.length > 1) {
    // اختيار جملة ثانية مكملة من وسط أو نهاية الخبر
    const secondCandidate = rawSegments.find((s, idx) => idx > 0 && s.length >= 30) || rawSegments[1]!
    para2 = trimToWords(secondCandidate, 22)
  } else {
    // تقسيم الجملة الطويلة إلى فقرتين قصيرتين
    const allWords = rawSegments[0]!.split(/\s+/)
    if (allWords.length > 22) {
      para1 = allWords.slice(0, 20).join(' ').replace(/[،,.]+$/, '')
      para2 = allWords.slice(20, 40).join(' ').replace(/[،,.]+$/, '')
    }
  }

  if (type === 'opinion' && !para1.startsWith('يرى الكاتب')) {
    para1 = `يرى الكاتب أن ${para1}`
  }

  return para2 ? `${para1}.\n\n${para2}.` : `${para1}.`
}

/**
 * تنسيق الملخص وضمان كونه مختصراً جداً (بحد أقصى ~48 كلمة) ومقسماً إلى فقرتين احترافيتين
 */
function enforceConciseParagraphs(rawText: string, type: string): string {
  // إزالة أي HTML disclaimer قديم
  const withoutDisclaimer = rawText.split('<div class="ai-disclaimer"')[0] || rawText

  let cleaned = withoutDisclaimer
    .replace(/<[^>]*>/g, '')
    .replace(/^\*+|\*+$/g, '')
    .replace(/\*\*([^*]+)\*\*/g, '$1')
    .replace(/\*([^*]+)\*/g, '$1')
    .replace(/^[\*\-\•]\s*/gm, '')
    .replace(/^(الفقرة الأولى|الفقرة الثانية|ملخص الخبر|الملخص)\s*[:：\-]?\s*/gim, '')
    .replace(/&amp;nbsp;|&nbsp;|\u00A0/gi, ' ')
    .trim()

  // تقسيم إلى فقرات
  let paragraphs = cleaned
    .split(/\n\s*\n|\n/)
    .map(p => p.trim())
    .filter(p => p.length > 10)

  // إذا كانت فقرة واحدة، نقسمها إلى فقرتين متوازنتين
  if (paragraphs.length === 1) {
    const sentences = paragraphs[0]!
      .split(/[.!؟]+/)
      .map(s => s.trim())
      .filter(s => s.length > 10)

    if (sentences.length >= 2) {
      paragraphs = [sentences[0]!, sentences.slice(1, 2).join('. ')]
    } else {
      const words = paragraphs[0]!.split(/\s+/)
      if (words.length > 24) {
        const mid = Math.min(22, Math.ceil(words.length / 2))
        paragraphs = [
          words.slice(0, mid).join(' '),
          words.slice(mid, 44).join(' ')
        ]
      }
    }
  }

  // الاكتفاء بفقرتين كحد أقصى وتحديد سقف الكلمات لكل فقرة (حوالي 24 كلمة للفقرة = ~45-48 كلمة إجمالاً)
  const finalParagraphs = paragraphs.slice(0, 2).map((p, idx) => {
    const words = p.split(/\s+/)
    const maxW = idx === 0 ? 25 : 23
    let clipped = words.length > maxW ? words.slice(0, maxW).join(' ') : p
    clipped = clipped.replace(/[،,.\s]+$/, '')
    if (!/[.!؟]$/.test(clipped)) {
      clipped += '.'
    }
    return clipped
  })

  if (type === 'opinion' && finalParagraphs[0] && !finalParagraphs[0].startsWith('يرى الكاتب')) {
    finalParagraphs[0] = `يرى الكاتب أن ${finalParagraphs[0]}`
  }

  return finalParagraphs.join('\n\n')
}

function countWords(text: string): number {
  const cleanText = text.replace(/<[^>]*>/g, '').trim()
  if (!cleanText) return 0
  return cleanText.split(/\s+/).length
}

function generateContentHash(content: string, type: string): string {
  const normalizedContent = content
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase()

  const key = `v3-concise-${normalizedContent}-${type}`
  return crypto.createHash('sha256').update(key).digest('hex')
}

async function getCachedSummaryFromDB(hash: string): Promise<CachedSummary | null> {
  try {
    const apiBase = process.env.NUXT_PUBLIC_API_BASE
    if (!apiBase) return null

    const response = await fetch(`${apiBase}/smart-summaries/get/${hash}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        [API_CLIENT_HEADER]: API_CLIENT_TOKEN
      }
    })

    if (response.ok) {
      const rawData = await response.json()
      const data = decryptApiData(rawData)
      return data.summary || null
    }

    return null
  } catch {
    return null
  }
}

async function saveSummaryToDB(summaryData: any): Promise<void> {
  try {
    const apiBase = process.env.NUXT_PUBLIC_API_BASE
    if (!apiBase) return

    await fetch(`${apiBase}/smart-summaries/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        [API_CLIENT_HEADER]: API_CLIENT_TOKEN
      },
      body: JSON.stringify(summaryData)
    })
  } catch {
    // ignore cache save errors
  }
}
