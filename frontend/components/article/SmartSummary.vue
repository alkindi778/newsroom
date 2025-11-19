<template>
  <div class="smart-summary-container">
    <!-- زر توليد الملخص -->
    <div class="summary-button-container">
      <button
        ref="summaryBtn"
        @click="toggleSummary"
        :disabled="isGenerating"
        class="smart-summary-btn"
        :class="{
          'generating': isGenerating,
          'has-summary': currentSummary,
          'hidden-summary': hasSummary && !showSummary
        }"
      >
        <div class="button-content">
          <component :is="'div'" class="button-icon">
            <!-- Loading icon -->
            <svg v-if="getButtonIcon === 'loading'" :class="{ 'rotating': isGenerating }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12a9 9 0 11-6.219-8.56"/>
            </svg>
            <!-- Eye icon -->
            <svg v-else-if="getButtonIcon === 'eye'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <!-- Eye-off icon -->
            <svg v-else-if="getButtonIcon === 'eye-off'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m15 18-.722-3.25"/>
              <path d="M2 12s3-7 10-7c1.763 0 3.37.487 4.69 1.273"/>
              <path d="m22 22-5-5"/>
              <path d="m17 17-.5-1.5"/>
              <path d="M9.681 9.681a3 3 0 1 0 4.24 4.24"/>
              <path d="M6.5 6.5A10 10 0 0 0 2 12s3 7 10 7c1.454 0 2.807-.31 4.068-.832"/>
            </svg>
            <!-- Sparkles icon -->
            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.582a.5.5 0 0 1 0 .963L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
            </svg>
          </component>
          <span class="button-text">{{ getButtonText }}</span>
          <span v-if="!hasUsedSummary && !currentSummary" class="new-badge">جديد</span>
        </div>
      </button>
    </div>

    <!-- عرض الملخص -->
    <div v-if="showSummary && currentSummary" class="summary-result">
      <div class="summary-header">
        <h3 class="summary-title">
          <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.582a.5.5 0 0 1 0 .963L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
          </svg>
          الملخص الذكي
          <span v-if="summaryData?.compression_ratio" class="compression-badge">
            ضغط {{ summaryData.compression_ratio }}%
          </span>
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

// Refs
const summaryBtn = ref<HTMLButtonElement>()
const summaryContent = ref<HTMLDivElement>()

// State
const currentSummary = ref('')
const summaryData = ref<SummaryData>()
const isGenerating = ref(false)
const showSummary = ref(false)
const error = ref('')
const isTyping = ref(false)
const typedSummary = ref('')
const copySuccess = ref(false)
const hasUsedSummary = ref(false)
const startTime = ref(0)
const generationTime = ref(0)


// Computed
const hasSummary = computed(() => !!currentSummary.value)
const getButtonIcon = computed(() => {
  if (isGenerating.value) return 'loading'
  if (hasSummary.value && !showSummary.value) return 'eye'
  if (hasSummary.value && showSummary.value) return 'eye-off'
  return 'sparkles'
})

const getButtonText = computed(() => {
  if (isGenerating.value) return 'جاري التوليد...'
  if (hasSummary.value && !showSummary.value) return 'عرض الملخص'
  if (hasSummary.value && showSummary.value) return 'إخفاء الملخص'
  return 'ملخص ذكي بالذكاء الاصطناعي'
})

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

const wordCount = computed(() => {
  if (!props.content) return 0
  return props.content.split(/\s+/).length
})

const estimatedTime = computed(() => {
  const baseTime = 8
  const lengthMultiplier: Record<string, number> = {
    short: 0.7,
    medium: 1,
    long: 1.3
  }
  return Math.round(baseTime * (lengthMultiplier[props.length] || 1))
})

// API Service
const config = useRuntimeConfig()

const generateSummary = async () => {
  if (isGenerating.value) return
  
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
    
    if (result.success) {
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

const regenerateSummary = async () => {
  currentSummary.value = ''
  summaryData.value = undefined
  showSummary.value = false
  await generateSummary()
}

const copySummary = async () => {
  try {
    await navigator.clipboard.writeText(processedSummary.value)
    copySuccess.value = true
    setTimeout(() => copySuccess.value = false, 2000)
  } catch (err) {
    // Fallback للمتصفحات القديمة
    const textArea = document.createElement('textarea')
    textArea.value = processedSummary.value
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand('copy')
    document.body.removeChild(textArea)
    copySuccess.value = true
    setTimeout(() => copySuccess.value = false, 2000)
  }
}

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

// Animation functions

const startTypingEffect = async (text: string) => {
  if (!text) return
  
  isTyping.value = true
  typedSummary.value = ''
  
  const cleanText = text.replace(/<[^>]*>/g, '')
  const speed = 30 // milliseconds per character
  
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
onMounted(() => {
  if (props.autoGenerate && (props.content || props.articleId)) {
    generateSummary()
  }
})

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

<style scoped>
.smart-summary-container {
  width: 100%;
  max-width: none;
}

.summary-button-container {
  margin-bottom: 1rem;
}

/* الزر الرئيسي */
.smart-summary-btn {
  background: #007bff;
  border: 1px solid #007bff;
  color: white;
  padding: 10px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: 'Azer', sans-serif;
}
.smart-summary-btn:hover:not(:disabled) {
  background: #0056b3;
  border-color: #0056b3;
}

.smart-summary-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.smart-summary-btn.generating {
  background: #6c757d;
  border-color: #6c757d;
}

.smart-summary-btn.has-summary {
  background: #28a745;
  border-color: #28a745;
}

.smart-summary-btn.hidden-summary {
  background: #6c757d;
  border-color: #6c757d;
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


/* عرض الملخص */
.summary-result {
  background: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%);
  border-radius: 16px;
  border: 1px solid #e3e8f0;
  box-shadow: 0 8px 32px rgba(30, 42, 74, 0.12);
  overflow: hidden;
  margin-top: 20px;
  position: relative;
}

.summary-result::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #1E2A4A 0%, #4285f4 50%, #1E2A4A 100%);
}

.summary-header {
  background: linear-gradient(135deg, #1E2A4A 0%, #2d3f5f  100%);
  color: white;
  padding: 16px 20px;
  position: relative;
  overflow: hidden;
}

.summary-header::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100%;
  background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
}

.summary-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  flex-wrap: nowrap;
  position: relative;
  z-index: 1;
}

.summary-title .summary-icon {
  color: #ffd700;
  filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.3));
  animation: sparkle 2s ease-in-out infinite alternate;
}

@keyframes sparkle {
  0% { transform: scale(1); opacity: 0.8; }
  100% { transform: scale(1.1); opacity: 1; }
}

.rotating {
  animation: rotate 1s linear infinite;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.compression-badge {
  background: linear-gradient(135deg, #00d4aa 0%, #00a085 100%);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  margin-left: 8px;
  box-shadow: 0 2px 8px rgba(0, 212, 170, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.2);
}


.summary-content {
  padding: 24px;
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
}

.typing-effect {
  font-size: 16px;
  line-height: 1.7;
  color: #2d3748;
  white-space: pre-line;
  font-family: 'Azer', sans-serif;
  text-align: justify;
  margin-bottom: 16px;
}

.typing-cursor {
  animation: blink 1s infinite;
  color: #1E2A4A;
  font-weight: 700;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.summary-text {
  font-size: 16px;
  line-height: 1.7;
  color: #2d3748;
  white-space: pre-line;
  font-family: 'Azer', sans-serif;
  text-align: justify;
  margin-bottom: 16px;
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
  color: #b91c1c;
  margin-top: 0.25rem;
}

/* Responsive للهواتف */
@media (max-width: 768px) {
  .smart-summary-btn {
    width: 100%;
    padding: 12px 16px;
    font-size: 16px;
    justify-content: center;
  }
  
  .summary-result {
    margin-top: 16px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(30, 42, 74, 0.15);
  }
  
  .summary-header {
    padding: 14px 16px;
  }
  
  .summary-title {
    font-size: 15px;
    gap: 6px;
  }
  
  .summary-content {
    padding: 18px;
  }
  
  .typing-effect, .summary-text {
    font-size: 15px;
    line-height: 1.6;
  }
  
  .compression-badge {
    font-size: 11px;
    padding: 2px 6px;
  }
  
  
  .error-container {
    border-radius: 6px;
    padding: 12px;
  }
  
  .new-badge {
    font-size: 11px;
    padding: 2px 6px;
  }
}

.error-close {
  color: #ef4444;
  background: none;
  border: none;
  padding: 0.25rem;
  cursor: pointer;
  flex-shrink: 0;
}

.error-close:hover {
  color: #b91c1c;
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
