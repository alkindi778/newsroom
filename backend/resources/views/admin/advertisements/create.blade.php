@extends('admin.layouts.app')

@section('title', 'إضافة إعلان جديد')
@section('page-title', 'إضافة إعلان جديد')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إضافة إعلان جديد</h1>
            <p class="mt-1 text-sm text-gray-600">أنشئ إعلان جديد وحدد خصائصه</p>
        </div>
        <a href="{{ route('admin.advertisements.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <form method="POST" action="{{ route('admin.advertisements.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                    
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            عنوان الإعلان <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                        @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type & Position & Layout -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                نوع الإعلان <span class="text-red-500">*</span>
                            </label>
                            <select name="type" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                                <option value="">اختر النوع</option>
                                @foreach($types as $key => $value)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                موقع الإعلان <span class="text-red-500">*</span>
                            </label>
                            <select name="position" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror">
                                <option value="">اختر الموقع</option>
                                @foreach($positions as $key => $value)
                                <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Layout -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            تخطيط العرض <span class="text-red-500">*</span>
                        </label>
                        <select name="layout" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('layout') border-red-500 @enderror">
                            <option value="single" {{ old('layout', 'single') == 'single' ? 'selected' : '' }}>إعلان واحد (عرض كامل)</option>
                            <option value="double" {{ old('layout') == 'double' ? 'selected' : '' }}>إعلانين جنب بعض</option>
                            <option value="triple" {{ old('layout') == 'triple' ? 'selected' : '' }}>ثلاثة إعلانات جنب بعض</option>
                        </select>
                        @error('layout')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">حدد كيف سيتم عرض الإعلانات في نفس الموقع</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <!-- Homepage Section (if between_sections) -->
                        <div id="section-select-container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                عرض الإعلان بعد القسم
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <select name="after_section_id" 
                                    id="after_section_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">لا يوجد - عرض حسب الموقع فقط</option>
                                @foreach($homepageSections as $section)
                                <option value="{{ $section->id }}" {{ old('after_section_id') == $section->id ? 'selected' : '' }}>
                                    بعد: {{ $section->title }} (#{{ $section->order }})
                                </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">سيتم عرض الإعلان بعد القسم المحدد في الصفحة الرئيسية</p>
                        </div>
                    </div>

                    <!-- Link -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رابط الإعلان</label>
                        <input type="url" 
                               name="link" 
                               value="{{ old('link') }}"
                               placeholder="https://example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('link') border-red-500 @enderror">
                        @error('link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="open_new_tab" 
                                       {{ old('open_new_tab') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="mr-2 text-sm text-gray-700">فتح في تبويب جديد</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العرض (بكسل)</label>
                            <input type="number" 
                                   name="width" 
                                   value="{{ old('width') }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('width') border-red-500 @enderror">
                            @error('width')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الارتفاع (بكسل)</label>
                            <input type="number" 
                                   name="height" 
                                   value="{{ old('height') }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('height') border-red-500 @enderror">
                            @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- HTML Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">محتوى HTML مخصص</h2>
                    <textarea name="content" 
                              rows="6"
                              placeholder="<div>...</div>"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                    @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">يمكنك إضافة كود HTML مخصص للإعلان</p>
                </div>

                <!-- Targeting Options -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">خيارات الاستهداف</h2>
                    
                    <!-- Target Pages -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الصفحات المستهدفة</label>
                        <div class="grid grid-cols-2 gap-2 p-3 border border-gray-200 rounded-lg">
                            @foreach($pages as $key => $value)
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="target_pages[]" 
                                       value="{{ $key }}"
                                       {{ (is_array(old('target_pages')) && in_array($key, old('target_pages'))) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="mr-2 text-sm text-gray-700">{{ $value }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">اترك فارغاً للعرض في جميع الصفحات</p>
                    </div>

                    <!-- Target Categories -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الأقسام المستهدفة</label>
                        <select name="target_categories[]" 
                                multiple
                                size="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (is_array(old('target_categories')) && in_array($category->id, old('target_categories'))) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">اضغط Ctrl للتحديد المتعدد</p>
                    </div>

                    <!-- Target Devices -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الأجهزة المستهدفة</label>
                        <div class="flex gap-4 p-3 border border-gray-200 rounded-lg">
                            @foreach($devices as $key => $value)
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="target_devices[]" 
                                       value="{{ $key }}"
                                       {{ (is_array(old('target_devices')) && in_array($key, old('target_devices'))) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="mr-2 text-sm text-gray-700">{{ $value }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">اترك فارغاً للعرض على جميع الأجهزة</p>
                    </div>
                </div>

                <!-- Client Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات العميل</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
                            <input type="text" 
                                   name="client_name" 
                                   value="{{ old('client_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" 
                                   name="client_email" 
                                   value="{{ old('client_email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                        <input type="text" 
                               name="client_phone" 
                               value="{{ old('client_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sidebar (1 column) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">إعدادات النشر</h2>
                    
                    <!-- Active Status -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   {{ old('is_active') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="mr-2 text-sm font-medium text-gray-900">تفعيل الإعلان</span>
                        </label>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الأولوية</label>
                        <input type="number" 
                               name="priority" 
                               value="{{ old('priority', 0) }}"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">الأعلى يظهر أولاً (0 = الأدنى)</p>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء</label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ old('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">صورة الإعلان</h2>
                    
                    <div class="mb-4">
                        <input type="file" 
                               name="image" 
                               id="imageInput"
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror">
                        @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="hidden">
                        <img id="preview" 
                             src="" 
                             alt="معاينة" 
                             class="w-full rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 mb-3">
                        <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ الإعلان
                    </button>
                    
                    <a href="{{ route('admin.advertisements.index') }}" 
                       class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Image Preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Show/Hide Homepage Section Select based on Position
const positionSelect = document.querySelector('select[name="position"]');
const sectionContainer = document.getElementById('section-select-container');

function toggleSectionSelect() {
    const selectedPosition = positionSelect.value;
    if (selectedPosition === 'between_sections' || selectedPosition === 'homepage_top' || selectedPosition === 'homepage_bottom') {
        sectionContainer.style.display = 'block';
    } else {
        sectionContainer.style.display = 'none';
        // Reset selection when hidden
        document.getElementById('after_section_id').value = '';
    }
}

// Check on page load
toggleSectionSelect();

// Check on change
positionSelect.addEventListener('change', toggleSectionSelect);
</script>
@endpush
@endsection
