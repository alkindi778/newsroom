@extends('admin.layouts.app')

@section('title', 'عرض الخبر')
@section('page-title', 'عرض الخبر')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">عرض الخبر</h1>
                <p class="mt-1 text-sm text-gray-600">معاينة تفاصيل الخبر</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <a href="{{ route('admin.articles.edit', $article) }}" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تحرير
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Article Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Status Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $article->status === 'published' ? '✅ منشور' : '📝 مسودة' }}
                    </span>
                    
                    <div class="text-sm text-gray-500">
                        {{ $article->created_at->format('Y/m/d H:i') }}
                    </div>
                </div>
                
                <!-- Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>
                
                <!-- Meta Info -->
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span>{{ $article->category->name ?? 'بدون قسم' }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>{{ $article->user->name ?? 'غير محدد' }}</span>
                    </div>
                    
                    @if($article->published_at)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>نُشر في {{ $article->published_at->format('Y/m/d H:i') }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Slug -->
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-600 mb-1">الرابط المختصر:</div>
                    <code class="text-sm bg-white px-2 py-1 rounded border">{{ $article->slug }}</code>
                </div>
            </div>

            <!-- Featured Image -->
            @if($article->image)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">الصورة الرئيسية</h3>
                <div class="relative">
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         alt="{{ $article->image_alt ?? $article->title }}" 
                         class="w-full h-64 object-cover rounded-lg">
                    @if($article->image_alt)
                    <div class="mt-2 text-sm text-gray-600">
                        <strong>وصف الصورة:</strong> {{ $article->image_alt }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Summary -->
            @if($article->summary)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ملخص الخبر</h3>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <p class="text-gray-700 leading-relaxed">{{ $article->summary }}</p>
                </div>
            </div>
            @endif

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">محتوى الخبر</h3>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed article-content">
                    {!! $article->content !!}
                </div>
            </div>

            <!-- SEO Preview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معاينة محركات البحث</h3>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-blue-600 text-lg hover:underline cursor-pointer">{{ $article->title }}</div>
                    <div class="text-green-600 text-sm">{{ url('/') }}/news/{{ $article->slug }}</div>
                    <div class="text-gray-600 text-sm mt-1">
                        {{ $article->meta_description ?: Str::limit(strip_tags($article->content), 160) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.articles.edit', $article) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        تحرير الخبر
                    </a>
                    
                    @if($article->status === 'published')
                    <a href="#" target="_blank"
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        عرض في الموقع
                    </a>
                    @endif
                    
                    <button onclick="if(confirm('هل أنت متأكد من حذف هذا الخبر؟')) { document.getElementById('delete-form').submit(); }"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف الخبر
                    </button>
                    
                    <form id="delete-form" action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Article Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الخبر</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحالة:</span>
                        <span class="font-medium {{ $article->status === 'published' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $article->status === 'published' ? 'منشور' : 'مسودة' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">القسم:</span>
                        <span class="font-medium">{{ $article->category->name ?? 'بدون قسم' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">الكاتب:</span>
                        <span class="font-medium">{{ $article->user->name ?? 'غير محدد' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="font-medium">{{ $article->created_at->format('Y/m/d') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="font-medium">{{ $article->updated_at->format('Y/m/d') }}</span>
                    </div>
                    
                    @if($article->published_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ النشر:</span>
                        <span class="font-medium">{{ $article->published_at->format('Y/m/d') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">عدد الكلمات:</span>
                        <span class="font-medium">{{ str_word_count(strip_tags($article->content)) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">عدد الأحرف:</span>
                        <span class="font-medium">{{ strlen(strip_tags($article->content)) }}</span>
                    </div>
                </div>
            </div>

            <!-- SEO Information -->
            @if($article->meta_description || $article->keywords)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات SEO</h3>
                <div class="space-y-3 text-sm">
                    @if($article->meta_description)
                    <div>
                        <span class="text-gray-600 block mb-1">وصف الميتا:</span>
                        <p class="text-gray-800 bg-gray-50 p-2 rounded text-xs">{{ $article->meta_description }}</p>
                        <span class="text-xs text-gray-500">{{ strlen($article->meta_description) }}/160 حرف</span>
                    </div>
                    @endif
                    
                    @if($article->keywords)
                    <div>
                        <span class="text-gray-600 block mb-1">الكلمات المفتاحية:</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(',', $article->keywords) as $keyword)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ trim($keyword) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
