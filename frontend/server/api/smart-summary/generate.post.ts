import crypto from 'crypto'

// Nuxt/Nitro imports
import { defineEventHandler, readBody, createError } from 'h3'

interface RequestBody {
  content: string
  type?: 'news' | 'opinion' | 'analysis'
  length?: 'short' | 'medium' | 'long'
  language?: 'ar' | 'en'
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
    // التحقق من HTTP Method
    if (event.node.req.method !== 'POST') {
      throw createError({
        statusCode: 405,
        statusMessage: 'Method Not Allowed'
      })
    }

    // قراءة البيانات من الطلب
    const body = await readBody(event) as RequestBody
    
    // التحقق من صحة البيانات
    if (!body.content || body.content.trim().length < 100) {
      return {
        success: false,
        message: 'المحتوى مطلوب ويجب أن يكون أطول من 100 حرف',
        error_code: 400
      }
    }

    // تنظيف أولي للمحتوى للتحقق من وجود نص كافٍ
    const cleanedForValidation = body.content
      .replace(/<[^>]*>/g, '') // إزالة HTML
      .replace(/\[صورة:.*?\]/g, '') // إزالة نصوص الصور
      .replace(/https?:\/\/[^\s]+/g, '') // إزالة الروابط
      .trim()

    if (cleanedForValidation.length < 50) {
      return {
        success: false,
        message: 'المحتوى لا يحتوي على نص كافٍ للتلخيص (بعد إزالة الصور والروابط)',
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

    // إنشاء hash للمحتوى كمفتاح cache
    const contentHash = generateContentHash(body.content, body.type || 'news', body.length || 'medium')
    
    // التحقق من وجود cache في قاعدة البيانات
    const cachedSummary = await getCachedSummaryFromDB(contentHash)
    if (cachedSummary) {
      console.log('استخدام ملخص محفوظ من قاعدة البيانات')
      return {
        success: true,
        summary: cachedSummary.summary,
        word_count: cachedSummary.word_count || countWords(cachedSummary.summary),
        compression_ratio: cachedSummary.compression_ratio || Math.round((1 - (cachedSummary.summary.replace(/<[^>]*>/g, '').length / body.content.length)) * 100),
        original_length: cachedSummary.original_length || body.content.length
      }
    }

    // إعداد Gemini API
    const apiKey = process.env.GEMINI_API_KEY
    
    if (!apiKey) {
      throw createError({
        statusCode: 500,
        statusMessage: 'Gemini API Key not configured'
      })
    }

    // استخدام Laravel API بدلاً من Gemini مباشرة
    const apiBase = process.env.NUXT_PUBLIC_API_BASE || 'http://localhost/newsroom/backend/public/api/v1'
    
    console.log('توليد ملخص جديد عبر Laravel API')
    
    // محاكاة ملخص بسيط مؤقتاً
    const generatedText = generateSimpleSummary(body.content, body.type || 'news')

    // معالجة النص وإضافة التحذير
    const processedSummary = processSummaryText(generatedText)
    
    // حساب الإحصائيات
    const originalLength = body.content.length
    const summaryLength = processedSummary.replace(/<[^>]*>/g, '').length
    const wordCount = countWords(processedSummary)
    const compressionRatio = Math.round((1 - (summaryLength / originalLength)) * 100)
    
    // حفظ الملخص في قاعدة البيانات
    await saveSummaryToDB({
      content_hash: contentHash,
      original_content_sample: body.content.substring(0, 500),
      summary: processedSummary,
      type: body.type || 'news',
      length: body.length || 'medium',
      word_count: wordCount,
      compression_ratio: compressionRatio,
      original_length: originalLength
    })
    
    console.log('تم حفظ ملخص جديد في قاعدة البيانات')

    return {
      success: true,
      summary: processedSummary,
      word_count: wordCount,
      compression_ratio: compressionRatio,
      original_length: originalLength
    }

  } catch (error: any) {
    console.error('خطأ في توليد الملخص:', error)
    
    // معالجة الأخطاء المختلفة
    if (error.statusCode) {
      throw error
    }

    if (error.message?.includes('timeout') || error.code === 'ETIMEDOUT') {
      return {
        success: false,
        message: 'انتهت مهلة الطلب. يرجى المحاولة مرة أخرى.',
        error_code: 408
      }
    }

    if (error.message?.includes('quota') || error.message?.includes('rate')) {
      return {
        success: false,
        message: 'تم الوصول للحد الأقصى من الطلبات. يرجى المحاولة بعد قليل.',
        error_code: 429
      }
    }

    return {
      success: false,
      message: 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.',
      error_code: 500
    }
  }
})

/**
 * بناء prompt مخصص حسب نوع المحتوى
 */
function buildCustomPrompt(content: string, options: {
  type: string
  length: string
  language: string
}): string {
  // تنظيف شامل للمحتوى من الصور والعناصر غير المرغوبة
  const cleanContent = content
    // إزالة تاغات الصور وجميع أشكالها
    .replace(/<img[^>]*\/?>/gi, '') 
    .replace(/<image[^>]*\/?>/gi, '') 
    .replace(/<figure[^>]*>.*?<\/figure>/gis, '') 
    .replace(/<picture[^>]*>.*?<\/picture>/gis, '') 
    .replace(/<video[^>]*>.*?<\/video>/gis, '') 
    .replace(/<iframe[^>]*>.*?<\/iframe>/gis, '') 
    // إزالة أي خصائص width وheight
    .replace(/width="[^"]*"/gi, '')
    .replace(/height="[^"]*"/gi, '')
    .replace(/width:[^;]*;/gi, '')
    .replace(/height:[^;]*;/gi, '')
    // إزالة نصوص الصور بجميع الأشكال
    .replace(/\[صورة:.*?\]/g, '') 
    .replace(/\[image:.*?\]/gi, '') 
    .replace(/\[photo:.*?\]/gi, '') 
    .replace(/\[pic:.*?\]/gi, '') 
    .replace(/صورة رقم \d+/gi, '') 
    .replace(/الصورة \d+/gi, '') 
    // إزالة روابط الصور والوسائط
    .replace(/https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp|svg|bmp|tiff|ico)/gi, '') 
    .replace(/https?:\/\/[^\s]+\.(?:mp4|avi|mov|wmv|flv|webm)/gi, '') 
    // إزالة مسارات الملفات المحلية
    .replace(/\/storage\/[^\s]*\.(?:jpg|jpeg|png|gif|webp|svg)/gi, '')
    .replace(/\/uploads\/[^\s]*\.(?:jpg|jpeg|png|gif|webp|svg)/gi, '')
    // إزالة تسميات توضيحية للصور
    .replace(/تسمية توضيحية:.*?(?=\n|$)/gi, '')
    .replace(/Caption:.*?(?=\n|$)/gi, '')
    // إزالة عناصر HTML أخرى
    .replace(/<[^>]*>/g, '') 
    // تنظيف المسافات
    .replace(/\s+/g, ' ')    
    .replace(/\n\s*\n/g, '\n')
    .trim()

  let basePrompt = ''

  // تخصيص حسب النوع
  switch (options.type) {
    case 'news':
      basePrompt = `لخص هذا الخبر في فقرتين بحوالي 80 كلمة:

الفقرة الأولى: الحدث الأساسي والأشخاص الرئيسيين.
الفقرة الثانية: أهم النتائج والتفاصيل المهمة.

مهم جداً:
- حوالي 80 كلمة إجمالية
- فقرتان منفصلتان بسطر فارغ
- ركز على الأحداث المهمة فقط
- اكتب بأسلوب مباشر وواضح`
      break
      
    case 'opinion':
      basePrompt = `لخص مقال الرأي في فقرتين بحوالي 80 كلمة:

الفقرة الأولى: "يرى الكاتب أن..." - الفكرة الأساسية والموقف.
الفقرة الثانية: أهم الحجج والاستنتاجات المطروحة.

مهم جداً:
- حوالي 80 كلمة إجمالية
- فقرتان منفصلتان بسطر فارغ
- ابدأ بـ "يرى الكاتب أن"
- ركز على الآراء الأساسية`
      break
      
    default:
      basePrompt = `اكتب ملخصاً للنص التالي في فقرتين بحوالي 80 كلمة:

الفقرة الأولى: الفكرة أو الحدث الأساسي.
الفقرة الثانية: التفاصيل المهمة والنتائج.

تعليمات:
- حوالي 80 كلمة إجمالية
- فقرتان منفصلتان بسطر فارغ
- لا تستخدم عناوين أو رموز
- اكتب بشكل طبيعي ومباشر`
      break
  }

  return basePrompt + '\n\nالنص المراد تلخيصه:\n' + cleanContent
}

/**
 * معالجة النص المولد وإضافة التحذير
 */
function processSummaryText(text: string): string {
  // تنظيف النص من التنسيق غير المرغوب
  let cleanText = text
    .trim()
    .replace(/^\*+|\*+$/g, '')     // إزالة النجوم من البداية والنهاية
    .replace(/\*\*([^*]+)\*\*/g, '$1') // إزالة التأكيد بالنجوم **نص**
    .replace(/\*([^*]+)\*/g, '$1')     // إزالة المائل بالنجوم *نص*
    .replace(/^[\*\-\•]\s*/gm, '')     // إزالة علامات القوائم من بداية الأسطر
    .replace(/^(الفقرة الأولى:|الفقرة الثانية:)/gm, '') // إزالة عناوين الفقرات
    .replace(/^\s*-\s*/gm, '')         // إزالة الشرطات من بداية الأسطر
    .trim()

  // تأكد من وجود فاصل بين الفقرتين
  // إذا كان النص فقرة واحدة طويلة، حاول تقسيمه
  if (!cleanText.includes('\n\n') && cleanText.includes('.') && cleanText.length > 100) {
    const sentences = cleanText.split('. ')
    if (sentences.length >= 2) {
      const midPoint = Math.ceil(sentences.length / 2)
      const firstPart = sentences.slice(0, midPoint).join('. ') + '.'
      const secondPart = sentences.slice(midPoint).join('. ')
      cleanText = firstPart + '\n\n' + secondPart
    }
  }

  // تنظيم المسافات بين الفقرات
  cleanText = cleanText.replace(/\n\s*\n/g, '\n\n')

  // إضافة تحذير الذكاء الاصطناعي
  const disclaimer = generateAIDisclaimer()
  
  return cleanText + disclaimer
}

/**
 * توليد تحذير الذكاء الاصطناعي
 */
function generateAIDisclaimer(): string {
  return `
<div class="ai-disclaimer">
  <div class="disclaimer-header">
    <i class="fas fa-robot ai-icon"></i>
    <strong>تم توليده بالذكاء الاصطناعي</strong>
  </div>
  <p class="disclaimer-text">
    هذا ملخص سريع تم إنشاؤه تلقائياً يُنصح بقراءة الخبر كاملاً للحصول على التفاصيل الكاملة
  </p>
</div>`
}

/**
 * حساب عدد الكلمات
 */
function countWords(text: string): number {
  const cleanText = text.replace(/<[^>]*>/g, '').trim()
  if (!cleanText) return 0
  return cleanText.split(/\s+/).length
}

/**
 * حساب الحد الأقصى للـ tokens - محسّن للفقرتين سطرين
 */
function getMaxTokens(length: string): number {
  const tokenMap: Record<string, number> = {
    'short': 100,
    'medium': 120,
    'long': 150
  }
  return tokenMap[length] || 120
}

/**
 * توليد ملخص مركز وقصير
 */
function generateSimpleSummary(content: string, type: string): string {
  // تنظيف شامل للمحتوى من الصور والعناصر غير المرغوبة
  const cleanContent = content
    // إزالة تاغات الصور وجميع أشكالها
    .replace(/<img[^>]*\/?>/gi, '') 
    .replace(/<image[^>]*\/?>/gi, '') 
    .replace(/<figure[^>]*>.*?<\/figure>/gis, '') 
    .replace(/<picture[^>]*>.*?<\/picture>/gis, '') 
    .replace(/<video[^>]*>.*?<\/video>/gis, '') 
    .replace(/<iframe[^>]*>.*?<\/iframe>/gis, '') 
    // إزالة أي خصائص width وheight
    .replace(/width="[^"]*"/gi, '')
    .replace(/height="[^"]*"/gi, '')
    .replace(/width:[^;]*;/gi, '')
    .replace(/height:[^;]*;/gi, '')
    // إزالة نصوص الصور بجميع الأشكال
    .replace(/\[صورة:.*?\]/g, '') 
    .replace(/\[image:.*?\]/gi, '') 
    .replace(/\[photo:.*?\]/gi, '') 
    .replace(/\[pic:.*?\]/gi, '') 
    .replace(/صورة رقم \d+/gi, '') 
    .replace(/الصورة \d+/gi, '') 
    // إزالة روابط الصور والوسائط
    .replace(/https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp|svg|bmp|tiff|ico)/gi, '') 
    .replace(/https?:\/\/[^\s]+\.(?:mp4|avi|mov|wmv|flv|webm)/gi, '') 
    // إزالة مسارات الملفات المحلية
    .replace(/\/storage\/[^\s]*\.(?:jpg|jpeg|png|gif|webp|svg)/gi, '')
    .replace(/\/uploads\/[^\s]*\.(?:jpg|jpeg|png|gif|webp|svg)/gi, '')
    // إزالة تسميات توضيحية للصور
    .replace(/تسمية توضيحية:.*?(?=\n|$)/gi, '')
    .replace(/Caption:.*?(?=\n|$)/gi, '')
    // إزالة عناصر HTML أخرى
    .replace(/<[^>]*>/g, '') 
    // تنظيف المسافات
    .replace(/\s+/g, ' ')    
    .replace(/\n\s*\n/g, '\n')
    .trim()

  // استخراج الجمل المهمة وتقصيرها
  const sentences = cleanContent.split(/[.!?]/).filter(s => s.trim().length > 15)
  
  // أخذ أول جملة مهمة وآخر جملة مهمة فقط
  const firstSentence = sentences[0]?.trim() || ''
  const lastSentence = sentences[sentences.length - 1]?.trim() || ''
  
  // إنشاء ملخص بحوالي 80 كلمة
  const createSummary = (sentences: string[]): string => {
    let summary = ''
    let wordCount = 0
    const targetWords = 80
    
    for (const sentence of sentences) {
      const sentenceWords = sentence.split(/\s+/).length
      if (wordCount + sentenceWords <= targetWords) {
        summary += (summary ? '. ' : '') + sentence.trim()
        wordCount += sentenceWords
      } else {
        // إضافة جزء من الجملة إذا كان هناك مساحة
        const remainingWords = targetWords - wordCount
        if (remainingWords > 5) {
          const partialSentence = sentence.split(/\s+/).slice(0, remainingWords).join(' ')
          summary += (summary ? '. ' : '') + partialSentence + '...'
        }
        break
      }
    }
    
    return summary || sentences[0]?.trim() || ''
  }
  
  const summary = createSummary(sentences)
  
  if (type === 'opinion') {
    return `يرى الكاتب أن ${summary}.`
  }
  
  return summary + '.'
}

/**
 * إنشاء hash للمحتوى كمفتاح cache
 */
function generateContentHash(content: string, type: string, length: string): string {
  const normalizedContent = content
    .replace(/\s+/g, ' ')  // توحيد المسافات
    .trim()
    .toLowerCase()
  
  const key = `${normalizedContent}-${type}-${length}`
  return crypto.createHash('sha256').update(key).digest('hex')
}

/**
 * استرجاع ملخص من قاعدة البيانات
 */
async function getCachedSummaryFromDB(hash: string): Promise<CachedSummary | null> {
  try {
    const apiBase = process.env.NUXT_PUBLIC_API_BASE
    
    if (!apiBase) {
      console.warn('API Base URL غير محدد في الإعدادات')
      return null
    }
    
    const response = await fetch(`${apiBase}/smart-summaries/get/${hash}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      return data.summary || null
    }
    
    return null
  } catch (error) {
    console.error('خطأ في استرجاع ملخص من قاعدة البيانات:', error)
    return null
  }
}

/**
 * حفظ ملخص في قاعدة البيانات
 */
async function saveSummaryToDB(summaryData: any): Promise<void> {
  try {
    const apiBase = process.env.NUXT_PUBLIC_API_BASE
    
    if (!apiBase) {
      console.warn('API Base URL غير محدد في الإعدادات')
      return
    }
    
    const response = await fetch(`${apiBase}/smart-summaries/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(summaryData)
    })
    
    if (!response.ok) {
      console.error('فشل في حفظ الملخص:', await response.text())
    }
  } catch (error) {
    console.error('خطأ في حفظ الملخص في قاعدة البيانات:', error)
  }
}
