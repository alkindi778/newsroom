@extends('admin.layouts.app')

@section('title', 'إضافة خبر جديد')
@section('page-title', 'إضافة خبر جديد')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة خبر جديد</h1>
                <p class="mt-1 text-sm text-gray-600">أدخل تفاصيل الخبر الجديد</p>
            </div>
            <a href="{{ route('admin.articles.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Titles -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <!-- Subtitle -->
                    <div class="mb-4">
                        <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-2">العنوان الفرعي</label>
                        <input type="text" 
                               id="subtitle" 
                               name="subtitle" 
                               value="{{ old('subtitle') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('subtitle') ? 'border-red-500' : '' }}"
                               placeholder="عنوان فرعي أو تفصيل إضافي (اختياري)">
                        @error('subtitle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Main Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">العنوان الرئيسي *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('title') ? 'border-red-500' : '' }}"
                               placeholder="أدخل العنوان الرئيسي للخبر">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden Slug Field -->
                    <input type="hidden" id="slug" name="slug" value="{{ old('slug') }}">
                </div>

                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">محتوى الخبر *</label>
                    <textarea id="content" 
                              name="content" 
                              rows="12" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('content') ? 'border-red-500' : '' }}"
                              placeholder="اكتب محتوى الخبر هنا...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden Summary Field - Auto-generated -->
                <input type="hidden" id="summary" name="summary" value="{{ old('summary') }}">
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Action Buttons - Desktop Only -->
                <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">إجراءات النشر</h3>
                    
                    <div class="flex flex-col space-y-3">
                        <button type="submit" 
                                name="action" 
                                value="publish"
                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            نشر الخبر
                        </button>
                        <button type="submit" 
                                name="action" 
                                value="draft"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            حفظ كمسودة
                        </button>
                        <a href="{{ route('admin.articles.index') }}" 
                           class="w-full px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-center">
                            إلغاء
                        </a>
                    </div>
                </div>

                <!-- Publish Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">إعدادات النشر</h3>
                    
                    <!-- News Source -->
                    <div class="mb-4">
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-2">مصدر الخبر</label>
                        <input type="text" 
                               id="source" 
                               name="source" 
                               value="{{ old('source') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('source') ? 'border-red-500' : '' }}"
                               placeholder="مثال: عدن خاص، وكالة الأنباء، إلخ...">
                        @error('source')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">حالة النشر</label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">القسم *</label>
                        <select id="category_id" 
                                name="category_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('category_id') ? 'border-red-500' : '' }}">
                            <option value="">اختر القسم</option>
                            @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Show in Slider -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="show_in_slider_toggle" class="text-sm font-medium text-gray-700 cursor-pointer">عرض في السلايدر</label>
                                <p class="text-xs text-gray-500">سيظهر الخبر في السلايدر الرئيسي للموقع</p>
                            </div>
                            <label for="show_in_slider_toggle" class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="show_in_slider_toggle"
                                       name="show_in_slider" 
                                       value="1" 
                                       {{ old('show_in_slider') ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Breaking News -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_breaking_news_toggle" class="text-sm font-medium text-gray-700 cursor-pointer">خبر عاجل</label>
                                <p class="text-xs text-gray-500">سيتم وضع علامة "عاجل" على الخبر</p>
                            </div>
                            <label for="is_breaking_news_toggle" class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="is_breaking_news_toggle"
                                       name="is_breaking_news" 
                                       value="1" 
                                       {{ old('is_breaking_news') ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Hidden Auto Publish Date -->
                    <input type="hidden" id="published_at" name="published_at" value="{{ old('published_at') }}">
                </div>

                <!-- Featured Image using Media Picker -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">الصورة الرئيسية</h3>
                    
                    {{-- Use Media Picker Component --}}
                    <x-admin.media-picker 
                        field="image" 
                        label="اختيار الصورة الرئيسية" 
                        :value="old('image', '')"
                        collection="articles"
                        help="اختر صورة من مكتبة الوسائط أو ارفع صورة جديدة. يُفضل أن تكون بحجم 1200x600 بكسل"
                        required />

                    <!-- Image Alt Text -->
                    <div class="mt-4">
                        <label for="image_alt" class="block text-sm font-medium text-gray-700 mb-2">وصف الصورة</label>
                        <input type="text" 
                               id="image_alt" 
                               name="image_alt" 
                               value="{{ old('image_alt') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="وصف مختصر للصورة">
                        <p class="mt-1 text-xs text-gray-500">يساعد في SEO وإمكانية الوصول</p>
                        @error('image_alt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hidden SEO Fields - Auto-generated -->
                <input type="hidden" id="meta_description" name="meta_description" value="{{ old('meta_description') }}">
                <input type="hidden" id="keywords" name="keywords" value="{{ old('keywords') }}">
            </div>
        </div>

        <!-- Mobile Action Buttons - Show only on mobile -->
        <div class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-6">
            <div class="flex flex-col space-y-3">
                <button type="submit" 
                        name="action" 
                        value="publish"
                        class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    نشر الخبر
                </button>
                <button type="submit" 
                        name="action" 
                        value="draft"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    حفظ كمسودة
                </button>
                <a href="{{ route('admin.articles.index') }}" 
                   class="w-full px-4 py-3 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-center">
                    إلغاء
                </a>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle switches functionality
    const sliderToggle = document.getElementById('show_in_slider_toggle');
    const breakingToggle = document.getElementById('is_breaking_news_toggle');
    
    // Ensure toggles are clickable
    if (sliderToggle) {
        const sliderSwitch = sliderToggle.nextElementSibling;
        if (sliderSwitch) {
            sliderSwitch.addEventListener('click', function(e) {
                e.preventDefault();
                sliderToggle.checked = !sliderToggle.checked;
            });
        }
    }
    
    if (breakingToggle) {
        const breakingSwitch = breakingToggle.nextElementSibling;
        if (breakingSwitch) {
            breakingSwitch.addEventListener('click', function(e) {
                e.preventDefault();
                breakingToggle.checked = !breakingToggle.checked;
            });
        }
    }
    
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const contentInput = document.getElementById('content');
    const summaryInput = document.getElementById('summary');
    const metaDescriptionInput = document.getElementById('meta_description');
    const keywordsInput = document.getElementById('keywords');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            const slug = this.value
                .trim()
                // إزالة التشكيل العربي
                .replace(/[\u064B-\u065F\u0670]/g, '')
                // تحويل الحروف العربية الخاصة
                .replace(/آ|أ|إ|ٱ/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/ؤ/g, 'و')
                .replace(/ئ/g, 'ي')
                // الإبقاء على الحروف العربية والإنجليزية والأرقام والمسافات فقط
                .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-zA-Z0-9\s-]/g, '')
                // استبدال المسافات المتعددة بمسافة واحدة
                .replace(/\s+/g, '-')
                // استبدال الشرطات المتعددة بشرطة واحدة
                .replace(/-+/g, '-')
                // إزالة الشرطات من البداية والنهاية
                .replace(/^-+|-+$/g, '')
                .toLowerCase();
            slugInput.value = slug;
        });
    }
    
    // Function to clean text
    function cleanText(text) {
        return text
            .replace(/<[^>]*>/g, '') // Remove HTML tags
            .replace(/&[^;]+;/g, ' ') // Remove HTML entities
            .replace(/\s+/g, ' ') // Replace multiple spaces with single space
            .trim();
    }
    
    // Function to extract keywords from text
    function extractKeywords(title, content) {
        const allText = (title + ' ' + content).toLowerCase();
        const cleanedText = cleanText(allText);
        
        // Arabic and English stop words to exclude
        const stopWords = ['في', 'من', 'إلى', 'على', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك', 'الذي', 'التي', 'يتم', 'تم', 'كما', 'أن', 'أي', 'كل', 'بعض', 'جميع', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'the', 'a', 'an', 'is', 'are', 'was', 'were', 'will', 'would', 'could', 'should', 'may', 'might', 'can'];
        
        // Extract words (Arabic and English)
        const words = cleanedText.match(/[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-zA-Z]{3,}/g) || [];
        
        // Filter out stop words and get unique words
        const keywords = [...new Set(words.filter(word => 
            !stopWords.includes(word.toLowerCase()) && word.length >= 3
        ))];
        
        // Return first 8 keywords
        return keywords.slice(0, 8).join('، ');
    }
    
    // Auto-generate summary, meta description and keywords from content
    if (contentInput && summaryInput && metaDescriptionInput && keywordsInput) {
        function updateSEOFields() {
            const content = contentInput.value;
            const title = titleInput ? titleInput.value : '';
            
            if (content) {
                const cleanContent = cleanText(content);
                
                // Generate summary (150 characters)
                const summary = cleanContent.length > 150 
                    ? cleanContent.substring(0, 150) + '...'
                    : cleanContent;
                summaryInput.value = summary;
                
                // Generate meta description (157 characters max to leave room for "...")
                const metaDescription = cleanContent.length > 157 
                    ? cleanContent.substring(0, 157) + '...'
                    : cleanContent;
                metaDescriptionInput.value = metaDescription;
                
                // Generate keywords
                const keywords = extractKeywords(title, content);
                keywordsInput.value = keywords;
            }
        }
        
        contentInput.addEventListener('input', updateSEOFields);
        if (titleInput) {
            titleInput.addEventListener('input', updateSEOFields);
        }
    }
    
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const uploadArea = document.getElementById('upload-area');
    const removeImageBtn = document.getElementById('remove-image');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    alert('حجم الملف كبير جداً. الحد الأقصى 10MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                if (!file.type.startsWith('image/')) {
                    alert('يرجى اختيار ملف صورة صحيح');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadArea.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Remove image preview
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            previewContainer.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            previewImage.src = '';
        });
    }
    
    // Drag and drop functionality
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                imageInput.dispatchEvent(new Event('change'));
            }
        });
    }
});
</script>
@endpush
