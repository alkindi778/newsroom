@extends('admin.layouts.app')

@section('title', 'إدارة الأخبار')
@section('page-title', 'إدارة الأخبار')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إدارة الأخبار</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة وتنظيم جميع الأخبار في الموقع</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.articles.index') }}" class="relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="البحث في الأخبار..." 
                       class="w-full sm:w-64 pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                       onchange="this.form.submit()">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </form>
            
            <!-- Pending Articles Button (للمدراء فقط - يظهر فقط إذا كان هناك مقالات معلقة) -->
            @can('عرض المقالات المعلقة')
                @if(isset($pendingCount) && $pendingCount > 0)
                <a href="{{ route('admin.articles.pending') }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                    <i class="fas fa-clock ml-2"></i>
                    في انتظار الموافقة
                    <span class="mr-2 px-2 py-0.5 bg-white text-amber-600 rounded-full text-xs font-bold">{{ $pendingCount }}</span>
                </a>
                @endif
            @endcan
            
            <!-- Add News Button -->
            <a href="{{ route('admin.articles.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة خبر جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.articles.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
                    <option value="">جميع الأخبار</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
                    <option value="">جميع الأقسام</option>
                    @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Author Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الكاتب</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
                    <option value="">جميع الكتاب</option>
                    @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Date Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
            </div>
        </div>
        
        <!-- Preserve search parameter -->
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        
        <div class="flex justify-end mt-4">
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors duration-200">
                مسح الفلاتر
            </a>
        </div>
    </form>

    <!-- News Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Mobile View -->
        <div class="md:hidden">
            @forelse($articles ?? [] as $article)
            <div class="border-b border-gray-200 p-4">
                <div class="flex items-start space-x-4 space-x-reverse">
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
                    
                    <div class="flex-1 min-w-0 mr-3">
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">
                            {{ $article->title }}
                        </h3>
                        
                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-2">
                            <span>{{ $article->category->name ?? 'بدون قسم' }}</span>
                            <span>•</span>
                            <span>{{ $article->user->name ?? 'غير محدد' }}</span>
                            <span>•</span>
                            <span>{{ $article->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            @if($article->approval_status === 'pending_approval')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    في انتظار الموافقة
                                </span>
                            @elseif($article->approval_status === 'rejected')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    مرفوض
                                </span>
                            @elseif($article->is_published && $article->approval_status === 'approved')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    منشور
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    مسودة
                                </span>
                            @endif
                            
                            <div class="flex space-x-2 space-x-reverse">
                                <a href="{{ route('admin.articles.edit', $article) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline-block" onsubmit="return confirm('هل تريد نقل هذا الخبر إلى سلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="نقل إلى سلة المهملات">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أخبار</h3>
                <p class="text-gray-500 mb-4">ابدأ بإضافة أول خبر لك</p>
                <a href="{{ route('admin.articles.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    إضافة خبر جديد
                </a>
            </div>
            @endforelse
            
            <!-- Mobile Pagination -->
            @if(isset($articles) && $articles->hasPages())
            <div class="px-4 py-4 border-t border-gray-200">
                <x-admin.pagination :paginator="$articles" />
            </div>
            @endif
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكاتب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($articles ?? [] as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 ml-4">
                                    @if($article->image)
                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                                    @else
                                    <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 max-w-xs truncate">{{ $article->title }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->category->name ?? 'بدون قسم' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->user->name ?? 'غير محدد' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($article->approval_status === 'pending_approval')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    في انتظار الموافقة
                                </span>
                            @elseif($article->approval_status === 'rejected')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    مرفوض
                                </span>
                            @elseif($article->is_published && $article->approval_status === 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    منشور
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    مسودة
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3 space-x-reverse">
                                <a href="{{ route('admin.articles.show', $article) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.articles.edit', $article) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="تحرير">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline-block" onsubmit="return confirm('هل تريد نقل هذا الخبر إلى سلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="نقل إلى سلة المهملات">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أخبار</h3>
                                <p class="text-gray-500 mb-4">ابدأ بإضافة أول خبر لك</p>
                                <a href="{{ route('admin.articles.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    إضافة خبر جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Desktop Pagination -->
            @if(isset($articles) && $articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <x-admin.pagination :paginator="$articles" />
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
