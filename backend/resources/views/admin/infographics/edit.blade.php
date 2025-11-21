@extends('admin.layouts.app')

@section('title', 'تعديل إنفوجرافيك')
@section('page-title', 'تعديل إنفوجرافيك')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تعديل: {{ $infographic->title }}</h1>
            <p class="mt-1 text-sm text-gray-600">عدّل معلومات الإنفوجرافيك</p>
        </div>
        <a href="{{ route('admin.infographics.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-right"></i>
            <span>عودة</span>
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.infographics.update', $infographic) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Current Image Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="relative inline-block">
                    <img src="{{ asset('storage/' . $infographic->image) }}" 
                         alt="{{ $infographic->title }}" 
                         class="rounded-lg shadow-md max-w-sm w-full h-auto">
                    @php
                        $filePath = public_path('storage/' . $infographic->image);
                        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                    @endphp
                    @if($fileSize > 0)
                    <div class="absolute top-2 right-2 bg-white px-3 py-1 rounded-full text-xs font-medium text-gray-700 shadow-sm">
                        {{ number_format($fileSize / 1024, 0) }} KB
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Main Content Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 space-y-6">

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        العنوان <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $infographic->title) }}" 
                           required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('title') border-red-500 @enderror" 
                           placeholder="أدخل عنوان الإنفوجرافيك">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-2 text-xs text-blue-600 flex items-center gap-1">
                        <i class="fas fa-robot"></i>
                        سيتم ترجمته تلقائياً بالذكاء الاصطناعي
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror" 
                              placeholder="أدخل وصفاً مختصراً للإنفوجرافيك">{{ old('description', $infographic->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-2 text-xs text-blue-600 flex items-center gap-1">
                        <i class="fas fa-robot"></i>
                        سيتم ترجمته تلقائياً بالذكاء الاصطناعي
                    </p>
                </div>

                <!-- Image Upload using Media Picker -->
                <x-admin.media-picker 
                    field="image" 
                    label="تغيير الصورة (اختياري)" 
                    :value="old('image', $infographic->image ? asset('storage/' . $infographic->image) : '')"
                    collection="infographics"
                    help="اترك فارغاً للإبقاء على الصورة الحالية، أو اختر صورة جديدة من المكتبة" />

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        الرابط (Slug)
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug', $infographic->slug) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('slug') border-red-500 @enderror" 
                           placeholder="الرابط الفريد">
                    @error('slug')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                        الوسوم (Tags)
                    </label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           value="{{ old('tags', is_array($infographic->tags) ? implode(', ', $infographic->tags) : '') }}" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tags') border-red-500 @enderror" 
                           placeholder="افصل بينها بفاصلة: سياسة، اقتصاد، رياضة">
                    @error('tags')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Checkboxes -->
                <div class="flex flex-wrap gap-4">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               class="sr-only peer"
                               {{ old('is_active', $infographic->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700 group-hover:text-gray-900">نشط</span>
                    </label>

                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               id="is_featured" 
                               name="is_featured" 
                               value="1" 
                               class="sr-only peer"
                               {{ old('is_featured', $infographic->is_featured) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700 group-hover:text-gray-900 flex items-center gap-1">
                            <i class="fas fa-star"></i>
                            مميز
                        </span>
                    </label>
                </div>

                <!-- Stats -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                        </div>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-blue-800">إحصائيات</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-eye"></i>
                                    <span class="font-semibold">المشاهدات:</span> 
                                    {{ number_format($infographic->views) }}
                                </p>
                                <p class="flex items-center gap-2 mt-1">
                                    <i class="fas fa-calendar"></i>
                                    <span class="font-semibold">تاريخ الإنشاء:</span> 
                                    {{ $infographic->created_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.infographics.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors">
                        <i class="fas fa-save"></i>
                        <span>تحديث الإنفوجرافيك</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
