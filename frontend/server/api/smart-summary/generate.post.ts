import { GoogleGenAI } from '@google/genai'

interface RequestBody {
  content: string
  type?: 'news' | 'opinion' | 'analysis'
  length?: 'short' | 'medium' | 'long'
  language?: 'ar' | 'en'
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

    if (body.content.length > 50000) {
      return {
        success: false,
        message: 'المحتوى طويل جداً (الحد الأقصى 50000 حرف)',
        error_code: 400
      }
    }

    // إعداد Gemini AI
    const apiKey = useRuntimeConfig().geminiApiKey
    
    if (!apiKey) {
      throw createError({
        statusCode: 500,
        statusMessage: 'Gemini API Key not configured'
      })
    }

    const ai = new GoogleGenAI({
      apiKey: apiKey
    })

    // بناء prompt مخصص
    const prompt = buildCustomPrompt(body.content, {
      type: body.type || 'news',
      length: body.length || 'medium',
      language: body.language || 'ar'
    })

    // إعداد المحتوى
    const contents = [
      {
        role: 'user' as const,
        parts: [
          {
            text: prompt
          }
        ]
      }
    ]

    // توليد المحتوى مع النموذج
    // @ts-ignore - تجاهل خطأ TypeScript مؤقتاً
    const response = await ai.models.generateContent({
      model: 'gemini-flash-latest', 
      contents: contents
    })

    // استخراج النص من الاستجابة
    let generatedText = ''
    if (response.candidates && response.candidates.length > 0) {
      const candidate = response.candidates[0]
      if (candidate.content && candidate.content.parts && candidate.content.parts.length > 0) {
        generatedText = candidate.content.parts[0].text || ''
      }
    }

    if (!generatedText.trim()) {
      return {
        success: false,
        message: 'فشل في توليد الملخص',
        error_code: 500
      }
    }

    // معالجة النص وإضافة التحذير
    const processedSummary = processSummaryText(generatedText)
    
    // حساب الإحصائيات
    const originalLength = body.content.length
    const summaryLength = processedSummary.replace(/<[^>]*>/g, '').length
    const wordCount = countWords(processedSummary)
    const compressionRatio = Math.round((1 - (summaryLength / originalLength)) * 100)

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
  // تنظيف المحتوى
  const cleanContent = content
    .replace(/<[^>]*>/g, '') // إزالة HTML
    .replace(/\s+/g, ' ')    // دمج المسافات
    .trim()

  let basePrompt = 'لخص الخبر التالي في فقرتين قصيرتين فقط باللغة العربية:\n\n'
  
  basePrompt += 'الفقرة الأولى: اذكر الحدث الرئيسي ومن المعني به (جملة واحدة أو جملتين).\n\n'
  basePrompt += 'الفقرة الثانية: اذكر التفاصيل المهمة والنتائج (جملة واحدة أو جملتين).\n\n'
  
  basePrompt += 'ضع سطر فارغ بين الفقرتين. لا تضع أي عناوين أو ترقيم.\n\n'

  return basePrompt + 'النص المراد تلخيصه:\n' + cleanContent
}

/**
 * معالجة النص المولد وإضافة التحذير
 */
function processSummaryText(text: string): string {
  // تنظيف النص
  const cleanText = text
    .trim()
    .replace(/^[\*\-\•]\s*/, '') // إزالة علامات القوائم
    .replace(/\n\s*\n/g, '\n')  // دمج الأسطر الفارغة

  // إضافة تحذير الذكاء الاصطناعي
  const disclaimer = generateAIDisclaimer()
  
  return cleanText + disclaimer
}

/**
 * توليد تحذير الذكاء الاصطناعي
 */
function generateAIDisclaimer(): string {
  return `
<div style="
  margin-top: 16px; 
  padding: 16px; 
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
  border: 1px solid #bae6fd; 
  border-radius: 12px; 
  font-size: 13px; 
  color: #0369a1; 
  line-height: 1.5;
  box-shadow: 0 4px 12px rgba(3, 105, 161, 0.1);
">
  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
    <svg style="
      color: #0ea5e9; 
      width: 16px; 
      height: 16px; 
      padding: 6px; 
      background: rgba(14, 165, 233, 0.1); 
      border-radius: 50%; 
      animation: pulse 2s infinite;
    " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 8V4H8"/>
      <rect width="16" height="12" x="4" y="8" rx="2"/>
      <path d="M2 14h2"/>
      <path d="M20 14h2"/>
      <path d="M15 13v2"/>
      <path d="M9 13v2"/>
    </svg>
    <strong style="color: #0369a1; font-size: 14px;">ملخص مولد بالذكاء الاصطناعي</strong>
  </div>
  <p style="margin: 0; font-size: 12px; color: #0284c7;">
    هذا ملخص سريع وذكي • يُنصح بالرجوع للنص الأصلي للتفاصيل الكاملة
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
 * حساب الحد الأقصى للـ tokens - محسّن للسرعة
 */
function getMaxTokens(length: string): number {
  const tokenMap: Record<string, number> = {
    'short': 80,
    'medium': 120,
    'long': 180
  }
  return tokenMap[length] || 120
}
