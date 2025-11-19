<template>
  <div class="smart-summary-container">
    <!-- زر توليد الملخص -->
    <div class="summary-button-container">
      <!-- زر توليد الملخص -->
      <button 
        @click="generateSummary"
        :disabled="isGenerating || !props.content"
        class="smart-summary-button"
      >
        <i :class="getButtonIcon"></i>
        <span v-if="isGenerating">جاري التوليد...</span>
        <span v-else-if="currentSummary && showSummary">إخفاء الملخص</span>
        <span v-else-if="currentSummary && !showSummary">إظهار الملخص</span>
        <span v-else>ملخص سريع</span>
        <div v-if="isGenerating" class="loading-dots">
          <span></span><span></span><span></span>
        </div>
      </button>
    </div>

    <!-- تم حذف مؤشر التحميل -->

    <!-- عرض الملخص -->
    <div v-if="showSummary && currentSummary" class="summary-result">
      <div class="summary-header">
        <h3 class="summary-title">
          <i class="fas fa-file-alt summary-icon"></i>
          الملخص
        </h3>
      </div>

      <!-- محتوى الملخص -->
      <div class="summary-content">
        <div v-if="isTyping" class="typing-effect">
          <span>{{ typedSummary }}</span>
          <span class="typing-cursor">|</span>
        </div>
        <div v-else v-html="processedSummary" class="summary-text"></div>
      </div>

      <!-- تحذير الذكاء الاصطناعي -->
      <div v-html="aiDisclaimer" class="ai-disclaimer"></div>
    </div>

    <!-- رسائل الخطأ -->
    <div v-if="error" class="error-container">
      <div class="error-content">
        <i class="fas fa-exclamation-triangle error-icon"></i>
        <div class="error-text">
          <h4 class="error-title">حدث خطأ</h4>
          <p class="error-message">{{ error }}</p>
        </div>
        <button @click="clearError" class="error-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  articleId?: number
  content?: string
  type?: 'news' | 'opinion' | 'analysis'
  length?: 'short' | 'medium' | 'long'
  autoGenerate?: boolean
}

interface SummaryData {
  summary: string
  word_count: number
  compression_ratio: number
  original_length: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'news',
  length: 'medium',
  autoGenerate: false
})

// تم حذف refs غير المستخدمة

// State
const currentSummary = ref('')
const summaryData = ref<SummaryData>()
const isGenerating = ref(false)
const showSummary = ref(false)
const error = ref('')
const isTyping = ref(false)
const typedSummary = ref('')
const hasUsedSummary = ref(false)
const startTime = ref(0)
const generationTime = ref(0)

// تم حذف loading animation

// Computed
const hasSummary = computed(() => !!currentSummary.value)

const processedSummary = computed(() => {
  if (!currentSummary.value) return ''
  
  // فصل المحتوى عن التحذير
  const parts = currentSummary.value.split('<div class=\"ai-disclaimer\"')
  return parts[0]?.trim() || ''
})

const aiDisclaimer = computed(() => {
  if (!currentSummary.value) return ''
  
  const parts = currentSummary.value.split('<div class="ai-disclaimer"')
  return parts[1] ? '<div class="ai-disclaimer"' + parts[1] : ''
})

const getButtonIcon = computed(() => {
  if (isGenerating.value) return 'fas fa-brain fa-spin'
  if (currentSummary.value && showSummary.value) return 'fas fa-eye-slash'
  if (currentSummary.value && !showSummary.value) return 'fas fa-database'
  return 'fas fa-brain'
})

// API Service
const config = useRuntimeConfig()

const generateSummary = async () => {
  if (isGenerating.value) return
  
  // إذا كان الملخص موجود، فقط أظهره/أخفه
  if (currentSummary.value) {
    showSummary.value = !showSummary.value
    return
  }
  
  try {
    isGenerating.value = true
    error.value = ''
    startTime.value = Date.now()
    
    // استخدام Nuxt server API
    const payload = {
      content: props.content || '',
      type: props.type,
      length: props.length
    }
    
    const result: any = await $fetch('/api/smart-summary/generate', {
      method: 'POST',
      body: payload
    })
    
    if (result.success && result.summary) {
      summaryData.value = {
        summary: result.summary,
        word_count: result.word_count,
        compression_ratio: result.compression_ratio,
        original_length: result.original_length
      }
      currentSummary.value = result.summary
      hasUsedSummary.value = true
      generationTime.value = Math.round((Date.now() - startTime.value) / 1000)
      
      showSummary.value = true
      
      // تشغيل تأثير الكتابة
      await startTypingEffect(processedSummary.value)
      
    } else {
      throw new Error(result.message || 'فشل في توليد الملخص')
    }
    
  } catch (err: any) {
    console.error('خطأ في توليد الملخص:', err)
    error.value = getErrorMessage(err)
  } finally {
    isGenerating.value = false
  }
}

const toggleSummary = () => {
  if (hasSummary.value) {
    showSummary.value = !showSummary.value
  } else {
    generateSummary()
  }
}

// تم حذف دوال copySummary و regenerateSummary

const shareSummary = () => {
  if (navigator.share) {
    navigator.share({
      title: 'ملخص ذكي',
      text: processedSummary.value,
      url: window.location.href
    })
  } else {
    // Fallback - نسخ رابط + ملخص
    const shareText = `${processedSummary.value}\n\n${window.location.href}`
    navigator.clipboard.writeText(shareText)
  }
}

const clearError = () => {
  error.value = ''
}

// تم حذف startLoadingAnimation

const startTypingEffect = async (text: string) => {
  if (!text) return
  
  isTyping.value = true
  typedSummary.value = ''
  
  const cleanText = text.replace(/<[^>]*>/g, '')
  const speed = 15 // milliseconds per character - أسرع
  
  for (let i = 0; i <= cleanText.length; i++) {
    typedSummary.value = cleanText.slice(0, i)
    await new Promise(resolve => setTimeout(resolve, speed))
  }
  
  isTyping.value = false
}

const getErrorMessage = (err: any) => {
  if (err.status === 408 || err.message?.includes('timeout')) {
    return 'استغرق الطلب وقتاً أطول من المتوقع. يرجى المحاولة مرة أخرى.'
  }
  if (err.status === 429) {
    return 'تم الوصول للحد الأقصى من الطلبات. يرجى المحاولة بعد قليل.'
  }
  if (err.status === 422) {
    return 'المحتوى غير صالح للتلخيص. يرجى التأكد من أن النص يحتوي على محتوى كافٍ.'
  }
  return err.message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'
}

// Auto-generate on mount if requested
onMounted(async () => {
  hasUsedSummary.value = localStorage.getItem('has_used_smart_summary') === 'true'
  
  if (props.content) {
    // التحقق من وجود cache أولاً
    await checkForExistingSummary()
  }
  
  if (props.autoGenerate && (props.content || props.articleId)) {
    generateSummary()
  }
})


// دالة للتحقق من وجود ملخص محفوظ
const checkForExistingSummary = async () => {
  if (!props.content) return
  
  try {
    // إنشاء نفس الـ hash المستخدم في الـ API
    const contentHash = await generateContentHash(props.content, props.type || 'news', props.length || 'medium')
    console.log('🔍 البحث عن cache بـ hash:', contentHash)
    
    // استخدام رابط نسبي فقط - ديناميكي لأي موقع
    const response = await fetch(`/api/v1/smart-summaries/get/${contentHash}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      if (data.success && data.summary) {
        console.log('✅ تم العثور على ملخص محفوظ - تم الاسترجاع من قاعدة البيانات')
        currentSummary.value = data.summary.summary
        summaryData.value = {
          summary: data.summary.summary,
          word_count: data.summary.word_count,
          compression_ratio: data.summary.compression_ratio,
          original_length: data.summary.original_length
        }
        hasUsedSummary.value = true
        // لا نظهر الملخص تلقائياً، المستخدم يحدد
        // showSummary.value = true
      }
    }
  } catch (error) {
    console.log('لا يوجد ملخص محفوظ، سيتم التوليد عند الطلب')
  }
}

// دالة لإنشاء hash مطابقة للـ API
const generateContentHash = async (content: string, type: string, length: string): Promise<string> => {
  const normalizedContent = content
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase()
  
  const key = `${normalizedContent}-${type}-${length}`
  
  // استخدام Web Crypto API
  const encoder = new TextEncoder()
  const data = encoder.encode(key)
  const hashBuffer = await crypto.subtle.digest('SHA-256', data)
  const hashArray = Array.from(new Uint8Array(hashBuffer))
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('')
}

// Store usage in localStorage
watchEffect(() => {
  if (hasUsedSummary.value) {
    localStorage.setItem('has_used_smart_summary', 'true')
  }
})

onMounted(() => {
  hasUsedSummary.value = localStorage.getItem('has_used_smart_summary') === 'true'
})
</script>

<style>
  /* الأساسي */
  .smart-summary-container {
    margin: 1.5rem 0;
    direction: rtl;
    text-align: right;
    font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  /* زر الملخص السريع - اللون #1e2a4a */
  .smart-summary-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    background: #1e2a4a;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(30, 42, 74, 0.3);
    position: relative;
    overflow: hidden;
    font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  .smart-summary-button:hover:not(:disabled) {
    background: #243355;
    box-shadow: 0 6px 20px rgba(30, 42, 74, 0.4);
    transform: translateY(-2px);
  }

  .smart-summary-button:disabled {
    opacity: 0.8;
    cursor: not-allowed;
    transform: none;
  }

  .smart-summary-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(30, 42, 74, 0.3);
  }

  /* Loading dots animation */
  .loading-dots {
    display: inline-flex;
    gap: 2px;
    margin-right: 5px;
  }

  .loading-dots span {
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    animation: loading-bounce 1.4s infinite ease-in-out;
  }

  .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
  .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

  @keyframes loading-bounce {
    0%, 80%, 100% {
      transform: scale(0.8);
      opacity: 0.5;
    }
    40% {
      transform: scale(1);
      opacity: 1;
    }
  }

.smart-summary-btn.generating {
  background: linear-gradient(135deg, #a855f7, #ec4899);
  animation: pulse-glow 2s infinite;
}

.smart-summary-btn.has-summary {
  background: linear-gradient(135deg, #10b981, #14b8a6);
}

.smart-summary-btn.hidden-summary {
  background: linear-gradient(135deg, #6b7280, #3b82f6);
}

.button-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.button-icon {
  font-size: 1.25rem;
}

.button-text {
  font-weight: 600;
}

.new-badge {
  background: #ef4444;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  animation: pulse 2s infinite;
}

/* تم حذف CSS مؤشر التحميل */

/* عرض الملخص - متناسق مع الموقع */
.summary-result {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 25px rgba(30, 42, 74, 0.1);
  overflow: hidden;
  margin-top: 1rem;
}

.summary-header {
  background: #1e2a4a;
  color: white;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.summary-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
}

.compression-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  margin-left: 0.5rem;
}

/* أيقونات ملونة */
.ai-icon {
  color: #a78bfa !important;
}

.summary-icon {
  color: rgba(255, 255, 255, 0.9) !important;
  margin-left: 0.5rem;
}

.summary-content {
  padding: 1.75rem 1.5rem;
  line-height: 1.8;
}

.typing-effect {
  font-size: 1.125rem;
  line-height: 1.75;
  color: #374151;
}

.typing-cursor {
  animation: pulse 1s infinite;
  color: #1e2a4a;
  font-weight: 700;
}

.summary-text {
  font-size: 1.125rem;
  line-height: 1.8;
  color: #2d3748;
  font-weight: 400;
}

.summary-text strong {
  color: #1e2a4a;
  font-weight: 600;
}

/* AI Disclaimer - متناسق مع لون الموقع */
.ai-disclaimer {
  background: linear-gradient(135deg, #f8fafc, #edf2f7);
  border: 1px solid rgba(30, 42, 74, 0.1);
  border-radius: 12px;
  padding: 1.25rem;
  margin-top: 1.5rem;
  font-size: 0.9rem;
  box-shadow: 0 2px 8px rgba(30, 42, 74, 0.05);
}

.disclaimer-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #1e2a4a;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.disclaimer-header i {
  font-size: 1.1rem;
}

.disclaimer-text {
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
  font-size: 0.9rem;
}

/* رسائل الخطأ */
.error-container {
  background: #fef2f2;
  border: 2px solid #fecaca;
  border-radius: 0.75rem;
  padding: 1rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.error-content {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.error-icon {
  color: #ef4444;
  font-size: 1.5rem;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.error-text {
  flex: 1;
}

.error-title {
  font-weight: 700;
  color: #991b1b;
  font-size: 1.125rem;
}

.error-message {
  color: #991b1b;
  font-size: 0.875rem;
  margin: 0;
}

.error-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 0.25rem;
  transition: all 0.3s ease;
}

.error-close:hover {
  background: #f3f4f6;
  color: #374151;
}

/* Responsive Design - للهاتف والكمبيوتر */
@media (max-width: 768px) {
  .smart-summary-container {
    margin: 1rem 0;
  }
  
  .smart-summary-button {
    width: 100%;
    justify-content: center;
    padding: 1rem 1.5rem;
    font-size: 1rem;
  }
  
  .summary-header {
    padding: 1rem 1.25rem;
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .summary-title {
    justify-content: center;
    font-size: 1rem;
  }
  
  .summary-content {
    padding: 1.5rem 1.25rem;
  }
  
  .summary-text {
    font-size: 1rem;
    line-height: 1.7;
  }
  
  .ai-disclaimer {
    padding: 1rem;
    margin-top: 1.25rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .smart-summary-button {
    padding: 0.875rem 1.25rem;
    font-size: 0.95rem;
    gap: 0.5rem;
  }
  
  .summary-content {
    padding: 1.25rem 1rem;
  }
  
  .summary-text {
    font-size: 0.95rem;
  }
  
  .control-btn {
    padding: 0.375rem;
  }
}

/* Animations */
@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(147, 51, 234, 0.5);
  }
  50% {
    box-shadow: 0 0 40px rgba(147, 51, 234, 0.8);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Responsive */
@media (max-width: 640px) {
  .summary-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .smart-summary-btn {
    font-size: 1rem;
    padding: 0.75rem 1rem;
  }
}
</style>
