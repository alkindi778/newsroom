@extends('admin.layouts.app')

@section('title', 'إضافة قسم جديد')
@section('page-title', 'إضافة قسم جديد')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة قسم جديد</h1>
                <p class="mt-1 text-sm text-gray-600">أضف قسم جديد للصفحة الرئيسية</p>
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
    <form method="POST" action="{{ route('admin.homepage-sections.store') }}" class="space-y-6">
        @csrf

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
                           value="{{ old('name') }}"
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
                        المعرّف (Slug)
                    </label>
                    <input type="text" 
                           name="slug" 
                           id="slug" 
                           value="{{ old('slug') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">سيتم توليده تلقائياً إذا ترك فارغاً</p>
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
                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
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
                           value="{{ old('title') }}"
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
                           value="{{ old('subtitle') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subtitle') border-red-500 @enderror">
                    @error('subtitle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div id="category-field" style="display: none;">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        القسم المرتبط
                    </label>
                    <select name="category_id" 
                            id="category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                        <option value="">اختر قسم</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                               value="{{ old('order', 0) }}"
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
                               value="{{ old('items_count', 6) }}"
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
                        <option value="default" selected>القالب الافتراضي (Default)</option>
                        <option value="grid">شبكة متساوية (Grid)</option>
                        <option value="featured">خبر رئيسي مميز (Featured)</option>
                        <option value="list">قائمة عمودية (List)</option>
                        <option value="magazine">نمط مجلة (Magazine)</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">اختر شكل عرض الأخبار/التقارير/التحليلات (الافتراضي = القالب الأصلي للقسم)</p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="mr-2 block text-sm text-gray-900">
                        تفعيل القسم
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.homepage-sections.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    حفظ القسم
                </button>
            </div>
        </div>
    </form>
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

    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.manuallyEdited !== 'true') {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\u0600-\u06FF\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });

    slugInput.addEventListener('input', function() {
        if (this.value) {
            this.dataset.manuallyEdited = 'true';
        }
    });
});
</script>
@endpush
@endsection
