@extends('admin.layouts.app')

@section('title', 'تحرير الخبر')
@section('page-title', 'تحرير الخبر')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تحرير الخبر</h1>
                <p class="mt-1 text-sm text-gray-600">تحرير تفاصيل الخبر</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <a href="{{ route('admin.articles.show', $article) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    معاينة
                </a>
                <a href="{{ route('admin.articles.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
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
                               value="{{ old('subtitle', $article->subtitle ?? '') }}"
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
                               value="{{ old('title', $article->title) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('title') ? 'border-red-500' : '' }}"
                               placeholder="أدخل العنوان الرئيسي للخبر">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden Slug Field -->
                    <input type="hidden" id="slug" name="slug" value="{{ old('slug', $article->slug) }}">
                </div>

                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">محتوى الخبر *</label>
                    <textarea id="content" 
                              name="content" 
                              required
                              class="tinymce-editor">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden Summary Field - Auto-generated -->
                <input type="hidden" id="summary" name="summary" value="{{ old('summary', $article->summary ?? '') }}">
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
                            حفظ ونشر
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
                               value="{{ old('source', $article->source ?? '') }}"
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
                            <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>منشور</option>
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
                            <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
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
                                       {{ old('show_in_slider', $article->show_in_slider) ? 'checked' : '' }}
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
                                       {{ old('is_breaking_news', $article->is_breaking_news) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Hidden Auto Publish Date -->
                    <input type="hidden" id="published_at" name="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">

                    <!-- Article Stats -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">إحصائيات الخبر</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>تاريخ الإنشاء:</span>
                                <span>{{ $article->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>آخر تحديث:</span>
                                <span>{{ $article->updated_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>بواسطة:</span>
                                <span>{{ $article->user->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Image using Media Picker -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">الصورة الرئيسية</h3>
                    
                    {{-- Use Media Picker Component --}}
                    <x-admin.media-picker 
                        field="image" 
                        label="اختيار/تغيير الصورة الرئيسية" 
                        :value="old('image', $article->image_url ?? ($article->image ? asset('storage/' . $article->image) : ''))"
                        collection="articles"
                        help="اختر صورة من مكتبة الوسائط أو ارفع صورة جديدة. يُفضل أن تكون بحجم 1200x600 بكسل"
                        required />

                    <!-- Image Alt Text -->
                    <div class="mt-4">
                        <label for="image_alt" class="block text-sm font-medium text-gray-700 mb-2">وصف الصورة</label>
                        <input type="text" 
                               id="image_alt" 
                               name="image_alt" 
                               value="{{ old('image_alt', $article->image_alt) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="وصف مختصر للصورة">
                        @error('image_alt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hidden SEO Fields - Auto-generated -->
                <input type="hidden" id="meta_description" name="meta_description" value="{{ old('meta_description', $article->meta_description ?? '') }}">
                <input type="hidden" id="keywords" name="keywords" value="{{ old('keywords', $article->keywords ?? '') }}">
            </div>
        </div>

        <!-- Mobile Action Buttons - Show only on mobile -->
        <div class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-6">
            <div class="flex flex-col space-y-3">
                <button type="submit" 
                        name="action" 
                        value="publish"
                        class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    حفظ ونشر
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
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    // معالجة المحتوى القديم - تحويله إلى فقرات قبل تحميل المحرر
    const contentTextarea = document.getElementById('content');
    if (contentTextarea && contentTextarea.value) {
        let content = contentTextarea.value;
        // إذا كان المحتوى لا يحتوي على HTML tags
        if (!content.match(/<[^>]+>/)) {
            // تقسيم على الجمل الطويلة (كل جملة تنتهي بنقطة أو سطر جديد)
            let sentences = content.split(/\.\s+|\n+/);
            let paragraphs = [];
            let currentPara = '';
            
            sentences.forEach((sentence, index) => {
                sentence = sentence.trim();
                if (sentence.length > 0) {
                    // إضافة النقطة إذا لم تكن موجودة
                    if (!sentence.endsWith('.') && !sentence.endsWith('،') && !sentence.endsWith(':')) {
                        sentence += '.';
                    }
                    currentPara += sentence + ' ';
                    
                    // كل 3-4 جمل، ننشئ فقرة جديدة
                    if ((index + 1) % 3 === 0 || sentence.includes(':')) {
                        paragraphs.push('<p>' + currentPara.trim() + '</p>');
                        currentPara = '';
                    }
                }
            });
            
            // إضافة أي محتوى متبقي
            if (currentPara.trim().length > 0) {
                paragraphs.push('<p>' + currentPara.trim() + '</p>');
            }
            
            if (paragraphs.length > 0) {
                contentTextarea.value = paragraphs.join('\n\n');
            }
        }
    }
    
    // Initialize TinyMCE
    if (typeof initTinyMCE === 'function') {
        initTinyMCE('#content');
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
            if (!slugInput.value.trim()) {
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
            }
        });
    }
    
    // دالة لتنظيف النص
    function cleanText(text) {
        return text
            .replace(/<[^>]*>/g, '') // إزالة أكواد HTML
            .replace(/&[^;]+;/g, ' ') // إزالة رموز HTML الخاصة
            .replace(/\s+/g, ' ') // استبدال المسافات المتعددة بمسافة واحدة
            .trim();
    }
    
    // دالة لاستخراج الكلمات الدلالية من النص
    function extractKeywords(title, content) {
        const allText = (title + ' ' + content).toLowerCase();
        const cleanedText = cleanText(allText);
        
        // الكلمات المستبعدة (stop words) بالعربية والإنجليزية
        const stopWords = ['في', 'من', 'إلى', 'على', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك', 'الذي', 'التي', 'يتم', 'تم', 'كما', 'أن', 'أي', 'كل', 'بعض', 'جميع', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'the', 'a', 'an', 'is', 'are', 'was', 'were', 'will', 'would', 'could', 'should', 'may', 'might', 'can'];
        
        // استخراج الكلمات (عربية وإنجليزية)
        const words = cleanedText.match(/[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-zA-Z]{3,}/g) || [];
        
        // تصفية الكلمات المستبعدة والحصول على كلمات فريدة
        const keywords = [...new Set(words.filter(word => 
            !stopWords.includes(word.toLowerCase()) && word.length >= 3
        ))];
        
        // إرجاع أول 8 كلمات دلالية
        return keywords.slice(0, 8).join('، ');
    }
    
    // توليد تلقائي للملخص ووصف SEO والكلمات الدلالية من المحتوى
    if (contentInput && summaryInput && metaDescriptionInput && keywordsInput) {
        function updateSEOFields() {
            const content = contentInput.value;
            const title = titleInput ? titleInput.value : '';
            
            if (content) {
                const cleanContent = cleanText(content);
                
                // توليد الملخص (150 حرف)
                const summary = cleanContent.length > 150 
                    ? cleanContent.substring(0, 150) + '...'
                    : cleanContent;
                summaryInput.value = summary;
                
                // توليد وصف SEO (160 حرف)  
                const metaDescription = cleanContent.length > 160 
                    ? cleanContent.substring(0, 160) + '...'
                    : cleanContent;
                metaDescriptionInput.value = metaDescription;
                
                // توليد الكلمات الدلالية
                const keywords = extractKeywords(title, content);
                keywordsInput.value = keywords;
            }
        }
        
        contentInput.addEventListener('input', updateSEOFields);
        if (titleInput) {
            titleInput.addEventListener('input', updateSEOFields);
        }
    }
    
    // وظيفة معاينة الصورة
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const uploadArea = document.getElementById('upload-area');
    const removeNewImageBtn = document.getElementById('remove-new-image');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // التحقق من حجم الملف (الحد الأقصى 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('حجم الملف كبير جداً. الحد الأقصى 10MB');
                    this.value = '';
                    return;
                }
                
                // التحقق من نوع الملف
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
    
    // إزالة معاينة الصورة الجديدة
    if (removeNewImageBtn) {
        removeNewImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            previewContainer.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            previewImage.src = '';
        });
    }
    
    // وظيفة السحب والإفلات
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
