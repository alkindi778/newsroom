<template>
  <div v-if="showBanner" class="cookie-consent-banner">
    <div class="cookie-banner-overlay" @click="!showSettings && closeBanner()"></div>
    
    <div class="cookie-banner-container">
      <!-- Simple Banner View -->
      <div v-if="!showSettings" class="cookie-banner-simple">
        <div class="cookie-banner-content">
          <div class="cookie-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/>
              <path d="M8.5 8.5v.01"/>
              <path d="M16 15.5v.01"/>
              <path d="M12 12v.01"/>
              <path d="M11 17v.01"/>
              <path d="M7 14v.01"/>
            </svg>
          </div>
          
          <div class="cookie-text">
            <h3>نحن نستخدم ملفات تعريف الارتباط</h3>
            <p>
              نستخدم ملفات تعريف الارتباط (الكوكيز) لتحسين تجربتك وتحليل استخدام الموقع. 
              بالنقر على "قبول الكل"، فإنك توافق على استخدامنا لملفات تعريف الارتباط.
              <button @click="showSettings = true" class="learn-more-btn">معرفة المزيد</button>
            </p>
          </div>
        </div>

        <div class="cookie-banner-actions">
          <button @click="handleAcceptAll" class="btn-accept">
            قبول الكل
          </button>
          <button @click="handleAcceptNecessary" class="btn-necessary">
            الضرورية فقط
          </button>
          <button @click="showSettings = true" class="btn-settings">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"/>
            </svg>
            إعدادات الكوكيز
          </button>
        </div>
      </div>

      <!-- Detailed Settings View -->
      <div v-else class="cookie-banner-detailed">
        <div class="cookie-settings-header">
          <h3>إعدادات ملفات تعريف الارتباط</h3>
          <button @click="showSettings = false" class="close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <div class="cookie-settings-content">
          <p class="settings-description">
            يمكنك اختيار أنواع ملفات تعريف الارتباط التي تريد السماح بها. 
            ملفات تعريف الارتباط الضرورية مطلوبة للوظائف الأساسية ولا يمكن تعطيلها.
          </p>

          <div class="cookie-categories">
            <!-- Necessary Cookies -->
            <div class="cookie-category">
              <div class="category-header">
                <div class="category-info">
                  <input 
                    type="checkbox" 
                    id="necessary" 
                    checked 
                    disabled 
                    class="cookie-checkbox"
                  />
                  <label for="necessary">
                    <strong>ملفات تعريف ضرورية</strong>
                  </label>
                </div>
                <span class="required-badge">مطلوبة</span>
              </div>
              <p class="category-description">
                هذه الملفات ضرورية لعمل الموقع ولا يمكن تعطيلها. عادة ما يتم تعيينها فقط استجابة لإجراءات تتخذها.
              </p>
            </div>

            <!-- Analytics Cookies -->
            <div class="cookie-category">
              <div class="category-header">
                <div class="category-info">
                  <input 
                    type="checkbox" 
                    id="analytics" 
                    v-model="preferences.analytics"
                    class="cookie-checkbox"
                  />
                  <label for="analytics">
                    <strong>ملفات تحليلية</strong>
                  </label>
                </div>
              </div>
              <p class="category-description">
                تساعدنا هذه الملفات على فهم كيفية استخدام الزوار لموقعنا من خلال جمع وتقديم معلومات إحصائية مجهولة.
              </p>
            </div>

            <!-- Marketing Cookies -->
            <div class="cookie-category">
              <div class="category-header">
                <div class="category-info">
                  <input 
                    type="checkbox" 
                    id="marketing" 
                    v-model="preferences.marketing"
                    class="cookie-checkbox"
                  />
                  <label for="marketing">
                    <strong>ملفات تسويقية</strong>
                  </label>
                </div>
              </div>
              <p class="category-description">
                تُستخدم لتتبع الزوار عبر المواقع الإلكترونية بهدف عرض إعلانات ذات صلة وجذابة للمستخدم الفردي.
              </p>
            </div>

            <!-- Preferences Cookies -->
            <div class="cookie-category">
              <div class="category-header">
                <div class="category-info">
                  <input 
                    type="checkbox" 
                    id="preferences" 
                    v-model="preferences.preferences"
                    class="cookie-checkbox"
                  />
                  <label for="preferences">
                    <strong>ملفات التفضيلات</strong>
                  </label>
                </div>
              </div>
              <p class="category-description">
                تسمح هذه الملفات للموقع بتذكر الخيارات التي تقوم بها لتوفير وظائف محسّنة وأكثر تخصيصًا.
              </p>
            </div>
          </div>
        </div>

        <div class="cookie-settings-actions">
          <button @click="handleSavePreferences" class="btn-save">
            حفظ التفضيلات
          </button>
          <button @click="handleAcceptAll" class="btn-accept-all">
            قبول الكل
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useCookieConsentState } from '~/composables/useCookieConsent'

const { 
  showBanner: showBannerState, 
  acceptAll, 
  acceptNecessary, 
  acceptCustom,
  loadConsent 
} = useCookieConsentState()

const showBanner = ref(false)
const showSettings = ref(false)
const preferences = ref({
  necessary: true,
  analytics: false,
  marketing: false,
  preferences: false,
})

onMounted(() => {
  loadConsent()
  showBanner.value = showBannerState.value
})

const handleAcceptAll = () => {
  acceptAll()
  showBanner.value = false
  showSettings.value = false
}

const handleAcceptNecessary = () => {
  acceptNecessary()
  showBanner.value = false
  showSettings.value = false
}

const handleSavePreferences = () => {
  acceptCustom(preferences.value)
  showBanner.value = false
  showSettings.value = false
}

const closeBanner = () => {
  // User closed without choosing, default to necessary only
  handleAcceptNecessary()
}
</script>

<style scoped>
.cookie-consent-banner {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  pointer-events: none;
  font-family: 'Azer', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.cookie-banner-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 32, 39, 0.7);
  backdrop-filter: blur(4px);
  pointer-events: all;
  animation: fadeIn 0.3s ease-in-out;
}

.cookie-banner-container {
  position: relative;
  width: 100%;
  max-width: 900px;
  margin: 0 1rem 1rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(30, 42, 74, 0.3);
  pointer-events: all;
  animation: slideUp 0.3s ease-out;
  border-top: 4px solid #D4AF37;
}

@media (max-width: 768px) {
  .cookie-banner-container {
    margin: 0;
    border-radius: 16px 16px 0 0;
    max-height: 90vh;
    overflow-y: auto;
  }
}

/* Simple Banner */
.cookie-banner-simple {
  padding: 1.5rem;
}

.cookie-banner-content {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.cookie-icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #D4AF37 0%, #C9A961 100%);
  border-radius: 12px;
  color: white;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

.cookie-text h3 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e2a4a;
}

.cookie-text p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.6;
  color: #666;
}

.learn-more-btn {
  background: none;
  border: none;
  color: #D4AF37;
  text-decoration: underline;
  cursor: pointer;
  padding: 0;
  margin-right: 0.25rem;
  font-size: inherit;
  font-weight: 600;
}

.learn-more-btn:hover {
  color: #C9A961;
}

.cookie-banner-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.cookie-banner-actions button {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  font-size: 0.95rem;
}

.btn-accept {
  background: linear-gradient(135deg, #D4AF37 0%, #B6962D 100%);
  color: white;
  flex: 1;
  min-width: 120px;
  font-weight: 600;
}

.btn-accept:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
  background: linear-gradient(135deg, #E1CF93 0%, #D4AF37 100%);
}

.btn-necessary {
  background: #1e2a4a;
  color: white;
  flex: 1;
  min-width: 120px;
  font-weight: 600;
}

.btn-necessary:hover {
  background: #2c3e68;
  transform: translateY(-1px);
}

.btn-settings {
  background: white;
  color: #1e2a4a;
  border: 2px solid #D4AF37;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  font-weight: 600;
}

.btn-settings:hover {
  background: #FAF7ED;
  border-color: #C9A961;
  transform: translateY(-1px);
}

/* Detailed Settings */
.cookie-banner-detailed {
  padding: 0;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
}

.cookie-settings-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.cookie-settings-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e2a4a;
}

.close-btn {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #FAF7ED;
  color: #D4AF37;
}

.cookie-settings-content {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.settings-description {
  margin: 0 0 1.5rem;
  color: #666;
  font-size: 0.95rem;
  line-height: 1.6;
}

.cookie-categories {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cookie-category {
  padding: 1.25rem;
  background: #FAF7ED;
  border-radius: 12px;
  border: 1px solid #EBDFB7;
  transition: all 0.2s;
}

.cookie-category:hover {
  border-color: #D4AF37;
  box-shadow: 0 2px 8px rgba(212, 175, 55, 0.1);
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.category-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.cookie-checkbox {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: #D4AF37;
}

.cookie-checkbox:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.category-info label {
  cursor: pointer;
  font-size: 1rem;
  color: #1e2a4a;
  font-weight: 600;
}

.required-badge {
  background: linear-gradient(135deg, #D4AF37 0%, #C9A961 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(212, 175, 55, 0.3);
}

.category-description {
  margin: 0;
  font-size: 0.875rem;
  line-height: 1.6;
  color: #666;
  padding-right: 2rem;
}

.cookie-settings-actions {
  display: flex;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.cookie-settings-actions button {
  flex: 1;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  font-size: 0.95rem;
}

.btn-save {
  background: #1e2a4a;
  color: white;
  font-weight: 600;
}

.btn-save:hover {
  background: #2c3e68;
  transform: translateY(-1px);
}

.btn-accept-all {
  background: linear-gradient(135deg, #D4AF37 0%, #B6962D 100%);
  color: white;
  font-weight: 600;
}

.btn-accept-all:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
  background: linear-gradient(135deg, #E1CF93 0%, #D4AF37 100%);
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
