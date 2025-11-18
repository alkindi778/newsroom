@extends('admin.layouts.app')

@section('title', 'إدارة كُتاب الرأي')
@section('page-title', 'إدارة كُتاب الرأي')

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    <x-admin.alert-messages />

    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إدارة كُتاب الرأي</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة وتنظيم كُتاب الرأي والمقالات</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.writers.index') }}" class="relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="البحث في الكُتاب..." 
                       class="w-full sm:w-64 pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                       onchange="this.form.submit()">
                <svg class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
            
            @can('إنشاء كُتاب الرأي')
            <a href="{{ route('admin.writers.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                إضافة كاتب جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Writers Table -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكاتب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التخصص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المقالات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإضافة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($writers as $writer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($writer->image_url)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ $writer->image_url }}" alt="{{ $writer->name }}" loading="lazy">
                                    @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $writer->name }}</div>
                                    @if($writer->email)
                                    <div class="text-sm text-gray-500">{{ $writer->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $writer->specialization ?? 'غير محدد' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $writer->opinions_count }} مقال
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($writer->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">نشط</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $writer->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                @can('عرض كُتاب الرأي')
                                <a href="{{ route('admin.writers.show', $writer) }}" class="text-blue-600 hover:text-blue-900" title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan
                                
                                @can('تعديل كُتاب الرأي')
                                <a href="{{ route('admin.writers.edit', $writer) }}" class="text-indigo-600 hover:text-indigo-900" title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                
                                @can('حذف كُتاب الرأي')
                                <form method="POST" action="{{ route('admin.writers.destroy', $writer) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكاتب؟')">
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
                        <td colspan="6" class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد كُتاب</h3>
                            <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة كُتاب الرأي الأوائل.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($writers) && $writers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <x-admin.pagination :paginator="$writers" />
        </div>
        @endif
    </div>
</div>
@endsection
