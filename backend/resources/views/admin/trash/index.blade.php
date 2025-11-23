@extends('admin.layouts.app')

@section('title', 'سلة المهملات')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">سلة المهملات</h1>
            <p class="text-sm sm:text-base text-gray-600">إدارة المحتوى المحذوف - يمكن استعادته أو حذفه نهائياً</p>
        </div>
        
        @if((isset($articles) && $articles->count() > 0) || (isset($videos) && $videos->count() > 0) || (isset($opinions) && $opinions->count() > 0) || (isset($infographics) && $infographics->count() > 0))
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <button onclick="bulkAction('restore')" 
                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6L9 4"></path>
                </svg>
                استعادة المحدد
            </button>
            <button onclick="bulkAction('force_delete')" 
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-md">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                حذف نهائي للمحدد
            </button>
            <button onclick="emptyTrash()" 
                    class="px-4 py-2 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg border border-red-700">
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                إفراغ السلة بالكامل
            </button>
        </div>
        @endif
    </div>

    <!-- Tabs -->
    <div class="mb-6 overflow-x-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1 inline-flex gap-1 min-w-full lg:min-w-0">
            <a href="{{ route('admin.trash.index', ['type' => 'articles']) }}" 
               class="group relative flex items-center gap-2 px-3 py-2 sm:px-6 sm:py-3 rounded-md text-xs sm:text-sm font-medium transition-all duration-200 whitespace-nowrap @if($type === 'articles') bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif">
                <i class="fas fa-newspaper text-sm sm:text-lg transition-transform duration-200 @if($type === 'articles') @else group-hover:scale-110 @endif"></i>
                <span class="hidden sm:inline">الأخبار</span>
                <span class="inline sm:hidden">أخبار</span>
                @if(isset($articles))
                    <span class="@if($type === 'articles') bg-white/20 text-white border border-white/30 @else bg-gray-100 text-gray-700 border border-gray-200 @endif inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-xs font-bold transition-all duration-200">
                        {{ $articles->total() }}
                    </span>
                @endif
                @if($type === 'articles')
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-blue-600 rounded-full"></div>
                @endif
            </a>
            
            <a href="{{ route('admin.trash.index', ['type' => 'videos']) }}" 
               class="group relative flex items-center gap-2 px-3 py-2 sm:px-6 sm:py-3 rounded-md text-xs sm:text-sm font-medium transition-all duration-200 whitespace-nowrap @if($type === 'videos') bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-md @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif">
                <i class="fas fa-video text-sm sm:text-lg transition-transform duration-200 @if($type === 'videos') @else group-hover:scale-110 @endif"></i>
                <span class="hidden sm:inline">الفيديوهات</span>
                <span class="inline sm:hidden">فيديو</span>
                @if(isset($videos))
                    <span class="@if($type === 'videos') bg-white/20 text-white border border-white/30 @else bg-gray-100 text-gray-700 border border-gray-200 @endif inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-xs font-bold transition-all duration-200">
                        {{ $videos->total() }}
                    </span>
                @endif
                @if($type === 'videos')
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-purple-600 rounded-full"></div>
                @endif
            </a>
            
            <a href="{{ route('admin.trash.index', ['type' => 'opinions']) }}" 
               class="group relative flex items-center gap-2 px-3 py-2 sm:px-6 sm:py-3 rounded-md text-xs sm:text-sm font-medium transition-all duration-200 whitespace-nowrap @if($type === 'opinions') bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-md @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif">
                <i class="fas fa-pen-fancy text-sm sm:text-lg transition-transform duration-200 @if($type === 'opinions') @else group-hover:scale-110 @endif"></i>
                <span class="hidden sm:inline">مقالات الرأي</span>
                <span class="inline sm:hidden">مقالات</span>
                @if(isset($opinions))
                    <span class="@if($type === 'opinions') bg-white/20 text-white border border-white/30 @else bg-gray-100 text-gray-700 border border-gray-200 @endif inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-xs font-bold transition-all duration-200">
                        {{ $opinions->total() }}
                    </span>
                @endif
                @if($type === 'opinions')
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-amber-600 rounded-full"></div>
                @endif
            </a>
            
            <a href="{{ route('admin.trash.index', ['type' => 'infographics']) }}" 
               class="group relative flex items-center gap-2 px-3 py-2 sm:px-6 sm:py-3 rounded-md text-xs sm:text-sm font-medium transition-all duration-200 whitespace-nowrap @if($type === 'infographics') bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif">
                <i class="fas fa-chart-bar text-sm sm:text-lg transition-transform duration-200 @if($type === 'infographics') @else group-hover:scale-110 @endif"></i>
                <span class="hidden sm:inline">الإنفوجرافيك</span>
                <span class="inline sm:hidden">إنفو</span>
                @if(isset($infographics))
                    <span class="@if($type === 'infographics') bg-white/20 text-white border border-white/30 @else bg-gray-100 text-gray-700 border border-gray-200 @endif inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-xs font-bold transition-all duration-200">
                        {{ $infographics->total() }}
                    </span>
                @endif
                @if($type === 'infographics')
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-green-600 rounded-full"></div>
                @endif
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.trash.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="البحث في العنوان..."
                       onchange="this.form.submit()">
            </div>

            <!-- Category Filter (Articles only) -->
            @if($type === 'articles' && isset($categories))
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                <select id="category_id" 
                        name="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="this.form.submit()">
                    <option value="">جميع الأقسام</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                <input type="date" 
                       id="date_from" 
                       name="date_from" 
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       onchange="this.form.submit()">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                <input type="date" 
                       id="date_to" 
                       name="date_to" 
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       onchange="this.form.submit()">
            </div>
        </form>

        @if(request()->hasAny(['search', 'category_id', 'date_from', 'date_to']))
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.trash.index') }}" 
               class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-full text-sm text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                مسح الفلاتر
            </a>
        </div>
        @endif
    </div>

    <!-- Content List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($type === 'articles' && isset($articles) && $articles->count() > 0)
        <!-- Bulk Actions Form -->
        <form id="bulk-form" method="POST">
            @csrf
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العنوان
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                القسم
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الكاتب
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تاريخ الحذف
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($articles as $article)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="article_ids[]" value="{{ $article->id }}" class="article-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($article->image)
                                    <img class="h-12 w-12 rounded-lg object-cover ml-4 flex-shrink-0" 
                                         src="{{ asset('storage/' . $article->image) }}" 
                                         alt="{{ $article->title }}">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center ml-4 flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $article->title }}</p>
                                        @if($article->subtitle)
                                        <p class="text-sm text-gray-500 truncate">{{ $article->subtitle }}</p>
                                        @endif
                                        @if($article->source)
                                        <p class="text-xs text-blue-600">{{ $article->source }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($article->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $article->category->name }}
                                </span>
                                @else
                                <span class="text-gray-400 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $article->user->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $article->deleted_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.trash.restore', $article->id) }}" 
                                       onclick="return confirm('هل تريد استعادة هذا الخبر؟')"
                                       class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        استعادة
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.trash.force-delete', $article->id) }}" 
                                       onclick="return confirm('تحذير: سيتم حذف الخبر نهائياً ولن يمكن استعادته. هل تريد المتابعة؟')"
                                       class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        حذف نهائي
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Desktop Pagination -->
                @if($articles->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $articles->links() }}
                </div>
                @endif
            </div>
            
            <!-- Mobile Card View -->
            <div class="lg:hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all-mobile" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                        <span class="text-sm font-medium text-gray-700">تحديد الكل</span>
                    </label>
                </div>
                
                @foreach($articles as $article)
                <div class="border-b border-gray-200 p-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <!-- Checkbox -->
                        <input type="checkbox" name="article_ids[]" value="{{ $article->id }}" class="article-checkbox-mobile rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mt-1">
                        
                        <!-- Image -->
                        <div class="flex-shrink-0">
                            @if($article->image)
                            <img class="h-16 w-16 rounded-lg object-cover" 
                                 src="{{ asset('storage/' . $article->image) }}" 
                                 alt="{{ $article->title }}">
                            @else
                            <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0 mr-3">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $article->title }}</h3>
                                    @if($article->subtitle)
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $article->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Meta Info -->
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                @if($article->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 font-medium">
                                    {{ $article->category->name }}
                                </span>
                                @endif
                                @if($article->source)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ $article->source }}
                                </span>
                                @endif
                                <span class="text-gray-500">
                                    بواسطة: {{ $article->user->name ?? 'غير محدد' }}
                                </span>
                                <span class="text-gray-500">
                                    حُذف: {{ $article->deleted_at->format('M d, H:i') }}
                                </span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="mt-3 flex space-x-2 space-x-reverse">
                                <a href="{{ route('admin.trash.restore', $article->id) }}" 
                                   onclick="return confirm('هل تريد استعادة هذا الخبر؟')"
                                   class="inline-flex items-center px-3 py-1 border border-green-300 rounded-md text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6L9 4"></path>
                                    </svg>
                                    استعادة
                                </a>
                                <a href="{{ route('admin.trash.force-delete', $article->id) }}" 
                                   onclick="return confirm('تحذير: سيتم حذف الخبر نهائياً ولن يمكن استعادته. هل تريد المتابعة؟')"
                                   class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    حذف نهائي
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Mobile Pagination -->
                @if($articles->hasPages())
                <div class="px-4 py-4 border-t border-gray-200">
                    {{ $articles->links() }}
                </div>
                @endif
            </div>
        </form>

        @elseif($type === 'videos' && isset($videos) && $videos->count() > 0)
        <!-- Videos List -->
        <form id="bulk-form" method="POST">
            @csrf
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العنوان
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                النوع
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المدة
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تاريخ الحذف
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($videos as $video)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="video_ids[]" value="{{ $video->id }}" class="video-checkbox rounded border-gray-300 text-blue-600 shadow-sm">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img class="h-12 w-16 rounded-lg object-cover ml-4 flex-shrink-0" 
                                         src="{{ $video->thumbnail_url }}" 
                                         alt="{{ $video->title }}">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $video->title }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst($video->video_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $video->duration ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $video->deleted_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.trash.video.restore', $video->id) }}" 
                                       onclick="return confirm('هل تريد استعادة هذا الفيديو؟')"
                                       class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        استعادة
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.trash.video.force-delete', $video->id) }}" 
                                       onclick="return confirm('تحذير: سيتم حذف الفيديو نهائياً ولن يمكن استعادته. هل تريد المتابعة؟')"
                                       class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        حذف نهائي
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($videos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $videos->appends(['type' => 'videos'])->links() }}
                </div>
                @endif
            </div>
        </form>

        @elseif($type === 'opinions' && isset($opinions) && $opinions->count() > 0)
        <!-- Opinions List -->
        <div class="divide-y divide-gray-200">
            @foreach($opinions as $opinion)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-4">
                    <!-- صورة -->
                    @if($opinion->image)
                    <img class="h-20 w-20 rounded-lg object-cover flex-shrink-0" 
                         src="{{ asset('storage/' . $opinion->image) }}" 
                         alt="{{ $opinion->title }}">
                    @elseif($opinion->writer && $opinion->writer->image)
                    <img class="h-20 w-20 rounded-lg object-cover flex-shrink-0" 
                         src="{{ asset('storage/' . $opinion->writer->image) }}" 
                         alt="{{ $opinion->writer->name }}">
                    @else
                    <div class="h-20 w-20 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-pen-fancy text-2xl text-amber-600"></i>
                    </div>
                    @endif
                    
                    <!-- المحتوى -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 mb-1">{{ $opinion->title }}</h3>
                        @if($opinion->writer)
                        <p class="text-xs text-amber-600 mb-2">
                            <i class="fas fa-user-edit ml-1"></i>
                            {{ $opinion->writer->name }}
                        </p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span>
                                <i class="far fa-clock ml-1"></i>
                                حُذف: {{ $opinion->deleted_at->format('Y/m/d H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- الإجراءات -->
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <a href="{{ route('admin.trash.opinion.restore', $opinion->id) }}" 
                           onclick="return confirm('هل تريد استعادة هذا المقال؟')"
                           class="inline-flex items-center justify-center px-3 py-1.5 border border-green-300 rounded-md text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 whitespace-nowrap">
                            <i class="fas fa-undo ml-1"></i>
                            استعادة
                        </a>
                        <a href="{{ route('admin.trash.opinion.force-delete', $opinion->id) }}" 
                           onclick="return confirm('تحذير: سيتم حذف المقال نهائياً ولن يمكن استعادته. هل تريد المتابعة؟')"
                           class="inline-flex items-center justify-center px-3 py-1.5 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 whitespace-nowrap">
                            <i class="fas fa-trash-alt ml-1"></i>
                            حذف نهائي
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Pagination -->
            @if($opinions->hasPages())
            <div class="px-4 py-4 border-t border-gray-200">
                {{ $opinions->links() }}
            </div>
            @endif
        </div>

        @elseif($type === 'opinions')
        <!-- Empty State - Opinions -->
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4">
                <i class="fas fa-pen-fancy text-3xl text-amber-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مقالات رأي محذوفة</h3>
            <p class="text-gray-500 mb-4">سلة المهملات فارغة من مقالات الرأي</p>
            <a href="{{ route('admin.opinions.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                العودة لمقالات الرأي
            </a>
        </div>

        @elseif($type === 'infographics' && isset($infographics) && $infographics->count() > 0)
        <!-- Infographics List -->
        <div class="divide-y divide-gray-200">
            @foreach($infographics as $infographic)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 space-x-reverse flex-1">
                        <input type="checkbox" name="ids[]" value="{{ $infographic->id }}" 
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        
                        @if($infographic->image)
                        <img src="{{ asset('storage/' . $infographic->image) }}" 
                             alt="{{ $infographic->title }}"
                             class="w-16 h-16 object-cover rounded-lg">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-bar text-gray-400 text-xl"></i>
                        </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-base font-medium text-gray-900">{{ $infographic->title }}</h3>
                            @if($infographic->category)
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-folder text-gray-400"></i>
                                {{ $infographic->category->name }}
                            </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-trash-alt"></i>
                                حُذف في: {{ $infographic->deleted_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <form method="POST" action="{{ route('admin.trash.restore', $infographic->id) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                <i class="fas fa-undo ml-1"></i>
                                استعادة
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.trash.force-delete', $infographic->id) }}" 
                              class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف النهائي؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                <i class="fas fa-times ml-1"></i>
                                حذف نهائي
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if($infographics->hasPages())
            <div class="px-4 py-4 border-t border-gray-200">
                {{ $infographics->links() }}
            </div>
            @endif
        </div>

        @elseif($type === 'infographics')
        <!-- Empty State - Infographics -->
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                <i class="fas fa-chart-bar text-3xl text-green-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إنفوجرافيكات محذوفة</h3>
            <p class="text-gray-500 mb-4">سلة المهملات فارغة من الإنفوجرافيكات</p>
            <a href="{{ route('admin.infographics.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                العودة للإنفوجرافيك
            </a>
        </div>

        @else
        <!-- Empty State - General -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">سلة المهملات فارغة</h3>
            <p class="mt-2 text-gray-500">
                @if($type === 'videos')
                    لا توجد فيديوهات محذوفة حالياً
                @elseif($type === 'opinions')
                    لا توجد مقالات رأي محذوفة حالياً
                @elseif($type === 'infographics')
                    لا توجد إنفوجرافيكات محذوفة حالياً
                @else
                    لا توجد أخبار محذوفة حالياً
                @endif
            </p>
            <div class="mt-6">
                @if($type === 'videos')
                    <a href="{{ route('admin.videos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        العودة للفيديوهات
                    </a>
                @elseif($type === 'opinions')
                    <a href="{{ route('admin.opinions.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                        العودة لمقالات الرأي
                    </a>
                @elseif($type === 'infographics')
                    <a href="{{ route('admin.infographics.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        العودة للإنفوجرافيك
                    </a>
                @else
                    <a href="{{ route('admin.articles.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        العودة للأخبار
                    </a>
                @endif
            </div>
        </div>

        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
const currentType = '{{ $type }}';

// Select All functionality for desktop
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxClass = currentType === 'videos' ? '.video-checkbox' : '.article-checkbox';
    const checkboxes = document.querySelectorAll(checkboxClass);
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Select All functionality for mobile
document.getElementById('select-all-mobile')?.addEventListener('change', function() {
    const checkboxClass = currentType === 'videos' ? '.video-checkbox-mobile' : '.article-checkbox-mobile';
    const checkboxes = document.querySelectorAll(checkboxClass);
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk Actions
function bulkAction(action) {
    let checkboxClass;
    let itemType;
    
    switch(currentType) {
        case 'videos':
            checkboxClass = '.video-checkbox';
            itemType = 'فيديو';
            break;
        case 'opinions':
            checkboxClass = '.opinion-checkbox';
            itemType = 'مقال';
            break;
        default:
            checkboxClass = '.article-checkbox';
            itemType = 'خبر';
    }
    
    const checkedBoxes = document.querySelectorAll(checkboxClass + ':checked');
    
    if (checkedBoxes.length === 0) {
        alert(`يرجى اختيار ${itemType} واحد على الأقل`);
        return;
    }

    let confirmMessage = '';
    let actionUrl = '';
    
    if (action === 'restore') {
        confirmMessage = `هل تريد استعادة ${checkedBoxes.length} ${itemType}؟`;
        actionUrl = '{{ route("admin.trash.bulk-restore") }}';
    } else if (action === 'force_delete') {
        confirmMessage = `تحذير: سيتم حذف ${checkedBoxes.length} ${itemType} نهائياً ولن يمكن استعادتها. هل تريد المتابعة؟`;
        actionUrl = '{{ route("admin.trash.bulk-force-delete") }}';
    }

    if (confirm(confirmMessage)) {
        const form = document.getElementById('bulk-form');
        form.action = actionUrl;
        form.submit();
    }
}

// Empty Trash
function emptyTrash() {
    let itemType;
    
    switch(currentType) {
        case 'videos':
            itemType = 'الفيديوهات';
            break;
        case 'opinions':
            itemType = 'مقالات الرأي';
            break;
        default:
            itemType = 'الأخبار';
    }
    
    if (confirm(`⚠️ تحذير خطير!\n\nسيتم حذف جميع ${itemType} في سلة المهملات نهائياً ولن يمكن استعادتها أبداً.\n\nهل أنت متأكد من أنك تريد إفراغ سلة المهملات بالكامل؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.trash.empty") }}?type=' + currentType;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = currentType;
        
        form.appendChild(csrfToken);
        form.appendChild(typeInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
