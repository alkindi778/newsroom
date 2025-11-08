<div class="space-y-6">
    <!-- Section Title -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">محسنات ظهور الموقع (SEO)</h3>
        <p class="text-sm text-gray-600">إعدادات محركات البحث والتحليلات - مطابقة لأفضل ممارسات Nuxt.js</p>
    </div>

    <!-- Basic SEO Settings -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Site Locale -->
        <div>
            <label for="site_locale" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                    لغة الموقع (Locale)
                </span>
            </label>
            <input type="text" 
                   name="settings[site_locale]" 
                   id="site_locale" 
                   value="{{ $settings['seo']['site_locale'] ?? '' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="ar_SA">
            <p class="mt-1 text-xs text-gray-500">كود اللغة والبلد (مثال: ar_SA, en_US)</p>
        </div>

        <!-- Theme Color -->
        <div>
            <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    لون السمة (Theme Color)
                </span>
            </label>
            <div class="flex gap-3">
                <input type="color" 
                       name="settings[theme_color]" 
                       id="theme_color" 
                       value="{{ $settings['seo']['theme_color'] ?? '#D4AF37' }}"
                       class="h-11 px-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <input type="text" 
                       value="{{ $settings['seo']['theme_color'] ?? '#D4AF37' }}"
                       readonly
                       class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 font-mono text-sm">
            </div>
            <p class="mt-1 text-xs text-gray-500">لون شريط العنوان في المتصفحات والأجهزة المحمولة</p>
        </div>
    </div>

    <!-- Twitter Handle -->
    <div>
        <label for="twitter_handle" class="block text-sm font-medium text-gray-700 mb-2">
            <span class="flex items-center">
                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                حساب تويتر (X)
            </span>
        </label>
        <input type="text" 
               name="settings[twitter_handle]" 
               id="twitter_handle" 
               value="{{ $settings['seo']['twitter_handle'] ?? '' }}"
               class="w-full md:w-1/2 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
               placeholder="@newsroom">
        <p class="mt-1 text-xs text-gray-500">معرّف تويتر الخاص بالموقع (يبدأ بـ @)</p>
    </div>

    <!-- SEO Title Separator -->
    <div>
        <label for="seo_title_separator" class="block text-sm font-medium text-gray-700 mb-2">
            فاصل العنوان
        </label>
        <input type="text" 
               name="settings[seo_title_separator]" 
               id="seo_title_separator" 
               value="{{ $settings['seo']['seo_title_separator'] ?? '' }}"
               class="w-full md:w-1/3 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
               placeholder=" - ">
        <p class="mt-1 text-xs text-gray-500">الحرف الذي يفصل بين عنوان الصفحة واسم الموقع (مثال: عنوان - اسم الموقع)</p>
    </div>

    <!-- Default OG Image -->
    <div>
        <label for="default_og_image" class="block text-sm font-medium text-gray-700 mb-2">
            الصورة الافتراضية لمشاركات السوشيال ميديا (Open Graph)
        </label>
        <div class="space-y-3">
            <!-- Current Image Preview -->
            @if(isset($settings['seo']['default_og_image']) && $settings['seo']['default_og_image'])
            <div class="flex items-center justify-center w-full md:w-2/3 h-48 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $settings['seo']['default_og_image']) }}" 
                     alt="OG Image" 
                     class="max-h-full object-cover">
            </div>
            @else
            <div class="flex items-center justify-center w-full md:w-2/3 h-48 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mt-1 text-sm text-gray-500">لا توجد صورة افتراضية</p>
                </div>
            </div>
            @endif
            
            <!-- Upload Button -->
            <input type="file" 
                   name="settings[default_og_image]" 
                   id="default_og_image" 
                   accept="image/*"
                   class="block w-full md:w-2/3 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
            <p class="text-xs text-gray-500">PNG, JPG (يُفضل 1200×630 بكسل للحصول على أفضل جودة عند المشاركة)</p>
        </div>
    </div>

    <!-- Analytics & Verification -->
    <div class="border-t border-gray-200 pt-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">التحليلات والتحقق</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Google Analytics -->
            <div>
                <label for="seo_google_analytics" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        Google Analytics ID
                    </span>
                </label>
                <input type="text" 
                       name="settings[seo_google_analytics]" 
                       id="seo_google_analytics" 
                       value="{{ $settings['seo']['seo_google_analytics'] ?? '' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono text-sm"
                       placeholder="G-XXXXXXXXXX أو UA-XXXXXXXXX-X">
                <p class="mt-1 text-xs text-gray-500">معرّف Google Analytics لتتبع الزوار</p>
            </div>

            <!-- Google Verification -->
            <div>
                <label for="seo_google_verification" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Google Site Verification
                    </span>
                </label>
                <input type="text" 
                       name="settings[seo_google_verification]" 
                       id="seo_google_verification" 
                       value="{{ $settings['seo']['seo_google_verification'] ?? '' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono text-sm"
                       placeholder="xxxxxxxxxxxxxxxxxxxx">
                <p class="mt-1 text-xs text-gray-500">كود التحقق من ملكية الموقع في Google Search Console</p>
            </div>
        </div>
    </div>

    <!-- SEO Tips -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <h3 class="text-sm font-medium text-blue-800">نصائح لتحسين SEO</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>استخدم صورة بجودة عالية (1200×630) لمشاركات السوشيال ميديا</li>
                        <li>تأكد من إضافة Google Analytics لتتبع أداء الموقع</li>
                        <li>فعّل Google Search Console للحصول على تقارير محركات البحث</li>
                        <li>راجع دليل SEO في الفرونت اند للمزيد من الخطوات</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
