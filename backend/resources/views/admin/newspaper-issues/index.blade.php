@extends('admin.layouts.app')

@section('title', 'إصدارات الصحف')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إصدارات الصحف</h1>
        <a href="{{ route('admin.newspaper-issues.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة إصدار جديد</span>
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.newspaper-issues.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث باسم الصحيفة أو الوصف" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">الحالة</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                </select>
            </div>
            <div>
                <select name="featured" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">التمييز</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>مميز</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>غير مميز</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">الترتيب</option>
                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>الأحدث أولاً</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم أولاً</option>
                    <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>الأكثر مشاهدة</option>
                    <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>الأكثر تحميلاً</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصحيفة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم العدد</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاهدات</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التحميلات</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مميز</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($issues as $issue)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-16 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                                        @if($issue->cover_image)
                                            <img src="{{ asset('storage/'.$issue->cover_image) }}" alt="{{ $issue->newspaper_name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-newspaper text-gray-400 text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $issue->newspaper_name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($issue->description, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                العدد {{ $issue->issue_number }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $issue->publication_date ? $issue->publication_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                <i class="fas fa-eye text-gray-400 ml-1"></i>
                                {{ number_format($issue->views) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                <i class="fas fa-download text-gray-400 ml-1"></i>
                                {{ number_format($issue->downloads) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($issue->is_published)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">منشور</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">مسودة</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($issue->is_featured)
                                    <i class="fas fa-star text-yellow-500 text-lg"></i>
                                @else
                                    <i class="far fa-star text-gray-400 text-lg"></i>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.newspaper-issues.show', $issue->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-gray-600 hover:bg-gray-700 text-white rounded transition-colors"
                                       title="عرض الإصدار">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    <a href="{{ route('admin.newspaper-issues.edit', $issue->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" title="تعديل">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.newspaper-issues.toggle-featured', $issue->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-purple-600 hover:bg-purple-700 text-white rounded transition-colors" title="تبديل التمييز">
                                            <i class="fas fa-star text-xs"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.newspaper-issues.destroy', $issue->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإصدار؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" title="حذف">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <i class="fas fa-newspaper text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-lg">لا توجد إصدارات حالياً</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $issues->links() }}
        </div>
    </div>
</div>
@endsection
