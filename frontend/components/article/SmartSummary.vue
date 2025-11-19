<template>
  <div class="smart-summary-container mb-8">
    <!-- عنوان القسم -->
    <div class="flex items-center gap-3 mb-6">
      <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
      </div>
      <div>
        <h3 class="text-xl font-bold text-gray-900">الملخص الذكي</h3>
        <p class="text-sm text-gray-600">ملخص تلقائي بالذكاء الاصطناعي</p>
      </div>
    </div>

    <!-- حالة التحميل -->
    <div v-if="loading" class="smart-summary-card loading">
      <div class="loading-animation">
        <div class="loading-dots">
          <div class="dot dot-1"></div>
          <div class="dot dot-2"></div>
          <div class="dot dot-3"></div>
        </div>
        <p class="loading-text">جاري إنشاء الملخص الذكي...</p>
      </div>
    </div>

    <!-- المحتوى -->
    <div v-else-if="summary" class="smart-summary-card">
      <!-- أيقونة الذكاء الاصطناعي -->
      <div class="ai-badge">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <span>AI</span>
      </div>

      <!-- النقاط الرئيسية -->
      <div class="summary-points">
        <h4 class="points-title">النقاط الرئيسية:</h4>
        <ul class="points-list">
          <li v-for="(point, index) in summaryPoints" :key="index" class="point-item">
            <div class="point-marker">{{ index + 1 }}</div>
            <span class="point-text">{{ point }}</span>
          </li>
        </ul>
      </div>

      <!-- الملخص النصي -->
      <div class="summary-text">
        <h4 class="text-title">الملخص المختصر:</h4>
        <p class="summary-paragraph">{{ summary.text }}</p>
      </div>

      <!-- معلومات إضافية -->
      <div class="summary-meta">
        <div class="meta-item">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>وقت القراءة المقدر: {{ estimatedReadTime }} دقائق</span>
        </div>
        <div class="meta-item">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>عدد الكلمات: {{ wordCount }} كلمة</span>
        </div>
      </div>

      <!-- أزرار التفاعل -->
      <div class="summary-actions">
        <button @click="copyToClipboard" :class="['action-btn', { copied: copied }]">
          <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>{{ copied ? 'تم النسخ!' : 'نسخ الملخص' }}</span>
        </button>

        <button @click="shareSummary" class="action-btn share-btn">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
          </svg>
          <span>مشاركة الملخص</span>
        </button>

        <button @click="toggleExpanded" class="action-btn expand-btn">
          <svg class="w-4 h-4" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
          <span>{{ expanded ? 'إخفاء التفاصيل' : 'عرض التفاصيل' }}</span>
        </button>
      </div>

      <!-- التفاصيل الموسعة -->
      <div v-if="expanded" class="summary-details">
        <div class="details-grid">
          <div class="detail-card">
            <h5 class="detail-title">الموضوع الرئيسي</h5>
            <p class="detail-text">{{ summary.mainTopic || 'غير محدد' }}</p>
          </div>
          <div class="detail-card">
            <h5 class="detail-title">المشاعر العامة</h5>
            <div class="sentiment-indicator" :class="summary.sentiment">
              <div class="sentiment-icon"></div>
              <span>{{ getSentimentText(summary.sentiment) }}</span>
            </div>
          </div>
          <div class="detail-card">
            <h5 class="detail-title">الكلمات المفتاحية</h5>
            <div class="keywords-container">
              <span v-for="keyword in summary.keywords" :key="keyword" class="keyword-tag">
                {{ keyword }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- حالة الخطأ -->
    <div v-else-if="error" class="smart-summary-card error">
      <div class="error-content">
        <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h4 class="error-title">عذراً، حدث خطأ</h4>
        <p class="error-message">{{ error }}</p>
        <button @click="retrySummary" class="retry-btn">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          إعادة المحاولة
        </button>
      </div>
    </div>

    <!-- الحالة الافتراضية -->
    <div v-else class="smart-summary-card placeholder">
      <div class="placeholder-content">
        <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        <h4 class="placeholder-title">ملخص ذكي متاح</h4>
        <p class="placeholder-message">اضغط لإنشاء ملخص ذكي لهذا المقال</p>
        <button @click="generateSummary" class="generate-btn">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          إنشاء الملخص الآن
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  article: {
    type: Object,
    required: true
  }
})

// State
const loading = ref(false)
const summary = ref(null)
const error = ref(null)
const copied = ref(false)
const expanded = ref(false)

// Computed
const summaryPoints = computed(() => {
  if (!summary.value?.points) return []
  return summary.value.points
})

const estimatedReadTime = computed(() => {
  if (!props.article.content) return 0
  const wordsPerMinute = 200
  const words = props.article.content.replace(/<[^>]*>/g, '').split(/\s+/).length
  return Math.ceil(words / wordsPerMinute)
})

const wordCount = computed(() => {
  if (!props.article.content) return 0
  return props.article.content.replace(/<[^>]*>/g, '').split(/\s+/).length
})

// Methods
const generateSummary = async () => {
  loading.value = true
  error.value = null

  try {
    const { $fetch } = useNuxtApp()
    const response = await $fetch(`/api/v1/articles/${props.article.id}/smart-summary`, {
      method: 'POST'
    })

    if (response.status === 'success') {
      summary.value = response.data
    } else {
      throw new Error(response.message || 'فشل في إنشاء الملخص')
    }
  } catch (err) {
    error.value = err.message || 'حدث خطأ في إنشاء الملخص الذكي'
    console.error('Smart Summary Error:', err)
  } finally {
    loading.value = false
  }
}

const copyToClipboard = async () => {
  if (!summary.value) return

  const textToCopy = `
الملخص الذكي: ${props.article.title}

النقاط الرئيسية:
${summaryPoints.value.map((point, index) => `${index + 1}. ${point}`).join('\n')}

الملخص المختصر:
${summary.value.text}

المصدر: ${window.location.href}
  `.trim()

  try {
    await navigator.clipboard.writeText(textToCopy)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Copy failed:', err)
  }
}

const shareSummary = () => {
  const shareText = `الملخص الذكي: ${props.article.title}\n\n${summary.value.text}\n\nالمصدر: ${window.location.href}`
  
  if (navigator.share) {
    navigator.share({
      title: `ملخص ذكي: ${props.article.title}`,
      text: shareText,
      url: window.location.href
    })
  } else {
    // Fallback to WhatsApp
    const whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText)}`
    window.open(whatsappUrl, '_blank')
  }
}

const toggleExpanded = () => {
  expanded.value = !expanded.value
}

const retrySummary = () => {
  error.value = null
  generateSummary()
}

const getSentimentText = (sentiment) => {
  const sentiments = {
    positive: 'إيجابي',
    negative: 'سلبي',
    neutral: 'محايد'
  }
  return sentiments[sentiment] || 'غير محدد'
}

// Auto-generate summary when component mounts
onMounted(() => {
  generateSummary()
})
</script>

<style scoped>
.smart-summary-container {
  max-width: 100%;
}

.smart-summary-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  padding: 24px;
  color: white;
  position: relative;
  overflow: hidden;
}

.smart-summary-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 20px;
}

.smart-summary-card > * {
  position: relative;
  z-index: 1;
}

.loading {
  background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
}

.error {
  background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
}

.placeholder {
  background: linear-gradient(135deg, #81ecec 0%, #00cec9 100%);
}

.loading-animation {
  text-align: center;
  padding: 40px 20px;
}

.loading-dots {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 16px;
}

.dot {
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  animation: bounce 1.4s ease-in-out infinite both;
}

.dot-1 { animation-delay: -0.32s; }
.dot-2 { animation-delay: -0.16s; }
.dot-3 { animation-delay: 0s; }

@keyframes bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

.loading-text {
  font-size: 16px;
  font-weight: 600;
  opacity: 0.9;
}

.ai-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.2);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 20px;
}

.summary-points {
  margin-bottom: 24px;
}

.points-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 16px;
  color: rgba(255, 255, 255, 0.95);
}

.points-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.point-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
  animation: slideInUp 0.3s ease-out;
  animation-fill-mode: both;
}

.point-item:nth-child(1) { animation-delay: 0.1s; }
.point-item:nth-child(2) { animation-delay: 0.2s; }
.point-item:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.point-marker {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  font-weight: 700;
  font-size: 12px;
  flex-shrink: 0;
}

.point-text {
  line-height: 1.6;
  font-weight: 500;
}

.summary-text {
  margin-bottom: 24px;
}

.text-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 12px;
  color: rgba(255, 255, 255, 0.95);
}

.summary-paragraph {
  line-height: 1.8;
  font-size: 16px;
  background: rgba(255, 255, 255, 0.1);
  padding: 16px;
  border-radius: 12px;
  border-right: 4px solid rgba(255, 255, 255, 0.3);
}

.summary-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  opacity: 0.9;
}

.summary-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 25px;
  color: white;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.action-btn.copied {
  background: rgba(46, 160, 67, 0.8);
  border-color: rgba(46, 160, 67, 1);
}

.expand-btn svg {
  transition: transform 0.3s ease;
}

.summary-details {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.detail-card {
  background: rgba(255, 255, 255, 0.1);
  padding: 16px;
  border-radius: 12px;
  backdrop-filter: blur(10px);
}

.detail-title {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 8px;
  color: rgba(255, 255, 255, 0.9);
}

.detail-text {
  font-size: 14px;
  line-height: 1.5;
}

.sentiment-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sentiment-icon {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.sentiment-indicator.positive .sentiment-icon {
  background: #2ecc71;
}

.sentiment-indicator.negative .sentiment-icon {
  background: #e74c3c;
}

.sentiment-indicator.neutral .sentiment-icon {
  background: #95a5a6;
}

.keywords-container {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.keyword-tag {
  background: rgba(255, 255, 255, 0.2);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.error-content, .placeholder-content {
  text-align: center;
  padding: 40px 20px;
}

.error-icon, .placeholder-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 16px;
  opacity: 0.8;
}

.error-title, .placeholder-title {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 8px;
}

.error-message, .placeholder-message {
  font-size: 16px;
  opacity: 0.9;
  margin-bottom: 20px;
}

.retry-btn, .generate-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 25px;
  color: white;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover, .generate-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .smart-summary-card {
    padding: 20px;
    border-radius: 16px;
  }

  .points-title, .text-title {
    font-size: 16px;
  }

  .summary-paragraph {
    font-size: 15px;
  }

  .summary-actions {
    flex-direction: column;
  }

  .action-btn {
    justify-content: center;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }
}
</style>
