@extends('admin.layouts.app')

@section('title', 'تعديل تغذية RSS')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.rss.index') }}" class="hover:text-blue-600">تغذيات RSS</a>
            <i class="fas fa-chevron-left text-xs"></i>
            <span>تعديل</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">تعديل تغذية RSS</h1>
    </div>

    <form action="{{ route('admin.rss.update', $rssFeed) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                   value="{{ old('title', $rssFeed->title) }}" 
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $rssFeed->description) }}</textarea>
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
                                <option value="{{ $category->id }}" {{ old('category_id', $rssFeed->category_id) == $category->id ? 'selected' : '' }}>
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
                                <option value="ar" {{ old('language', $rssFeed->language) == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ old('language', $rssFeed->language) == 'en' ? 'selected' : '' }}>English</option>
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
                                   value="{{ old('items_count', $rssFeed->items_count) }}" 
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
                                   value="{{ old('ttl', $rssFeed->ttl) }}" 
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
                                   value="{{ old('copyright', $rssFeed->copyright) }}" 
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
                                   value="{{ old('image_url', $rssFeed->image_url) }}" 
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
                                   {{ old('is_active', $rssFeed->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="mr-2 text-sm font-medium text-gray-700">
                                تفعيل التغذية
                            </label>
                        </div>

                        <div class="pt-4 border-t border-gray-200 space-y-3">
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.rss.index') }}" 
                               class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Feed Info Card -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">معلومات التغذية</h3>
                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>تاريخ الإنشاء:</span>
                            <span>{{ $rssFeed->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>آخر تحديث:</span>
                            <span>{{ $rssFeed->updated_at->format('Y-m-d') }}</span>
                        </div>
                        @if($rssFeed->last_generated_at)
                        <div class="flex justify-between">
                            <span>آخر توليد:</span>
                            <span>{{ $rssFeed->last_generated_at->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 ml-2"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-2">رابط التغذية:</p>
                            <code class="block bg-blue-100 px-2 py-1 rounded text-xs break-all">{{ $rssFeed->url }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
