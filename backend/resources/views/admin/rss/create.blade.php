@extends('admin.layouts.app')

@section('title', 'إضافة تغذية RSS جديدة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.rss.index') }}" class="hover:text-blue-600">تغذيات RSS</a>
            <i class="fas fa-chevron-left text-xs"></i>
            <span>إضافة جديد</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">إضافة تغذية RSS جديدة</h1>
    </div>

    <form action="{{ route('admin.rss.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                    
                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان التغذية <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror" 
                                   value="{{ old('title') }}" 
                                   required>
                            @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                وصف التغذية
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">وصف قصير يظهر في التغذية</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                القسم
                            </label>
                            <select name="category_id" 
                                    id="category_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">جميع الأقسام</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">اختر قسم محدد أو اترك فارغاً لجميع الأقسام</p>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات المتقدمة</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                لغة التغذية <span class="text-red-500">*</span>
                            </label>
                            <select name="language" 
                                    id="language" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    required>
                                <option value="ar" {{ old('language', 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <!-- Items Count -->
                        <div>
                            <label for="items_count" class="block text-sm font-medium text-gray-700 mb-2">
                                عدد العناصر <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="items_count" 
                                   id="items_count" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('items_count', 50) }}" 
                                   min="1" 
                                   max="100" 
                                   required>
                            <p class="mt-1 text-xs text-gray-500">من 1 إلى 100</p>
                        </div>

                        <!-- TTL -->
                        <div>
                            <label for="ttl" class="block text-sm font-medium text-gray-700 mb-2">
                                مدة التحديث (دقيقة) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="ttl" 
                                   id="ttl" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('ttl', 60) }}" 
                                   min="1" 
                                   required>
                            <p class="mt-1 text-xs text-gray-500">مدة التحديث بالدقائق</p>
                        </div>

                        <!-- Copyright -->
                        <div>
                            <label for="copyright" class="block text-sm font-medium text-gray-700 mb-2">
                                حقوق النشر
                            </label>
                            <input type="text" 
                                   name="copyright" 
                                   id="copyright" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('copyright') }}" 
                                   placeholder="© 2025 اسم الموقع">
                        </div>

                        <!-- Image URL -->
                        <div class="md:col-span-2">
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الصورة
                            </label>
                            <input type="url" 
                                   name="image_url" 
                                   id="image_url" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('image_url') }}" 
                                   placeholder="https://example.com/logo.png">
                            <p class="mt-1 text-xs text-gray-500">رابط صورة الموقع (اختياري)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">النشر</h2>
                    
                    <div class="space-y-4">
                        <!-- Status -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1" 
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="mr-2 text-sm font-medium text-gray-700">
                                تفعيل التغذية
                            </label>
                        </div>

                        <div class="pt-4 border-t border-gray-200 space-y-3">
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التغذية
                            </button>
                            <a href="{{ route('admin.rss.index') }}" 
                               class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 ml-2"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-2">معلومات مفيدة:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• التغذية ستكون متاحة على: <code class="bg-blue-100 px-1 rounded">/rss/slug</code></li>
                                <li>• يمكن تحديد قسم معين أو جميع الأقسام</li>
                                <li>• عدد العناصر يتحكم في كم خبر يظهر</li>
                                <li>• مدة التحديث تحدد متى يُحدّث القارئ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
