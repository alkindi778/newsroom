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
    const lang: 'ar' | 'en' = body.language === 'en' ? 'en' : 'ar'

    if (!body.content || body.content.trim().length < 100) {
      return {
        success: false,
        message: lang === 'en' ? 'Content is too short to summarize' : 'المحتوى مطلوب ويجب أن يكون أطول من 100 حرف',
        error_code: 400
      }
    }

    const cleanedForValidation = cleanHtmlAndMedia(body.content)

    if (cleanedForValidation.length < 50) {
      return {
        success: false,
        message: lang === 'en' ? 'Not enough text content to summarize' : 'المحتوى لا يحتوي على نص كافٍ للتلخيص',
        error_code: 422
      }
    }

    if (body.content.length > 50000) {
      return {
        success: false,
        message: lang === 'en' ? 'Content is too long' : 'المحتوى طويل جداً (الحد الأقصى 50000 حرف)',
        error_code: 400
      }
    }

    // مفتاح كاش منفصل لكل لغة (ar / en)
    const contentHash = generateContentHash(body.content, body.type || 'news', lang)

    const cachedSummary = await getCachedSummaryFromDB(contentHash)
    if (cachedSummary && cachedSummary.summary) {
      let formattedCached = enforceConciseParagraphs(cachedSummary.summary, body.type || 'news', lang)
      // التأكد من أن الملخص الإنجليزي المحفوظ ليس بالعربية
      if (lang === 'en' && hasArabicCharacters(formattedCached)) {
        formattedCached = await translateToEnglishFallback(formattedCached)
      }
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
        message: lang === 'en' ? 'No cached summary found' : 'لا يوجد ملخص محفوظ بعد لهذا المحتوى',
        error_code: 404
      }
    }

    const config = useRuntimeConfig()
    const apiKey = (config.geminiApiKey as string) || process.env.GEMINI_API_KEY || ''

    let generatedText = ''
    if (apiKey) {
      generatedText = await generateWithGemini(cleanedForValidation, body.type || 'news', lang, apiKey)
    }

    if (!generatedText) {
      generatedText = generateConciseExtractiveSummary(cleanedForValidation, body.type || 'news', lang)
    }

    let processedSummary = enforceConciseParagraphs(generatedText, body.type || 'news', lang)

    // إذا كانت اللغة المطلوبة إنجليزية ولكن النص المستخرج لا يزال بالعربية، نترجمه فوراً للإنجليزية
    if (lang === 'en' && hasArabicCharacters(processedSummary)) {
      const translated = await translateToEnglishFallback(processedSummary)
      processedSummary = enforceConciseParagraphs(translated, body.type || 'news', 'en')
    }

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

function hasArabicCharacters(text: string): boolean {
  return /[\u0600-\u06FF]/.test(text)
}

/**
 * ترجمة الملخص تلقائياً إلى الإنجليزية في حال كان المصدر عربياً أو تعذر اتصال Gemini
 */
async function translateToEnglishFallback(arabicSummary: string): Promise<string> {
  try {
    const paragraphs = arabicSummary.split(/\n\s*\n/).map(p => p.trim()).filter(Boolean)
    const translatedParagraphs: string[] = []

    for (const para of paragraphs) {
      const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=ar&tl=en&dt=t&q=${encodeURIComponent(para)}`
      const res = await fetch(url)
      if (res.ok) {
        const data: any = await res.json()
        if (Array.isArray(data?.[0])) {
          const translatedText = data[0].map((chunk: any) => chunk?.[0] || '').join(' ').trim()
          if (translatedText) {
            translatedParagraphs.push(translatedText)
            continue
          }
        }
      }
      translatedParagraphs.push(para)
    }

    return translatedParagraphs.join('\n\n')
  } catch {
    return arabicSummary
  }
}

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
 * توليد ملخص احترافي ومختصر جداً عبر Gemini AI (عربي أو إنجليزي)
 */
async function generateWithGemini(cleanContent: string, type: string, lang: 'ar' | 'en', apiKey: string): Promise<string> {
  try {
    const inputSample = cleanContent.length > 4000 ? cleanContent.slice(0, 4000) : cleanContent

    let prompt = ''
    if (lang === 'en') {
      const roleHintEn = type === 'opinion'
        ? 'Summarize the following opinion article in English into 2 very short paragraphs (start the first with "The author argues that").'
        : 'Summarize the following news article in professional journalistic English into 2 very short paragraphs.'

      prompt = `${roleHintEn}

Strict rules:
- Write the summary strictly in ENGLISH (even if the source text is in Arabic, translate and summarize it into fluent English).
- Total length must be between 35 and 45 words maximum (ultra-concise and focused).
- Divide into exactly 2 short paragraphs separated by a blank line:
  Paragraph 1: The core event or main decision in one concise sentence.
  Paragraph 2: The key outcome or essential detail in one brief sentence.
- Do not use headings, bullet symbols, or asterisks.

Source text:
${inputSample}`
    } else {
      const roleHintAr = type === 'opinion'
        ? 'لخص مقال الرأي التالي في فقرتين قصيرتين جداً (تبدأ الأولى بـ "يرى الكاتب أن").'
        : 'لخص الخبر التالي بأسلوب صحفي احترافي ومكثف في فقرتين قصيرتين جداً.'

      prompt = `${roleHintAr}

الشروط الصارمة:
- إجمالي الملخص بين 35 إلى 45 كلمة كحد أقصى (مختصر جداً ومركّز).
- مقسم إلى فقرتين قصيرتين فقط تفصل بينهما سطر فارغ:
  الفقرة الأولى: جوهر الحدث أو القرار الرئيسي في جملة واحدة مكثفة.
  الفقرة الثانية: أبرز نتيجة أو تفصيل محوري في جملة واحدة موجزة.
- بدون أي عناوين أو نجوم أو شرطات أو مقدمات.

النص:
${inputSample}`
    }

    const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        contents: [{ parts: [{ text: prompt }] }],
        generationConfig: {
          temperature: 0.3,
          maxOutputTokens: 150
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
 * توليد ملخص استخلاصي مختصر جداً ومنسق في فقرتين
 */
function generateConciseExtractiveSummary(cleanContent: string, type: string, lang: 'ar' | 'en'): string {
  const rawSegments = cleanContent
    .split(/(?:[.!؟\n]+|،\s+(?=و?(?:أكد|أشار|أوضح|ناقش|شدد|دعا|بحث|استعرض|أعلن|قررت|وجه)))/g)
    .map(s => s.trim())
    .filter(s => s.length >= 25 && !s.startsWith('صادر عن') && !s.startsWith('Issued by') && !s.includes('﷽'))

  if (rawSegments.length === 0) {
    const words = cleanContent.split(/\s+/).slice(0, 42)
    const mid = Math.ceil(words.length / 2)
    return `${words.slice(0, mid).join(' ')}.\n\n${words.slice(mid).join(' ')}.`
  }

  const trimToWords = (str: string, maxWords: number): string => {
    const words = str.trim().split(/\s+/)
    if (words.length <= maxWords) return str.trim().replace(/[،,.;:]+$/, '')
    return words.slice(0, maxWords).join(' ').replace(/[،,.;:]+$/, '')
  }

  let para1 = trimToWords(rawSegments[0]!, 24)
  let para2 = ''

  if (rawSegments.length > 1) {
    const secondCandidate = rawSegments.find((s, idx) => idx > 0 && s.length >= 30) || rawSegments[1]!
    para2 = trimToWords(secondCandidate, 22)
  } else {
    const allWords = rawSegments[0]!.split(/\s+/)
    if (allWords.length > 22) {
      para1 = allWords.slice(0, 20).join(' ').replace(/[،,.;:]+$/, '')
      para2 = allWords.slice(20, 42).join(' ').replace(/[،,.;:]+$/, '')
    }
  }

  if (type === 'opinion') {
    if (lang === 'en' && !para1.toLowerCase().startsWith('the author')) {
      para1 = `The author argues that ${para1}`
    } else if (lang === 'ar' && !para1.startsWith('يرى الكاتب')) {
      para1 = `يرى الكاتب أن ${para1}`
    }
  }

  return para2 ? `${para1}.\n\n${para2}.` : `${para1}.`
}

/**
 * تنسيق الملخص وضمان كونه مختصراً جداً (بحد أقصى ~48 كلمة) ومقسماً إلى فقرتين احترافيتين
 */
function enforceConciseParagraphs(rawText: string, type: string, lang: 'ar' | 'en'): string {
  const withoutDisclaimer = rawText.split('<div class="ai-disclaimer"')[0] || rawText

  const cleaned = withoutDisclaimer
    .replace(/<[^>]*>/g, '')
    .replace(/^\*+|\*+$/g, '')
    .replace(/\*\*([^*]+)\*\*/g, '$1')
    .replace(/\*([^*]+)\*/g, '$1')
    .replace(/^[\*\-\•]\s*/gm, '')
    .replace(/^(الفقرة الأولى|الفقرة الثانية|ملخص الخبر|الملخص|Paragraph 1|Paragraph 2|Summary)\s*[:：\-]?\s*/gim, '')
    .replace(/&amp;nbsp;|&nbsp;|\u00A0/gi, ' ')
    .trim()

  let paragraphs = cleaned
    .split(/\n\s*\n|\n/)
    .map(p => p.trim())
    .filter(p => p.length > 10)

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
          words.slice(mid, 45).join(' ')
        ]
      }
    }
  }

  const finalParagraphs = paragraphs.slice(0, 2).map((p, idx) => {
    const words = p.split(/\s+/)
    const maxW = idx === 0 ? 25 : 23
    let clipped = words.length > maxW ? words.slice(0, maxW).join(' ') : p
    clipped = clipped.replace(/[،,.;:\s]+$/, '')
    if (!/[.!؟]$/.test(clipped)) {
      clipped += '.'
    }
    return clipped
  })

  if (type === 'opinion' && finalParagraphs[0]) {
    if (lang === 'en' && !finalParagraphs[0].toLowerCase().startsWith('the author')) {
      finalParagraphs[0] = `The author argues that ${finalParagraphs[0]}`
    } else if (lang === 'ar' && !finalParagraphs[0].startsWith('يرى الكاتب')) {
      finalParagraphs[0] = `يرى الكاتب أن ${finalParagraphs[0]}`
    }
  }

  return finalParagraphs.join('\n\n')
}

function countWords(text: string): number {
  const cleanText = text.replace(/<[^>]*>/g, '').trim()
  if (!cleanText) return 0
  return cleanText.split(/\s+/).length
}

function generateContentHash(content: string, type: string, lang: 'ar' | 'en'): string {
  const normalizedContent = content
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase()

  const key = `v4-concise-${lang}-${normalizedContent}-${type}`
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
