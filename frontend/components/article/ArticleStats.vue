<template>
  <div class="article-stats">
    <div class="stats-container">
      <div class="stats-grid">
        <!-- عدد المشاهدات -->
        <div class="stat-item views">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ formatNumber(article.views || 0) }}</div>
            <div class="stat-label">مشاهدة</div>
          </div>
        </div>

        <!-- عدد الكلمات -->
        <div class="stat-item words">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ formatNumber(wordCount) }}</div>
            <div class="stat-label">كلمة</div>
          </div>
        </div>

        <!-- وقت القراءة -->
        <div class="stat-item reading-time">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ readingTime }}</div>
            <div class="stat-label">دقيقة قراءة</div>
          </div>
        </div>

        <!-- تاريخ النشر -->
        <div class="stat-item publish-date">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ publishDate }}</div>
            <div class="stat-label">تاريخ النشر</div>
          </div>
        </div>

        <!-- المؤلف -->
        <div v-if="article.author" class="stat-item author">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ article.author.name }}</div>
            <div class="stat-label">الكاتب</div>
          </div>
        </div>

        <!-- القسم -->
        <div v-if="article.category" class="stat-item category">
          <div class="stat-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ article.category.name }}</div>
            <div class="stat-label">القسم</div>
          </div>
        </div>
      </div>

      <!-- إحصائيات إضافية -->
      <div v-if="showExtended" class="extended-stats">
        <div class="extended-grid">
          <!-- معدل القراءة -->
          <div class="extended-stat">
            <h4 class="extended-title">معدل القراءة</h4>
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: `${readingProgress}%` }"></div>
            </div>
            <p class="extended-desc">{{ readingProgress }}% من القراء يكملون المقال</p>
          </div>

          <!-- أوقات الذروة -->
          <div class="extended-stat">
            <h4 class="extended-title">أفضل وقت للقراءة</h4>
            <div class="time-badges">
              <span class="time-badge active">المساء</span>
              <span class="time-badge">الصباح</span>
              <span class="time-badge">الليل</span>
            </div>
            <p class="extended-desc">معظم القراء يفضلون المساء</p>
          </div>

          <!-- نسبة المشاركة -->
          <div class="extended-stat">
            <h4 class="extended-title">معدل المشاركة</h4>
            <div class="share-chart">
              <div class="share-item">
                <div class="share-platform">فيسبوك</div>
                <div class="share-bar">
                  <div class="share-fill facebook" style="width: 65%"></div>
                </div>
                <div class="share-percent">65%</div>
              </div>
              <div class="share-item">
                <div class="share-platform">تويتر</div>
                <div class="share-bar">
                  <div class="share-fill twitter" style="width: 25%"></div>
                </div>
                <div class="share-percent">25%</div>
              </div>
              <div class="share-item">
                <div class="share-platform">واتساب</div>
                <div class="share-bar">
                  <div class="share-fill whatsapp" style="width: 10%"></div>
                </div>
                <div class="share-percent">10%</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- زر عرض المزيد -->
      <div class="toggle-extended">
        <button @click="showExtended = !showExtended" class="toggle-btn">
          <span>{{ showExtended ? 'إخفاء التفاصيل' : 'عرض إحصائيات مفصلة' }}</span>
          <svg 
            class="w-4 h-4 toggle-icon" 
            :class="{ 'rotate-180': showExtended }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
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
const showExtended = ref(false)

// Computed
const wordCount = computed(() => {
  if (!props.article.content) return 0
  return props.article.content.replace(/<[^>]*>/g, '').split(/\s+/).length
})

const readingTime = computed(() => {
  const wordsPerMinute = 200
  return Math.ceil(wordCount.value / wordsPerMinute)
})

const publishDate = computed(() => {
  if (!props.article.published_at) return 'غير محدد'
  const date = new Date(props.article.published_at)
  return date.toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

const readingProgress = computed(() => {
  // محاكاة نسبة إكمال القراءة بناءً على طول المقال
  const baseRate = 85
  const lengthFactor = Math.min(wordCount.value / 1000, 1) * 10
  return Math.round(baseRate - lengthFactor)
})

// Methods
const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}
</script>

<style scoped>
.article-stats {
  margin: 24px 0;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.stats-container {
  padding: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.stat-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-item.views { border-left: 4px solid #3b82f6; }
.stat-item.words { border-left: 4px solid #10b981; }
.stat-item.reading-time { border-left: 4px solid #f59e0b; }
.stat-item.publish-date { border-left: 4px solid #8b5cf6; }
.stat-item.author { border-left: 4px solid #ef4444; }
.stat-item.category { border-left: 4px solid #06b6d4; }

.stat-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.views .stat-icon { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.words .stat-icon { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
.reading-time .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.publish-date .stat-icon { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.author .stat-icon { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.category .stat-icon { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

.stat-info {
  flex: 1;
}

.stat-number {
  font-size: 18px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 2px;
}

.stat-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.extended-stats {
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.extended-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.extended-stat {
  background: white;
  padding: 20px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.extended-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 12px;
}

.extended-desc {
  font-size: 14px;
  color: #64748b;
  margin-top: 8px;
  margin-bottom: 0;
}

.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.time-badges {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.time-badge {
  padding: 6px 12px;
  background: #f1f5f9;
  color: #64748b;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid #e2e8f0;
}

.time-badge.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}

.share-chart {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.share-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.share-platform {
  width: 60px;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
}

.share-bar {
  flex: 1;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.share-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.share-fill.facebook { background: #1877f2; }
.share-fill.twitter { background: #1da1f2; }
.share-fill.whatsapp { background: #25d366; }

.share-percent {
  width: 35px;
  text-align: right;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
}

.toggle-extended {
  text-align: center;
  padding-top: 16px;
}

.toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: transparent;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  color: #64748b;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.toggle-btn:hover {
  background: white;
  border-color: #cbd5e0;
  color: #4a5568;
}

.toggle-icon {
  transition: transform 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
  }

  .stat-item {
    padding: 12px;
  }

  .stat-icon {
    width: 32px;
    height: 32px;
  }

  .stat-number {
    font-size: 16px;
  }

  .extended-grid {
    grid-template-columns: 1fr;
  }

  .share-item {
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }

  .share-platform {
    width: auto;
  }
}
</style>
