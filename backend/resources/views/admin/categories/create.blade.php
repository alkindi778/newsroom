@extends('admin.layouts.app')

@section('title', 'إضافة قسم جديد')
@section('page-title', 'إضافة قسم جديد')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة قسم جديد</h1>
                <p class="mt-1 text-sm text-gray-600">أدخل تفاصيل القسم الجديد</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم القسم *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                       placeholder="أدخل اسم القسم">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">الرابط المختصر</label>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       value="{{ old('slug') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-300 @enderror"
                       placeholder="سيتم إنشاؤه تلقائياً من الاسم">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">يُستخدم في رابط القسم. اتركه فارغاً لإنشائه تلقائياً.</p>
            </div>

            <!-- Category Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف القسم</label>
                <textarea id="description" 
                          name="description" 
                          rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                          placeholder="اكتب وصفاً مختصراً للقسم (اختياري)">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">لون القسم</label>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <input type="color" 
                           id="color" 
                           name="color" 
                           value="{{ old('color', '#3B82F6') }}"
                           class="h-10 w-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <input type="text" 
                           id="color_text" 
                           value="{{ old('color', '#3B82F6') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="#3B82F6">
                </div>
                <p class="mt-1 text-xs text-gray-500">سيُستخدم لتمييز هذا القسم في الواجهات المختلفة</p>
            </div>

            <!-- Parent Category -->
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">القسم الأب</label>
                <select
                    id="parent_id"
                    name="parent_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('parent_id') border-red-300 @enderror"
                >
                    <option value="">قسم رئيسي (بدون أب)</option>
                    @foreach($categories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                            {{ $parentCategory->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">يمكنك اختيار قسم أب لعمل أقسام فرعية، أو تركه فارغاً ليكون قسم رئيسي</p>
            </div>

            <!-- Category Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">حالة القسم</label>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <label class="flex items-center">
                        <input type="radio" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="mr-2 text-sm text-gray-700">نشط</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" 
                               name="is_active" 
                               value="0" 
                               {{ old('is_active') == '0' ? 'checked' : '' }}
                               class="text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="mr-2 text-sm text-gray-700">غير نشط</span>
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">الأقسام غير النشطة لن تظهر في الواجهة الأمامية</p>
            </div>

            <!-- SEO Settings -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إعدادات SEO</h3>
                
                <!-- Meta Title -->
                <div class="mb-4">
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان الميتا</label>
                    <input type="text" 
                           id="meta_title" 
                           name="meta_title" 
                           value="{{ old('meta_title') }}"
                           maxlength="60"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="عنوان القسم في محركات البحث">
                    <p class="mt-1 text-xs text-gray-500">الحد الأقصى 60 حرف</p>
                </div>

                <!-- Meta Description -->
                <div class="mb-4">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">وصف الميتا</label>
                    <textarea id="meta_description" 
                              name="meta_description" 
                              rows="3" 
                              maxlength="160"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="وصف القسم في محركات البحث">{{ old('meta_description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">الحد الأقصى 160 حرف</p>
                </div>

                <!-- Keywords -->
                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">الكلمات المفتاحية</label>
                    <input type="text" 
                           id="keywords" 
                           name="keywords" 
                           value="{{ old('keywords') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="كلمة1، كلمة2، كلمة3">
                    <p class="mt-1 text-xs text-gray-500">افصل بين الكلمات بفواصل</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    إنشاء القسم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.value.trim()) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-z0-9\s]/g, '')
                    .replace(/\s+/g, '-')
                    .trim();
                slugInput.value = slug;
            }
        });
    }
    
    // Sync color picker with text input
    const colorPicker = document.getElementById('color');
    const colorText = document.getElementById('color_text');
    
    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });
        
        colorText.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });
    }
});
</script>
@endpush
