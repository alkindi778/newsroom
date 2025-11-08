@extends('admin.layouts.app')

@section('title', 'عرض المقال: ' . $opinion->title)
@section('page-title', 'عرض المقال')

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    <x-admin.alert-messages />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 line-clamp-2">{{ $opinion->title }}</h1>
            <div class="mt-2 flex items-center space-x-4 space-x-reverse text-sm text-gray-600">
                <span>بواسطة: {{ $opinion->writer->name }}</span>
                @if($opinion->published_at)
                    <span>نُشر في: {{ $opinion->published_at->format('Y/m/d') }}</span>
                @else
                    <span>أُنشئ في: {{ $opinion->created_at->format('Y/m/d') }}</span>
                @endif
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            @can('تعديل مقالات الرأي')
            <a href="{{ route('admin.opinions.edit', $opinion) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                تعديل المقال
            </a>
            @endcan
            
            <a href="{{ route('admin.opinions.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Article Image -->
            @if($opinion->image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $opinion->image) }}" 
                     alt="{{ $opinion->title }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif

            <!-- Article Content -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">محتوى المقال</h3>
                </div>
                
                <div class="p-6">
                    @if($opinion->excerpt)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-2">المقدمة:</h4>
                        <p class="text-gray-700 italic bg-gray-50 p-4 rounded-lg">{{ $opinion->excerpt }}</p>
                    </div>
                    @endif

                    <div class="prose max-w-none">
                        {!! nl2br(e($opinion->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($opinion->tags && (is_array($opinion->tags) ? count($opinion->tags) > 0 : $opinion->tags))
            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">الكلمات الدلالية</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @if(is_array($opinion->tags))
                            @foreach($opinion->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $tag }}
                            </span>
                            @endforeach
                        @else
                            @foreach(explode(',', $opinion->tags) as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ trim($tag) }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Article Status -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">حالة المقال</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">الحالة:</span>
                        @if($opinion->is_published)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">منشور</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">مسودة</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">التمييز:</span>
                        @if($opinion->is_featured)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">مميز</span>
                        @else
                        <span class="text-sm text-gray-500">عادي</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">التعليقات:</span>
                        <span class="text-sm {{ $opinion->allow_comments ? 'text-green-600' : 'text-red-600' }}">
                            {{ $opinion->allow_comments ? 'مسموحة' : 'غير مسموحة' }}
                        </span>
                    </div>

                    @can('نشر مقالات الرأي')
                    <div class="pt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('admin.opinions.toggle-status', $opinion) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 {{ $opinion->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-opacity-50 transition-colors duration-200">
                                @if($opinion->is_published)
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"></path>
                                    </svg>
                                    إلغاء النشر
                                @else
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    نشر المقال
                                @endif
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">الإحصائيات</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">المشاهدات:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($opinion->views ?? 0) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">الإعجابات:</span>
                        <span class="text-sm font-semibold text-red-600">{{ number_format($opinion->likes ?? 0) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">المشاركات:</span>
                        <span class="text-sm font-semibold text-blue-600">{{ number_format($opinion->shares ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Writer Info -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">معلومات الكاتب</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        @if($opinion->writer->image)
                        <img src="{{ asset('storage/' . $opinion->writer->image) }}" 
                             alt="{{ $opinion->writer->name }}" 
                             class="h-12 w-12 rounded-full object-cover">
                        @else
                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        @endif
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $opinion->writer->name }}</h4>
                            @if($opinion->writer->position)
                            <p class="text-sm text-gray-600">{{ $opinion->writer->position }}</p>
                            @endif
                            @if($opinion->writer->specialization)
                            <p class="text-xs text-gray-500">تخصص: {{ $opinion->writer->specialization }}</p>
                            @endif
                        </div>
                    </div>

                    @if($opinion->writer->bio)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-700">{{ Str::limit($opinion->writer->bio, 150) }}</p>
                    </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.writers.show', $opinion->writer) }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                            عرض ملف الكاتب
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SEO Info -->
            @if($opinion->meta_title || $opinion->meta_description || $opinion->keywords)
            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">معلومات SEO</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($opinion->meta_title)
                    <div>
                        <span class="text-sm font-medium text-gray-600">عنوان SEO:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $opinion->meta_title }}</p>
                    </div>
                    @endif

                    @if($opinion->meta_description)
                    <div>
                        <span class="text-sm font-medium text-gray-600">وصف SEO:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $opinion->meta_description }}</p>
                    </div>
                    @endif

                    @if($opinion->keywords)
                    <div>
                        <span class="text-sm font-medium text-gray-600">الكلمات المفتاحية:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $opinion->keywords }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
