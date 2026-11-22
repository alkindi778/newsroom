<template>
  <div class="smart-summary-wrapper" dir="rtl">
    <!-- شريط زر الملخص الذكي -->
    <div class="summary-trigger-bar">
      <button
        type="button"
        @click="generateSummary"
        :disabled="isGenerating || !props.content"
        class="smart-summary-btn"
        :class="{ 'is-active': showSummary && currentSummary, 'is-loading': isGenerating }"
      >
        <!-- أيقونة الذكاء الاصطناعي SVG -->
        <span class="btn-icon-wrap">
          <svg
            v-if="isGenerating"
            class="icon-svg spin-anim"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <svg
            v-else-if="currentSummary && showSummary"
            class="icon-svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
          </svg>
          <svg
            v-else
            class="icon-svg sparkle-anim"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
          </svg>
        </span>

        <span class="btn-label">
          <template v-if="isGenerating">جاري تلخيص الخبر...</template>
          <template v-else-if="currentSummary && showSummary">إخفاء الملخص الذكي</template>
          <template v-else-if="currentSummary && !showSummary">عرض الملخص الذكي</template>
          <template v-else>ملخص ذكي وموجز للخبر</template>
        </span>

        <span class="ai-pill-badge">AI</span>
      </button>
    </div>

    <!-- بطاقة عرض الملخص الاحترافية -->
    <transition name="summary-fade">
      <div v-if="showSummary && summaryParagraphs.length > 0" class="summary-card">
        <!-- رأس البطاقة -->
        <div class="summary-card-header">
          <div class="header-right">
            <div class="header-icon-box">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <div>
              <h3 class="header-title">خلاصة الخبر بالذكاء الاصطناعي</h3>
              <p class="header-subtitle">ملخص صحفي مكثف لأهم ما ورد في الخبر</p>
            </div>
          </div>

          <div class="header-actions">
            <button
              type="button"
              @click="copySummaryText"
              class="action-copy-btn"
              :title="copied ? 'تم النسخ' : 'نسخ الملخص'"
            >
              <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <svg v-else class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ copied ? 'تم النسخ' : 'نسخ' }}</span>
            </button>
          </div>
        </div>

        <!-- جسم الملخص: فقرات منسقة ومختصرة -->
        <div class="summary-card-body">
          <div class="paragraphs-list">
            <div
              v-for="(paragraph, idx) in displayedParagraphs"
              :key="idx"
              class="summary-paragraph-item"
            >
              <span class="paragraph-bullet" :class="idx === 0 ? 'bullet-primary' : 'bullet-secondary'"></span>
              <p class="paragraph-text">
                {{ paragraph }}
                <span v-if="isTyping && idx === displayedParagraphs.length - 1" class="typing-cursor">|</span>
              </p>
            </div>
          </div>

          <!-- تذييل البطاقة (تنويه الذكاء الاصطناعي) -->
          <div class="summary-card-footer">
            <div class="footer-note">
              <svg class="footer-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>تم توليد هذا الملخص الموجز آلياً لتقديم نظرة سريعة، وللتفاصيل الكاملة يُرجى قراءة نص الخبر أدناه.</span>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- رسائل الخطأ -->
    <div v-if="error" class="summary-error-box">
      <div class="error-inner">
        <svg class="error-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="error-msg">{{ error }}</p>
        <button type="button" @click="clearError" class="error-dismiss" aria-label="إغلاق">
          ✕
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
  length: 'short',
  autoGenerate: false
})

const currentSummary = ref('')
const summaryData = ref<SummaryData>()
const isGenerating = ref(false)
const showSummary = ref(false)
const error = ref('')
const isTyping = ref(false)
const typedCharsCount = ref(0)
const hasUsedSummary = ref(false)
const copied = ref(false)

/**
 * تقسيم الملخص وتنسيقه إلى فقرتين قصيرتين ومختصرتين
 */
const summaryParagraphs = computed<string[]>(() => {
  if (!currentSummary.value) return []

  // فصل المحتوى عن أي تحذير قديم مضمن
  const raw = (currentSummary.value.split('<div class="ai-disclaimer"')[0] || '')
    .replace(/<[^>]*>/g, '')
    .replace(/&amp;nbsp;|&nbsp;|\u00A0/gi, ' ')
    .replace(/^(الفقرة الأولى|الفقرة الثانية|ملخص الخبر|الملخص)\s*[:：\-]?\s*/gim, '')
    .trim()

  if (!raw) return []

  let parts = raw
    .split(/\n\s*\n|\n/)
    .map(p => p.trim())
    .filter(p => p.length > 8)

  if (parts.length === 1) {
    const sentences = parts[0]!
      .split(/[.!؟]+/)
      .map(s => s.trim())
      .filter(s => s.length > 10)

    if (sentences.length >= 2) {
      parts = [sentences[0]!, sentences.slice(1, 2).join('. ')]
    } else {
      const words = parts[0]!.split(/\s+/)
      if (words.length > 24) {
        const mid = Math.min(22, Math.ceil(words.length / 2))
        parts = [words.slice(0, mid).join(' '), words.slice(mid, 44).join(' ')]
      }
    }
  }

  // ضمان الاختصار الشديد (فقرتان قصيرتان بحد أقصى ~24 كلمة لكل فقرة)
  return parts.slice(0, 2).map((p, idx) => {
    const words = p.split(/\s+/)
    const maxWords = idx === 0 ? 25 : 23
    let text = words.length > maxWords ? words.slice(0, maxWords).join(' ') : p
    text = text.replace(/[،,.\s]+$/, '')
    if (!/[.!؟]$/.test(text)) {
      text += '.'
    }
    return text
  })
})

/**
 * الفقرات المعروضة أثناء وبعد تأثير الكتابة السلس
 */
const displayedParagraphs = computed<string[]>(() => {
  if (!isTyping.value) {
    return summaryParagraphs.value
  }

  const result: string[] = []
  let remaining = typedCharsCount.value

  for (const para of summaryParagraphs.value) {
    if (remaining <= 0) break
    if (remaining >= para.length) {
      result.push(para)
      remaining -= para.length
    } else {
      result.push(para.slice(0, remaining))
      remaining = 0
    }
  }

  return result.length > 0 ? result : ['']
})

const startTypingEffect = async () => {
  const totalChars = summaryParagraphs.value.reduce((acc, p) => acc + p.length, 0)
  if (totalChars === 0) return

  isTyping.value = true
  typedCharsCount.value = 0

  const step = Math.max(2, Math.ceil(totalChars / 45))
  while (typedCharsCount.value < totalChars) {
    typedCharsCount.value = Math.min(totalChars, typedCharsCount.value + step)
    await new Promise(resolve => setTimeout(resolve, 16))
  }

  isTyping.value = false
}

const generateSummary = async () => {
  if (isGenerating.value) return

  if (currentSummary.value) {
    showSummary.value = !showSummary.value
    return
  }

  try {
    isGenerating.value = true
    error.value = ''

    const payload = {
      content: props.content || '',
      type: props.type,
      length: 'short'
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
      showSummary.value = true

      await startTypingEffect()
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

const copySummaryText = async () => {
  if (!process.client || !navigator.clipboard || summaryParagraphs.value.length === 0) return
  try {
    await navigator.clipboard.writeText(summaryParagraphs.value.join('\n\n'))
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch {
    // ignore copy error
  }
}

const clearError = () => {
  error.value = ''
}

const getErrorMessage = (err: any) => {
  if (err.status === 408 || err.message?.includes('timeout')) {
    return 'استغرق الطلب وقتاً أطول من المتوقع. يرجى المحاولة مرة أخرى.'
  }
  if (err.status === 422) {
    return 'المحتوى قصير جداً ولا يحتاج إلى تلخيص.'
  }
  return err.message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'
}

const checkForExistingSummary = async () => {
  if (!props.content) return

  try {
    const result: any = await $fetch('/api/smart-summary/generate', {
      method: 'POST',
      body: {
        content: props.content || '',
        type: props.type,
        length: 'short',
        onlyCache: true
      }
    })

    if (result.success && result.summary) {
      currentSummary.value = result.summary
      summaryData.value = {
        summary: result.summary,
        word_count: result.word_count,
        compression_ratio: result.compression_ratio,
        original_length: result.original_length
      }
      hasUsedSummary.value = true
    }
  } catch {
    // لا يوجد ملخص محفوظ مسبقاً
  }
}

watchEffect(() => {
  if (hasUsedSummary.value && process.client) {
    localStorage.setItem('has_used_smart_summary', 'true')
  }
})

onMounted(async () => {
  if (process.client) {
    hasUsedSummary.value = localStorage.getItem('has_used_smart_summary') === 'true'
  }

  if (props.content) {
    await checkForExistingSummary()
  }

  if (props.autoGenerate && (props.content || props.articleId)) {
    generateSummary()
  }
})
</script>

<style scoped>
.smart-summary-wrapper {
  margin: 1.5rem 0;
  direction: rtl;
  text-align: right;
  font-family: 'Azer', 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, sans-serif;
}

.summary-trigger-bar {
  display: flex;
  align-items: center;
}

.smart-summary-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.7rem 1.25rem;
  background: linear-gradient(135deg, #1e2a4a 0%, #2563eb 100%);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 0.75rem;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.22);
  transition: all 0.25s ease;
}

.smart-summary-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.32);
  background: linear-gradient(135deg, #17223d 0%, #1d4ed8 100%);
}

.smart-summary-btn.is-active {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  box-shadow: 0 3px 10px rgba(30, 41, 59, 0.18);
}

.smart-summary-btn:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

.btn-icon-wrap {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.icon-svg {
  width: 1.15rem;
  height: 1.15rem;
}

.ai-pill-badge {
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 0.1rem 0.45rem;
  border-radius: 9999px;
  border: 1px solid rgba(255, 255, 255, 0.25);
}

/* بطاقة الملخص */
.summary-card {
  margin-top: 1rem;
  background: #ffffff;
  border-radius: 1rem;
  border: 1px solid #dbeafe;
  box-shadow: 0 10px 28px -6px rgba(30, 42, 74, 0.08), 0 4px 12px -2px rgba(37, 99, 235, 0.05);
  overflow: hidden;
}

.summary-card-header {
  background: linear-gradient(135deg, #1e2a4a 0%, #1d4ed8 100%);
  color: #ffffff;
  padding: 0.95rem 1.35rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.header-icon-box {
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 0.6rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.header-title {
  font-size: 1rem;
  font-weight: 700;
  margin: 0;
  color: #ffffff;
  line-height: 1.3;
}

.header-subtitle {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
  margin: 0.15rem 0 0 0;
}

.action-copy-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.75rem;
  background: rgba(255, 255, 255, 0.14);
  hover: rgba(255, 255, 255, 0.24);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 0.5rem;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
}

.action-copy-btn:hover {
  background: rgba(255, 255, 255, 0.25);
}

/* جسم البطاقة والفقرات */
.summary-card-body {
  padding: 1.25rem 1.35rem;
  background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}

.paragraphs-list {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.summary-paragraph-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  background: #ffffff;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  border-right: 4px solid #2563eb;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.summary-paragraph-item:nth-child(2) {
  border-right-color: #0284c7;
}

.paragraph-bullet {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 9999px;
  margin-top: 0.65rem;
  flex-shrink: 0;
}

.bullet-primary {
  background-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.bullet-secondary {
  background-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.paragraph-text {
  margin: 0;
  font-size: 1.05rem;
  line-height: 1.85;
  color: #1e293b;
  font-weight: 500;
  text-align: justify;
}

.typing-cursor {
  display: inline-block;
  color: #2563eb;
  font-weight: 700;
  animation: blink 0.9s infinite;
}

/* تذييل البطاقة */
.summary-card-footer {
  margin-top: 1rem;
  padding-top: 0.85rem;
  border-top: 1px dashed #e2e8f0;
}

.footer-note {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  font-size: 0.78rem;
  line-height: 1.5;
}

.footer-info-icon {
  width: 1rem;
  height: 1rem;
  color: #2563eb;
  flex-shrink: 0;
}

/* صندوق الخطأ */
.summary-error-box {
  margin-top: 0.75rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
}

.error-inner {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.error-svg {
  width: 1.25rem;
  height: 1.25rem;
  color: #dc2626;
  flex-shrink: 0;
}

.error-msg {
  flex: 1;
  margin: 0;
  color: #991b1b;
  font-size: 0.875rem;
  font-weight: 500;
}

.error-dismiss {
  background: none;
  border: none;
  color: #991b1b;
  cursor: pointer;
  font-size: 0.9rem;
  padding: 0.2rem 0.4rem;
}

/* الحركات */
.summary-fade-enter-active,
.summary-fade-leave-active {
  transition: all 0.25s ease;
}

.summary-fade-enter-from,
.summary-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

@media (max-width: 640px) {
  .smart-summary-btn {
    width: 100%;
    justify-content: center;
  }

  .summary-card-header {
    padding: 0.85rem 1rem;
  }

  .summary-card-body {
    padding: 1rem;
  }

  .summary-paragraph-item {
    padding: 0.75rem 0.85rem;
  }

  .paragraph-text {
    font-size: 0.98rem;
    line-height: 1.75;
  }
}
</style>
