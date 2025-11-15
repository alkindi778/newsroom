@extends('admin.layouts.app')

@section('title', 'عرض القسم')
@section('page-title', 'عرض القسم')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">عرض القسم</h1>
                <p class="mt-1 text-sm text-gray-600">معاينة تفاصيل القسم</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <a href="{{ route('admin.categories.edit', $category) }}" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تحرير
                </a>
                <a href="{{ route('admin.categories.index') }}" 
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
            <!-- Category Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Status Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ ($category->is_active ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ($category->is_active ?? 1) ? '✅ نشط' : '❌ غير نشط' }}
                    </span>
                    
                    @if($category->color)
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="text-sm text-gray-600">لون القسم:</span>
                        <div class="w-6 h-6 rounded-full border border-gray-300" style="background-color: {{ $category->color }}"></div>
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $category->color }}</code>
                    </div>
                    @endif
                </div>
                
                <!-- Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
                
                <!-- Description -->
                @if($category->description)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-4">
                    <p class="text-gray-700 leading-relaxed">{{ $category->description }}</p>
                </div>
                @endif
                
                <!-- Slug -->
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-600 mb-1">الرابط المختصر:</div>
                    <code class="text-sm bg-white px-2 py-1 rounded border">{{ $category->slug }}</code>
                </div>
            </div>

            <!-- Category Articles -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">أخبار هذا القسم</h3>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $articles->count() }} خبر
                    </span>
                </div>
                
                @if($articles->count() > 0)
                <div class="space-y-4">
                    @foreach($articles as $article)
                    <div class="flex items-start space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200 border border-gray-100">
                        <div class="flex-shrink-0">
                            @if($article->image)
                            <img class="w-12 h-12 rounded-lg object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                            @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">
                                {{ $article->title }}
                            </h4>
                            
                            <div class="flex items-center space-x-3 space-x-reverse text-xs text-gray-500 mb-2">
                                <span>{{ $article->user->name ?? 'غير محدد' }}</span>
                                <span>•</span>
                                <span>{{ $article->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $article->status === 'published' ? 'منشور' : 'مسودة' }}
                            </span>
                        </div>
                        
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('admin.articles.show', $article) }}" 
                               class="text-blue-600 hover:text-blue-800 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.articles.edit', $article) }}" 
                               class="text-indigo-600 hover:text-indigo-900 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($articles->count() >= 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.articles.index', ['category_id' => $category->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        عرض جميع أخبار هذا القسم →
                    </a>
                </div>
                @endif
                
                @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">لا توجد أخبار</h3>
                    <p class="text-sm text-gray-500 mb-4">لم يتم إنشاء أي أخبار في هذا القسم بعد</p>
                    <a href="{{ route('admin.articles.create') }}?category_id={{ $category->id }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        إضافة خبر جديد
                    </a>
                </div>
                @endif
            </div>

            <!-- SEO Preview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معاينة محركات البحث</h3>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-blue-600 text-lg hover:underline cursor-pointer">
                        {{ $category->meta_title ?: $category->name }}
                    </div>
                    <div class="text-green-600 text-sm">{{ url('/') }}/categories/{{ $category->slug }}</div>
                    <div class="text-gray-600 text-sm mt-1">
                        {{ $category->meta_description ?: $category->description ?: 'قسم ' . $category->name . ' - تصفح جميع الأخبار في هذا القسم' }}
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
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        تحرير القسم
                    </a>
                    
                    <a href="{{ route('admin.articles.create') }}?category_id={{ $category->id }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        إضافة خبر جديد
                    </a>
                    
                    <button onclick="if(confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم حذف جميع الأخبار المرتبطة به أيضاً!')) { document.getElementById('delete-form').submit(); }"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف القسم
                    </button>
                    
                    <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Category Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات القسم</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحالة:</span>
                        <span class="font-medium {{ ($category->is_active ?? 1) ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($category->is_active ?? 1) ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">القسم الأب:</span>
                        <span class="font-medium">{{ $category->parent ? $category->parent->name : 'قسم رئيسي' }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">عدد الأخبار:</span>
                        <span class="font-medium">{{ $articles->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="font-medium">{{ $category->created_at->format('Y/m/d') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="font-medium">{{ $category->updated_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <!-- SEO Information -->
            @if($category->meta_title || $category->meta_description || $category->keywords)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات SEO</h3>
                <div class="space-y-3 text-sm">
                    @if($category->meta_title)
                    <div>
                        <span class="text-gray-600 block mb-1">عنوان الميتا:</span>
                        <p class="text-gray-800 bg-gray-50 p-2 rounded text-xs">{{ $category->meta_title }}</p>
                        <span class="text-xs text-gray-500">{{ strlen($category->meta_title) }}/60 حرف</span>
                    </div>
                    @endif
                    
                    @if($category->meta_description)
                    <div>
                        <span class="text-gray-600 block mb-1">وصف الميتا:</span>
                        <p class="text-gray-800 bg-gray-50 p-2 rounded text-xs">{{ $category->meta_description }}</p>
                        <span class="text-xs text-gray-500">{{ strlen($category->meta_description) }}/160 حرف</span>
                    </div>
                    @endif
                    
                    @if($category->keywords)
                    <div>
                        <span class="text-gray-600 block mb-1">الكلمات المفتاحية:</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(',', $category->keywords) as $keyword)
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
