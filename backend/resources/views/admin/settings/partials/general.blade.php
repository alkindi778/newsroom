<div class="space-y-6">
    <!-- Section Title -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">الإعدادات العامة</h3>
        <p class="text-sm text-gray-600">إعدادات أساسية للموقع مثل الاسم والشعار والوصف</p>
    </div>

    <!-- Site Name (Arabic) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                اسم الموقع (بالعربية) <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="settings[site_name]" 
                   id="site_name" 
                   value="{{ $settings['general']['site_name'] ?? 'غرفة الأخبار' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required>
            <p class="mt-1 text-xs text-gray-500">الاسم الرئيسي للموقع بالعربية</p>
        </div>

        <!-- Site Name (English) -->
        <div>
            <label for="site_name_en" class="block text-sm font-medium text-gray-700 mb-2">
                اسم الموقع (بالإنجليزية) <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="settings[site_name_en]" 
                   id="site_name_en" 
                   value="{{ $settings['general']['site_name_en'] ?? 'Newsroom' }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required>
            <p class="mt-1 text-xs text-gray-500">الاسم البديل للموقع بالإنجليزية</p>
        </div>
    </div>

    <!-- Site Slogan -->
    <div>
        <label for="site_slogan" class="block text-sm font-medium text-gray-700 mb-2">
            شعار الموقع
        </label>
        <input type="text" 
               name="settings[site_slogan]" 
               id="site_slogan" 
               value="{{ $settings['general']['site_slogan'] ?? '' }}"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
               placeholder="نبض الشارع - أخبارك من المصدر">
        <p class="mt-1 text-xs text-gray-500">شعار تعريفي مختصر للموقع</p>
    </div>

    <!-- Site Description -->
    <div>
        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
            وصف الموقع <span class="text-red-500">*</span>
        </label>
        <textarea name="settings[site_description]" 
                  id="site_description" 
                  rows="4"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                  required>{{ $settings['general']['site_description'] ?? '' }}</textarea>
        <p class="mt-1 text-xs text-gray-500">وصف شامل للموقع يُستخدم في محركات البحث والسوشيال ميديا</p>
    </div>

    <!-- Site Keywords -->
    <div>
        <label for="site_keywords" class="block text-sm font-medium text-gray-700 mb-2">
            الكلمات المفتاحية
        </label>
        <textarea name="settings[site_keywords]" 
                  id="site_keywords" 
                  rows="3"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                  placeholder="أخبار، أخبار عربية، أخبار اليمن، مقالات رأي">{{ $settings['general']['site_keywords'] ?? '' }}</textarea>
        <p class="mt-1 text-xs text-gray-500">الكلمات المفتاحية للموقع، افصل بينها بفاصلة (،)</p>
    </div>

    <!-- Logo & Favicon -->
    <div class="border-t border-gray-200 pt-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">الصور والأيقونات</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Site Logo -->
            <div>
                <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">
                    شعار الموقع (Logo)
                </label>
                <div class="space-y-3">
                    <!-- Current/Preview Logo -->
                    <div id="logo_preview_container" class="flex items-center justify-center w-full h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg relative" style="{{ isset($settings['general']['site_logo']) && $settings['general']['site_logo'] ? '' : 'display: none;' }}">
                        <img id="logo_preview_image"
                             src="{{ isset($settings['general']['site_logo']) && $settings['general']['site_logo'] ? asset('storage/' . $settings['general']['site_logo']) : '' }}" 
                             alt="Logo" 
                             class="max-h-28 object-contain">
                        <!-- Remove Button -->
                        <button type="button" 
                                id="remove_logo_btn"
                                onclick="removeLogo()"
                                class="absolute top-2 left-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="logo_empty_state" class="flex items-center justify-center w-full h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg" style="{{ isset($settings['general']['site_logo']) && $settings['general']['site_logo'] ? 'display: none;' : '' }}">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-500">لا يوجد شعار</p>
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <input type="file" 
                           name="settings[site_logo]" 
                           id="site_logo" 
                           accept="image/*"
                           onchange="previewLogo(this)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    <p class="text-xs text-gray-500">PNG, JPG, SVG (يُفضل 250×60 بكسل)</p>
                </div>
                
                <!-- Logo Width Control -->
                <div class="mt-4">
                    <label for="site_logo_width" class="block text-sm font-medium text-gray-700 mb-2">
                        عرض الشعار (بالبكسل)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" 
                               name="settings[site_logo_width]" 
                               id="site_logo_width" 
                               min="80" 
                               max="400" 
                               step="10"
                               value="{{ $settings['general']['site_logo_width'] ?? '180' }}"
                               oninput="updateLogoWidth(this.value)"
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   id="site_logo_width_display" 
                                   value="{{ $settings['general']['site_logo_width'] ?? '180' }}"
                                   min="80" 
                                   max="400"
                                   onchange="document.getElementById('site_logo_width').value = this.value; updateLogoWidth(this.value)"
                                   class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-600">px</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">حدد عرض الشعار في الموقع (من 80px إلى 400px)</p>
                    
                    <!-- Live Preview -->
                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-medium text-gray-700 mb-2">معاينة الحجم:</p>
                        <div class="flex items-center justify-center py-4 bg-white rounded border border-gray-200">
                            <div id="logo_size_preview" style="width: {{ $settings['general']['site_logo_width'] ?? '180' }}px; height: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 20px;">
                                <span class="text-white text-xs font-bold">LOGO</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">العرض الحالي: <span id="current_width_display">{{ $settings['general']['site_logo_width'] ?? '180' }}</span>px</p>
                    </div>
                </div>
                
                <!-- Mobile Logo Width Control -->
                <div class="mt-4">
                    <label for="site_logo_width_mobile" class="block text-sm font-medium text-gray-700 mb-2">
                        عرض الشعار في الهاتف (بالبكسل)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" 
                               name="settings[site_logo_width_mobile]" 
                               id="site_logo_width_mobile" 
                               min="60" 
                               max="400" 
                               step="10"
                               value="{{ $settings['general']['site_logo_width_mobile'] ?? '120' }}"
                               oninput="updateMobileLogoWidth(this.value)"
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   id="site_logo_width_mobile_display" 
                                   value="{{ $settings['general']['site_logo_width_mobile'] ?? '120' }}"
                                   min="60" 
                                   max="400"
                                   onchange="document.getElementById('site_logo_width_mobile').value = this.value; updateMobileLogoWidth(this.value)"
                                   class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-600">px</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">حدد عرض الشعار في أجهزة الهاتف (من 60px إلى 400px)</p>
                    
                    <!-- Mobile Logo Live Preview -->
                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-medium text-gray-700 mb-2">معاينة حجم الهاتف:</p>
                        <div class="flex items-center justify-center py-4 bg-white rounded border border-gray-200">
                            <div id="mobile_logo_size_preview" style="width: {{ $settings['general']['site_logo_width_mobile'] ?? '120' }}px; height: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                                <span class="text-white text-xs font-bold">LOGO</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">العرض في الهاتف: <span id="mobile_current_width_display">{{ $settings['general']['site_logo_width_mobile'] ?? '120' }}</span>px</p>
                    </div>
                </div>
            </div>

            <!-- Site Favicon -->
            <div>
                <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-2">
                    أيقونة الموقع (Favicon)
                </label>
                <div class="space-y-3">
                    <!-- Current/Preview Favicon -->
                    <div id="favicon_preview_container" class="flex items-center justify-center w-full h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg relative" style="{{ isset($settings['general']['site_favicon']) && $settings['general']['site_favicon'] ? '' : 'display: none;' }}">
                        <img id="favicon_preview_image"
                             src="{{ isset($settings['general']['site_favicon']) && $settings['general']['site_favicon'] ? asset('storage/' . $settings['general']['site_favicon']) : '' }}" 
                             alt="Favicon" 
                             class="h-16 w-16 object-contain">
                        <!-- Remove Button -->
                        <button type="button" 
                                id="remove_favicon_btn"
                                onclick="removeFavicon()"
                                class="absolute top-2 left-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="favicon_empty_state" class="flex items-center justify-center w-full h-32 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg" style="{{ isset($settings['general']['site_favicon']) && $settings['general']['site_favicon'] ? 'display: none;' : '' }}">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-500">لا توجد أيقونة</p>
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <input type="file" 
                           name="settings[site_favicon]" 
                           id="site_favicon" 
                           accept="image/x-icon,image/png"
                           onchange="previewFavicon(this)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    <p class="text-xs text-gray-500">ICO, PNG (يُفضل 32×32 بكسل)</p>
                </div>
            </div>
            
            <!-- Footer Logo -->
            <div>
                <label for="footer_logo" class="block text-sm font-medium text-gray-700 mb-2">
                    شعار الفوتر (Footer Logo)
                </label>
                <div class="space-y-3">
                    <!-- Current/Preview Footer Logo -->
                    <div id="footer_logo_preview_container" class="flex items-center justify-center w-full h-32 bg-gray-900 border-2 border-dashed border-gray-600 rounded-lg relative" style="{{ isset($settings['general']['footer_logo']) && $settings['general']['footer_logo'] ? '' : 'display: none;' }}">
                        <img id="footer_logo_preview_image"
                             src="{{ isset($settings['general']['footer_logo']) && $settings['general']['footer_logo'] ? asset('storage/' . $settings['general']['footer_logo']) : '' }}" 
                             alt="Footer Logo" 
                             class="max-h-28 object-contain">
                        <!-- Remove Button -->
                        <button type="button" 
                                id="remove_footer_logo_btn"
                                onclick="removeFooterLogo()"
                                class="absolute top-2 left-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="footer_logo_empty_state" class="flex items-center justify-center w-full h-32 bg-gray-900 border-2 border-dashed border-gray-600 rounded-lg" style="{{ isset($settings['general']['footer_logo']) && $settings['general']['footer_logo'] ? 'display: none;' : '' }}">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-400">لا يوجد شعار للفوتر</p>
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <input type="file" 
                           name="settings[footer_logo]" 
                           id="footer_logo" 
                           accept="image/*"
                           onchange="previewFooterLogo(this)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition">
                    <p class="text-xs text-gray-500">PNG, JPG, SVG (يُفضل 200×60 بكسل) - للاستخدام في الفوتر الداكن</p>
                </div>
                
                <!-- Footer Logo Width Control -->
                <div class="mt-4">
                    <label for="footer_logo_width" class="block text-sm font-medium text-gray-700 mb-2">
                        عرض شعار الفوتر (بالبكسل)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" 
                               name="settings[footer_logo_width]" 
                               id="footer_logo_width" 
                               min="80" 
                               max="300" 
                               step="10"
                               value="{{ $settings['general']['footer_logo_width'] ?? '150' }}"
                               oninput="updateFooterLogoWidth(this.value)"
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-600">
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   id="footer_logo_width_display" 
                                   value="{{ $settings['general']['footer_logo_width'] ?? '150' }}"
                                   min="80" 
                                   max="300"
                                   onchange="document.getElementById('footer_logo_width').value = this.value; updateFooterLogoWidth(this.value)"
                                   class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-600">px</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">حدد عرض الشعار في الفوتر (من 80px إلى 300px)</p>
                    
                    <!-- Footer Logo Live Preview -->
                    <div class="mt-3 p-3 bg-gray-800 rounded-lg border border-gray-600">
                        <p class="text-xs font-medium text-gray-300 mb-2">معاينة في الفوتر:</p>
                        <div class="flex items-center justify-center py-4 bg-gray-900 rounded border border-gray-700">
                            <div id="footer_logo_size_preview" style="width: {{ $settings['general']['footer_logo_width'] ?? '150' }}px; height: auto; background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                                <span class="text-white text-xs font-bold">FOOTER LOGO</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">العرض الحالي: <span id="footer_current_width_display">{{ $settings['general']['footer_logo_width'] ?? '150' }}</span>px</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
