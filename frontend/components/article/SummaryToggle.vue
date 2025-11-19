<template>
  <div class="summary-toggle-container">
    <!-- Toggle Switch -->
    <div class="toggle-header">
      <div class="toggle-info">
        <h4 class="toggle-title">الملخص الذكي</h4>
        <p class="toggle-description">ملخص تلقائي بالذكاء الاصطناعي لتوفير الوقت</p>
      </div>
      
      <div class="toggle-switch-container">
        <label class="toggle-switch" :class="{ active: isEnabled }">
          <input 
            type="checkbox" 
            v-model="isEnabled"
            @change="handleToggle"
          />
          <span class="slider">
            <span class="slider-icon">
              <svg v-if="isEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
              </svg>
            </span>
          </span>
        </label>
      </div>
    </div>

    <!-- Summary Content -->
    <transition name="summary-slide">
      <div v-if="isEnabled" class="summary-content">
        <slot />
      </div>
    </transition>

    <!-- Features Info -->
    <div v-if="!isEnabled" class="features-preview">
      <div class="features-grid">
        <div class="feature-item">
          <div class="feature-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div class="feature-text">
            <h5>النقاط الرئيسية</h5>
            <p>استخراج النقاط المهمة</p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="feature-text">
            <h5>وقت القراءة</h5>
            <p>تقدير الوقت المطلوب</p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div class="feature-text">
            <h5>الكلمات المفتاحية</h5>
            <p>أهم المصطلحات</p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a2.5 2.5 0 010 5H9m4.5-5H15a2.5 2.5 0 010 5h-1.5m-5-5v5m5-5v5"/>
            </svg>
          </div>
          <div class="feature-text">
            <h5>تحليل المشاعر</h5>
            <p>طبيعة المحتوى</p>
          </div>
        </div>
      </div>

      <div class="enable-prompt">
        <button @click="isEnabled = true" class="enable-button">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          تفعيل الملخص الذكي
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['toggle'])

// State
const isEnabled = ref(false)

// Methods
const handleToggle = () => {
  emit('toggle', isEnabled.value)
  
  // Save preference to localStorage
  if (process.client) {
    localStorage.setItem('smartSummaryEnabled', isEnabled.value.toString())
  }
}

// Load preference from localStorage
onMounted(() => {
  if (process.client) {
    const saved = localStorage.getItem('smartSummaryEnabled')
    if (saved !== null) {
      isEnabled.value = saved === 'true'
      emit('toggle', isEnabled.value)
    }
  }
})
</script>

<style scoped>
.summary-toggle-container {
  margin: 24px 0;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.toggle-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.toggle-info {
  flex: 1;
}

.toggle-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.toggle-description {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.toggle-switch-container {
  margin-left: 16px;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 32px;
  cursor: pointer;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #cbd5e0;
  transition: all 0.3s ease;
  border-radius: 32px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 4px;
}

.toggle-switch.active .slider {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  justify-content: flex-end;
}

.slider-icon {
  width: 24px;
  height: 24px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4a5568;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-switch.active .slider-icon {
  color: #667eea;
}

.summary-content {
  padding: 0;
}

.features-preview {
  padding: 24px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.feature-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: #667eea;
}

.feature-icon {
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

.feature-text {
  flex: 1;
}

.feature-text h5 {
  font-size: 14px;
  font-weight: 600;
  color: #1a202c;
  margin: 0 0 4px 0;
}

.feature-text p {
  font-size: 12px;
  color: #64748b;
  margin: 0;
}

.enable-prompt {
  text-align: center;
}

.enable-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.enable-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Transitions */
.summary-slide-enter-active,
.summary-slide-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.summary-slide-enter-from {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}

.summary-slide-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}

.summary-slide-enter-to,
.summary-slide-leave-from {
  max-height: 1000px;
  opacity: 1;
  transform: translateY(0);
}

/* Responsive Design */
@media (max-width: 768px) {
  .toggle-header {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .feature-item {
    justify-content: center;
    text-align: center;
  }
}
</style>
