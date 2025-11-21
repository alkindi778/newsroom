@extends('admin.layouts.app')

@section('title', 'إدارة الإنفوجرافيك')
@section('page-title', 'إدارة الإنفوجرافيك')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إدارة الإنفوجرافيك</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة وتنظيم جميع الإنفوجرافيكات</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.infographics.index') }}" class="relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="البحث في الإنفوجرافيكات..." 
                       class="w-full sm:w-64 pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                       onchange="this.form.submit()">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </form>
            
            <!-- Add Infographic Button -->
            @can('create_infographics')
            <a href="{{ route('admin.infographics.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة إنفوجرافيك جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.infographics.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
                    <option value="">جميع الإنفوجرافيكات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                </select>
            </div>
            
            <!-- Featured Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المميز</label>
                <select name="featured" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>المميزة فقط</option>
                    <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>غير المميزة</option>
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
            <a href="{{ route('admin.infographics.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors duration-200">
                مسح الفلاتر
            </a>
        </div>
    </form>

    <!-- Infographics Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Mobile View -->
        <div class="md:hidden">
            @forelse($infographics ?? [] as $infographic)
            <div class="border-b border-gray-200 p-4">
                <div class="flex items-start space-x-4 space-x-reverse">
                    <div class="flex-shrink-0">
                        @if($infographic->image)
                        <img class="w-16 h-16 rounded-lg object-cover" src="{{ asset('storage/' . $infographic->image) }}" alt="{{ $infographic->title }}">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-bar text-gray-400 text-xl"></i>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0 mr-3">
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">
                            {{ $infographic->title }}
                        </h3>
                        
                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-2">
                            <span>{{ $infographic->category->name ?? 'بدون قسم' }}</span>
                            <span>•</span>
                            <span>{{ $infographic->created_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span><i class="fas fa-eye"></i> {{ number_format($infographic->views) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex gap-1">
                                @if($infographic->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star ml-1"></i> مميز
                                </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $infographic->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $infographic->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </div>
                            
                            <div class="flex space-x-2 space-x-reverse">
                                @can('edit_infographics')
                                <a href="{{ route('admin.infographics.edit', $infographic) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('delete_infographics')
                                <form action="{{ route('admin.infographics.destroy', $infographic) }}" method="POST" class="inline-block" onsubmit="return confirm('هل تريد نقل هذا الإنفوجرافيك إلى سلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="نقل إلى سلة المهملات">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إنفوجرافيكات</h3>
                <p class="text-gray-500 mb-4">ابدأ بإضافة أول إنفوجرافيك لك</p>
                @can('create_infographics')
                <a href="{{ route('admin.infographics.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    إضافة إنفوجرافيك جديد
                </a>
                @endcan
            </div>
            @endforelse
            
            <!-- Mobile Pagination -->
            @if(isset($infographics) && $infographics->hasPages())
            <div class="px-4 py-4 border-t border-gray-200">
                {{ $infographics->links() }}
            </div>
            @endif
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصورة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاهدات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($infographics ?? [] as $infographic)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($infographic->image)
                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ asset('storage/' . $infographic->image) }}" alt="{{ $infographic->title }}">
                                @else
                                <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-gray-400"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate">{{ $infographic->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $infographic->category->name ?? 'بدون قسم' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-1">
                                @if($infographic->is_featured)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star ml-1"></i> مميز
                                </span>
                                @endif
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $infographic->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $infographic->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <i class="fas fa-eye text-gray-400 ml-1"></i>
                            {{ number_format($infographic->views) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $infographic->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3 space-x-reverse">
                                @can('edit_infographics')
                                <a href="{{ route('admin.infographics.edit', $infographic) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="تحرير">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('delete_infographics')
                                <form action="{{ route('admin.infographics.destroy', $infographic) }}" method="POST" class="inline-block" onsubmit="return confirm('هل تريد نقل هذا الإنفوجرافيك إلى سلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="نقل إلى سلة المهملات">
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
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إنفوجرافيكات</h3>
                                <p class="text-gray-500 mb-4">ابدأ بإضافة أول إنفوجرافيك لك</p>
                                @can('create_infographics')
                                <a href="{{ route('admin.infographics.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    إضافة إنفوجرافيك جديد
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Desktop Pagination -->
            @if(isset($infographics) && $infographics->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $infographics->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection