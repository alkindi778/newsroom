@extends('admin.layouts.app')

@section('title', 'إدارة مقالات الرأي')
@section('page-title', 'إدارة مقالات الرأي')

@section('content')
<div class="space-y-6">
    <!-- Messages are included globally in admin.layouts.app via admin.components.alerts -->

    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إدارة مقالات الرأي</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة وتنظيم مقالات الرأي والكُتاب</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.opinions.index') }}" class="relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="البحث في المقالات..." 
                       class="w-full sm:w-64 pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                       onchange="this.form.submit()">
                <svg class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
            
            @can('إنشاء مقالات الرأي')
            <a href="{{ route('admin.opinions.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                إضافة مقال جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.opinions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search (hidden, handled above) -->
            <input type="hidden" name="search" value="{{ request('search') }}">
            
            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">جميع الحالات</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
            </select>

            <!-- Writer Filter -->
            <select name="writer" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">جميع الكُتاب</option>
                @foreach($writers as $writer)
                <option value="{{ $writer->id }}" {{ request('writer') == $writer->id ? 'selected' : '' }}>
                    {{ $writer->name }}
                </option>
                @endforeach
            </select>

            <!-- Featured Filter -->
            <select name="featured" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">الكل</option>
                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>مميز</option>
                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>غير مميز</option>
            </select>

            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'status', 'writer', 'featured']))
            <a href="{{ route('admin.opinions.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                مسح الفلاتر
            </a>
            @endif
        </form>
    </div>

    <!-- Opinions Table -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المقال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكاتب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التمييز</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإحصائيات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ النشر</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($opinions as $opinion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($opinion->image)
                                <div class="flex-shrink-0 h-12 w-12">
                                    <img class="h-12 w-12 rounded object-cover" src="{{ asset('storage/' . $opinion->image) }}" alt="{{ $opinion->title }}">
                                </div>
                                @endif
                                <div class="{{ $opinion->image ? 'mr-4' : '' }}">
                                    <div class="text-sm font-medium text-gray-900 line-clamp-2">{{ $opinion->title }}</div>
                                    @if($opinion->excerpt)
                                    <div class="text-sm text-gray-500 line-clamp-1 mt-1">{{ $opinion->excerpt }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($opinion->writer->image)
                                <div class="flex-shrink-0 h-8 w-8">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . $opinion->writer->image) }}" alt="{{ $opinion->writer->name }}">
                                </div>
                                @endif
                                <div class="{{ $opinion->writer->image ? 'mr-3' : '' }}">
                                    <div class="text-sm font-medium text-gray-900">{{ $opinion->writer->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($opinion->is_published)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">منشور</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">مسودة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($opinion->is_featured)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">مميز</span>
                            @else
                            <span class="text-sm text-gray-500">عادي</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                @if($opinion->views > 0)
                                <span class="flex items-center text-gray-500">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $opinion->views }}
                                </span>
                                @endif
                                @if($opinion->likes > 0)
                                <span class="flex items-center text-red-500">
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $opinion->likes }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($opinion->published_at)
                                {{ $opinion->published_at->format('Y/m/d') }}
                            @else
                                {{ $opinion->created_at->format('Y/m/d') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                @can('عرض مقالات الرأي')
                                <a href="{{ route('admin.opinions.show', $opinion) }}" class="text-blue-600 hover:text-blue-900" title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan
                                
                                @can('تعديل مقالات الرأي')
                                <a href="{{ route('admin.opinions.edit', $opinion) }}" class="text-indigo-600 hover:text-indigo-900" title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('نشر مقالات الرأي')
                                <form method="POST" action="{{ route('admin.opinions.toggle-status', $opinion) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $opinion->is_published ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $opinion->is_published ? 'إلغاء النشر' : 'نشر' }}">
                                        @if($opinion->is_published)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"></path>
                                        </svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                @endcan
                                
                                @can('حذف مقالات الرأي')
                                <form method="POST" action="{{ route('admin.opinions.destroy', $opinion) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المقال؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد مقالات</h3>
                            <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة مقالات الرأي الأولى.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($opinions) && $opinions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <x-admin.pagination :paginator="$opinions" />
        </div>
        @endif
    </div>
</div>
@endsection
