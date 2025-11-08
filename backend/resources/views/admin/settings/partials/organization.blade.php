<div class="space-y-6">
    <!-- Section Title -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">معلومات المنظمة</h3>
        <p class="text-sm text-gray-600">معلومات تُستخدم في Schema.org وخرائط Google ومحركات البحث</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Founding Date -->
        <div>
            <label for="org_founding_date" class="block text-sm font-medium text-gray-700 mb-2">
                سنة التأسيس
            </label>
            <input type="text" 
                   name="settings[org_founding_date]" 
                   id="org_founding_date" 
                   value="{{ $settings['organization']['org_founding_date'] ?? '2024' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="2024">
            <p class="mt-1 text-xs text-gray-500">السنة التي تأسس فيها الموقع/المنظمة</p>
        </div>

        <!-- Area Served -->
        <div>
            <label for="org_area_served" class="block text-sm font-medium text-gray-700 mb-2">
                منطقة الخدمة
            </label>
            <input type="text" 
                   name="settings[org_area_served]" 
                   id="org_area_served" 
                   value="{{ $settings['organization']['org_area_served'] ?? 'اليمن' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="اليمن">
            <p class="mt-1 text-xs text-gray-500">المنطقة الجغرافية التي يخدمها الموقع</p>
        </div>

        <!-- Address Country Code -->
        <div>
            <label for="org_address_country" class="block text-sm font-medium text-gray-700 mb-2">
                رمز الدولة (ISO)
            </label>
            <select name="settings[org_address_country]" 
                    id="org_address_country"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <option value="YE" {{ ($settings['organization']['org_address_country'] ?? 'YE') == 'YE' ? 'selected' : '' }}>🇾🇪 اليمن (YE)</option>
                <option value="SA" {{ ($settings['organization']['org_address_country'] ?? '') == 'SA' ? 'selected' : '' }}>🇸🇦 السعودية (SA)</option>
                <option value="AE" {{ ($settings['organization']['org_address_country'] ?? '') == 'AE' ? 'selected' : '' }}>🇦🇪 الإمارات (AE)</option>
                <option value="EG" {{ ($settings['organization']['org_address_country'] ?? '') == 'EG' ? 'selected' : '' }}>🇪🇬 مصر (EG)</option>
                <option value="JO" {{ ($settings['organization']['org_address_country'] ?? '') == 'JO' ? 'selected' : '' }}>🇯🇴 الأردن (JO)</option>
                <option value="LB" {{ ($settings['organization']['org_address_country'] ?? '') == 'LB' ? 'selected' : '' }}>🇱🇧 لبنان (LB)</option>
                <option value="SY" {{ ($settings['organization']['org_address_country'] ?? '') == 'SY' ? 'selected' : '' }}>🇸🇾 سوريا (SY)</option>
                <option value="IQ" {{ ($settings['organization']['org_address_country'] ?? '') == 'IQ' ? 'selected' : '' }}>🇮🇶 العراق (IQ)</option>
                <option value="KW" {{ ($settings['organization']['org_address_country'] ?? '') == 'KW' ? 'selected' : '' }}>🇰🇼 الكويت (KW)</option>
                <option value="QA" {{ ($settings['organization']['org_address_country'] ?? '') == 'QA' ? 'selected' : '' }}>🇶🇦 قطر (QA)</option>
                <option value="BH" {{ ($settings['organization']['org_address_country'] ?? '') == 'BH' ? 'selected' : '' }}>🇧🇭 البحرين (BH)</option>
                <option value="OM" {{ ($settings['organization']['org_address_country'] ?? '') == 'OM' ? 'selected' : '' }}>🇴🇲 عُمان (OM)</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">رمز الدولة الدولي ISO 3166-1 alpha-2</p>
        </div>

        <!-- Address City -->
        <div>
            <label for="org_address_city" class="block text-sm font-medium text-gray-700 mb-2">
                المدينة
            </label>
            <input type="text" 
                   name="settings[org_address_city]" 
                   id="org_address_city" 
                   value="{{ $settings['organization']['org_address_city'] ?? 'عدن' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="عدن">
            <p class="mt-1 text-xs text-gray-500">المدينة التي يقع فيها المقر الرئيسي</p>
        </div>
    </div>

    <!-- Schema.org Info Box -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <h3 class="text-sm font-medium text-green-800">Schema.org Structured Data</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>هذه المعلومات تُستخدم لإنشاء Schema.org markup تلقائياً في الفرونت اند، مما يساعد محركات البحث على فهم موقعك بشكل أفضل وعرضه في نتائج البحث بشكل منسّق (Rich Results).</p>
                </div>
            </div>
        </div>
    </div>
</div>
