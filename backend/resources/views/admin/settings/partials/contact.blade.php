<div class="space-y-6">
    <!-- Section Title -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">معلومات الاتصال</h3>
        <p class="text-sm text-gray-600">وسائل التواصل والعنوان البريدي</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Email -->
        <div>
            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    البريد الإلكتروني
                </span>
            </label>
            <input type="email" 
                   name="settings[contact_email]" 
                   id="contact_email" 
                   value="{{ $settings['contact']['contact_email'] ?? '' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="info@example.com">
            <p class="mt-1 text-xs text-gray-500">البريد الإلكتروني الرسمي للموقع</p>
        </div>

        <!-- Phone -->
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    رقم الهاتف
                </span>
            </label>
            <input type="tel" 
                   name="settings[contact_phone]" 
                   id="contact_phone" 
                   value="{{ $settings['contact']['contact_phone'] ?? '' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="+967-xxx-xxx-xxx">
            <p class="mt-1 text-xs text-gray-500">رقم الهاتف مع المفتاح الدولي</p>
        </div>
    </div>

    <!-- Address -->
    <div>
        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">
            <span class="flex items-center">
                <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                العنوان الكامل
            </span>
        </label>
        <textarea name="settings[contact_address]" 
                  id="contact_address" 
                  rows="3"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                  placeholder="الشارع، المدينة، الدولة">{{ $settings['contact']['contact_address'] ?? '' }}</textarea>
        <p class="mt-1 text-xs text-gray-500">العنوان البريدي الكامل للمقر الرئيسي</p>
    </div>

    <!-- Info Box -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <h3 class="text-sm font-medium text-indigo-800">ملاحظة</h3>
                <div class="mt-2 text-sm text-indigo-700">
                    <p>هذه المعلومات تُستخدم في:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>صفحة "اتصل بنا" في الموقع</li>
                        <li>Schema.org ContactPoint لمحركات البحث</li>
                        <li>رسائل البريد الإلكتروني الآلية</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
