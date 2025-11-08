@extends('admin.layouts.app')

@section('title', 'تعديل القسم')
@section('page-title', 'تعديل القسم')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل القسم: {{ $homepageSection->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">تعديل إعدادات القسم في الصفحة الرئيسية</p>
            </div>
            <a href="{{ route('admin.homepage-sections.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.homepage-sections.update', $homepageSection) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم القسم <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $homepageSection->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">الاسم الداخلي للقسم (للإدارة فقط)</p>
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        المعرّف (Slug) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="slug" 
                           id="slug" 
                           value="{{ old('slug', $homepageSection->slug) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع القسم <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            id="type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">اختر نوع القسم</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $homepageSection->type) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        العنوان المعروض
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $homepageSection->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">العنوان الذي سيظهر في الموقع (اختياري)</p>
                </div>

                <!-- Subtitle -->
                <div>
                    <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-2">
                        العنوان الفرعي
                    </label>
                    <input type="text" 
                           name="subtitle" 
                           id="subtitle" 
                           value="{{ old('subtitle', $homepageSection->subtitle) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subtitle') border-red-500 @enderror">
                    @error('subtitle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div id="category-field" style="display: {{ old('type', $homepageSection->type) == 'category_news' ? 'block' : 'none' }};">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        القسم المرتبط
                    </label>
                    <select name="category_id" 
                            id="category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                        <option value="">اختر قسم</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $homepageSection->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">اختر القسم الذي تريد عرض أخباره (للأقسام المرتبطة بالأخبار فقط)</p>
                </div>

                <!-- Order & Items Count -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                            الترتيب <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               value="{{ old('order', $homepageSection->order) }}"
                               min="0"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('order') border-red-500 @enderror">
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">ترتيب ظهور القسم (الأقل أولاً)</p>
                    </div>

                    <!-- Items Count -->
                    <div>
                        <label for="items_count" class="block text-sm font-medium text-gray-700 mb-2">
                            عدد العناصر <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="items_count" 
                               id="items_count" 
                               value="{{ old('items_count', $homepageSection->items_count) }}"
                               min="1"
                               max="50"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('items_count') border-red-500 @enderror">
                        @error('items_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">عدد العناصر المعروضة في القسم</p>
                    </div>
                </div>

                <!-- Template Style (for content sections only) -->
                <div id="template-field" style="display: none;">
                    <label for="template_style" class="block text-sm font-medium text-gray-700 mb-2">
                        نمط العرض (القالب)
                    </label>
                    <select name="template_style" 
                            id="template_style" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="default" {{ (old('settings.template', $homepageSection->settings['template'] ?? 'default') == 'default') ? 'selected' : '' }}>
                            القالب الافتراضي (Default)
                        </option>
                        <option value="grid" {{ (old('settings.template', $homepageSection->settings['template'] ?? '') == 'grid') ? 'selected' : '' }}>
                            شبكة متساوية (Grid)
                        </option>
                        <option value="featured" {{ (old('settings.template', $homepageSection->settings['template'] ?? '') == 'featured') ? 'selected' : '' }}>
                            خبر رئيسي مميز (Featured)
                        </option>
                        <option value="list" {{ (old('settings.template', $homepageSection->settings['template'] ?? '') == 'list') ? 'selected' : '' }}>
                            قائمة عمودية (List)
                        </option>
                        <option value="magazine" {{ (old('settings.template', $homepageSection->settings['template'] ?? '') == 'magazine') ? 'selected' : '' }}>
                            نمط مجلة (Magazine)
                        </option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">اختر شكل عرض الأخبار/التقارير/التحليلات (الافتراضي = القالب الأصلي للقسم)</p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $homepageSection->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="mr-2 block text-sm text-gray-900">
                        تفعيل القسم
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.homepage-sections.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        حفظ التغييرات
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <div class="max-w-4xl mx-auto mt-4">
        <form method="POST" action="{{ route('admin.homepage-sections.destroy', $homepageSection) }}" 
              onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟ هذا الإجراء لا يمكن التراجع عنه.')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                حذف القسم
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const categoryField = document.getElementById('category-field');
    const categoryInput = document.getElementById('category_id');
    const templateField = document.getElementById('template-field');

    // Types that support templates
    const templateSupportedTypes = ['latest_news', 'category_news', 'trending'];

    function updateFields() {
        const selectedType = typeSelect.value;
        
        // Category field
        if (selectedType === 'category_news') {
            categoryField.style.display = 'block';
            categoryInput.required = true;
        } else {
            categoryField.style.display = 'none';
            categoryInput.required = false;
            categoryInput.value = '';
        }

        // Template field
        if (templateSupportedTypes.includes(selectedType)) {
            templateField.style.display = 'block';
        } else {
            templateField.style.display = 'none';
        }
    }

    // Show/hide fields based on type
    typeSelect.addEventListener('change', updateFields);
    
    // Initial state
    updateFields();
});
</script>
@endpush
@endsection
