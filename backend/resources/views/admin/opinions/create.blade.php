@extends('admin.layouts.app')

@section('title', 'إضافة مقال جديد')
@section('page-title', 'إضافة مقال جديد')

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    <x-admin.alert-messages />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إضافة مقال جديد</h1>
            <p class="mt-1 text-sm text-gray-600">أدخل تفاصيل المقال الجديد</p>
        </div>
        
        <a href="{{ route('admin.opinions.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('admin.opinions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الأساسية</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            عنوان المقال <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="أدخل عنوان المقال"
                               required>
                        @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Writer -->
                    <div>
                        <label for="writer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            الكاتب <span class="text-red-500">*</span>
                        </label>
                        <select name="writer_id" 
                                id="writer_id"
                                class="writer-select w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('writer_id') ? 'border-red-500' : 'border-gray-300' }}"
                                required>
                            <option value="">اختر الكاتب</option>
                            @foreach($writers as $writer)
                            <option value="{{ $writer->id }}" {{ old('writer_id') == $writer->id ? 'selected' : '' }}>
                                {{ $writer->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('writer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-search"></i> يمكنك البحث عن الكاتب بكتابة اسمه
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_published" class="block text-sm font-medium text-gray-700 mb-2">حالة النشر</label>
                        <select name="is_published" 
                                id="is_published"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('is_published') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="0" {{ old('is_published', '0') == '0' ? 'selected' : '' }}>مسودة</option>
                            <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>نشر المقال</option>
                        </select>
                        @error('is_published')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Excerpt -->
                <div class="mt-6">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">المقدمة</label>
                    <textarea name="excerpt" 
                              id="excerpt"
                              rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('excerpt') ? 'border-red-500' : 'border-gray-300' }}"
                              placeholder="مقدمة مختصرة عن المقال...">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mt-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        محتوى المقال <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content"
                              rows="12"
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('content') ? 'border-red-500' : 'border-gray-300' }}"
                              placeholder="اكتب محتوى المقال هنا..."
                              required>{{ old('content') }}</textarea>
                    @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image using Media Picker -->
                <div class="mt-6">
                    {{-- Use Media Picker Component --}}
                    <x-admin.media-picker 
                        field="image" 
                        label="صورة المقال" 
                        :value="old('image', '')"
                        collection="opinions"
                        help="اختر صورة للمقال من مكتبة الوسائط أو ارفع صورة جديدة. يُفضل أن تكون بحجم 1200x600 بكسل" />
                </div>
            </div>

            <!-- Options -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">خيارات المقال</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Featured -->
                    <div class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" 
                               name="is_featured" 
                               id="is_featured"
                               value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_featured" class="mr-2 block text-sm text-gray-900">مقال مميز</label>
                    </div>

                    <!-- Allow Comments -->
                    <div class="flex items-center">
                        <input type="hidden" name="allow_comments" value="0">
                        <input type="checkbox" 
                               name="allow_comments" 
                               id="allow_comments"
                               value="1"
                               {{ old('allow_comments', '1') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="allow_comments" class="mr-2 block text-sm text-gray-900">السماح بالتعليقات</label>
                    </div>
                </div>

                <!-- Tags -->
                <div class="mt-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">الكلمات الدلالية</label>
                    <input type="text" 
                           name="tags" 
                           id="tags"
                           value="{{ old('tags') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('tags') ? 'border-red-500' : 'border-gray-300' }}"
                           placeholder="السياسة، الاقتصاد، الرأي (فصل بينها بفاصلة)">
                    <p class="mt-1 text-xs text-gray-500">اكتب الكلمات الدلالية مفصولة بفاصلة</p>
                    @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- SEO -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">تحسين محركات البحث (SEO)</h3>
                
                <div class="space-y-6">
                    <!-- Meta Title -->
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان SEO</label>
                        <input type="text" 
                               name="meta_title" 
                               id="meta_title"
                               value="{{ old('meta_title') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('meta_title') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="عنوان المقال في محركات البحث">
                        @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">وصف SEO</label>
                        <textarea name="meta_description" 
                                  id="meta_description"
                                  rows="3"
                                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('meta_description') ? 'border-red-500' : 'border-gray-300' }}"
                                  placeholder="وصف المقال في محركات البحث (160 حرف كحد أقصى)">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keywords -->
                    <div>
                        <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">كلمات مفتاحية</label>
                        <input type="text" 
                               name="keywords" 
                               id="keywords"
                               value="{{ old('keywords') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('keywords') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="كلمة1، كلمة2، كلمة3">
                        @error('keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex flex-col sm:flex-row justify-between gap-3">
                <a href="{{ route('admin.opinions.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    إلغاء
                </a>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="action" value="draft"
                            class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        حفظ كمسودة
                    </button>
                    
                    @can('نشر مقالات الرأي')
                    <button type="submit" name="action" value="publish"
                            class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        نشر المقال
                    </button>
                    @else
                    <button type="submit" name="action" value="publish"
                            class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ المقال
                    </button>
                    @endcan
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        padding-right: 8px !important;
        color: #374151 !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        left: 8px !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        outline: 2px solid transparent !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        direction: rtl;
    }
    
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem !important;
        direction: rtl;
    }
    
    .select2-results__option {
        padding: 0.5rem !important;
        text-align: right;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<!-- Select2 JS -->
<script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        console.log('jQuery loaded:', typeof jQuery !== 'undefined');
        console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
        
        // تفعيل Select2 على خانة الكاتب
        $('#writer_id').select2({
            placeholder: 'ابحث عن الكاتب...',
            allowClear: true,
            dir: 'rtl',
            language: {
                noResults: function() {
                    return "لم يتم العثور على نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                },
                inputTooShort: function() {
                    return "يرجى إدخال حرف واحد على الأقل";
                }
            },
            width: '100%'
        });
        
        console.log('Select2 initialized on #writer_id');
    });
</script>
@endpush
