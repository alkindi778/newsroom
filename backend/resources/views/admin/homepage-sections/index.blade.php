@extends('admin.layouts.app')

@section('title', 'إدارة أقسام الصفحة الرئيسية')
@section('page-title', 'إدارة أقسام الصفحة الرئيسية')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إدارة أقسام الصفحة الرئيسية</h1>
            <p class="mt-1 text-sm text-gray-600">تحكم كامل في محتوى وترتيب أقسام الصفحة الرئيسية</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Add Section Button -->
            @can('create_homepage_sections')
            <a href="{{ route('admin.homepage-sections.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة قسم جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Sections -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600">إجمالي الأقسام</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $sections->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Active Sections -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600">الأقسام النشطة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $sections->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Inactive Sections -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600">الأقسام المعطلة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $sections->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Section Types -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600">أنواع مختلفة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $sections->unique('type')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="bg-blue-50 border-r-4 border-blue-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <p class="text-sm text-blue-700">
                    يمكنك سحب وإفلات الأقسام لإعادة ترتيبها. التغييرات ستظهر مباشرة في الصفحة الرئيسية.
                </p>
            </div>
        </div>
    </div>

    <!-- Sections Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الترتيب
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            اسم القسم
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            القالب
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            القسم المرتبط
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            عدد العناصر
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الترتيب
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="sections-tbody">
                    @forelse($sections as $section)
                    <tr class="hover:bg-gray-50 transition-colors duration-200" data-section-id="{{ $section->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center cursor-move">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <span class="mr-2">{{ $section->order }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $section->name }}</div>
                                    @if($section->title)
                                    <div class="text-sm text-gray-500">{{ $section->title }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($section->type == 'slider') bg-purple-100 text-purple-800
                                @elseif($section->type == 'latest_news') bg-blue-100 text-blue-800
                                @elseif($section->type == 'trending') bg-red-100 text-red-800
                                @elseif($section->type == 'opinions') bg-yellow-100 text-yellow-800
                                @elseif($section->type == 'videos') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $section->type_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if(isset($section->settings['template']))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($section->settings['template'] == 'default') bg-gray-100 text-gray-800
                                    @elseif($section->settings['template'] == 'grid') bg-blue-100 text-blue-800
                                    @elseif($section->settings['template'] == 'featured') bg-purple-100 text-purple-800
                                    @elseif($section->settings['template'] == 'list') bg-green-100 text-green-800
                                    @elseif($section->settings['template'] == 'magazine') bg-orange-100 text-orange-800
                                    @endif">
                                    @if($section->settings['template'] == 'default') 
                                        <i class="fas fa-cog"></i> افتراضي
                                    @elseif($section->settings['template'] == 'grid') 
                                        <i class="fas fa-th"></i> شبكة
                                    @elseif($section->settings['template'] == 'featured') 
                                        <i class="fas fa-star"></i> مميز
                                    @elseif($section->settings['template'] == 'list') 
                                        <i class="fas fa-list"></i> قائمة
                                    @elseif($section->settings['template'] == 'magazine') 
                                        <i class="fas fa-newspaper"></i> مجلة
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($section->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $section->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                {{ $section->items_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <input type="number" 
                                   value="{{ $section->order }}" 
                                   class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm order-input"
                                   data-section-id="{{ $section->id }}"
                                   min="0">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.homepage-sections.toggle-status', $section) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $section->is_active ? 'bg-blue-600' : 'bg-gray-200' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $section->is_active ? 'translate-x-0' : 'translate-x-5' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                            <div class="flex items-center gap-2">
                                @can('edit_homepage_sections')
                                <a href="{{ route('admin.homepage-sections.edit', $section) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                                @can('delete_homepage_sections')
                                <form method="POST" action="{{ route('admin.homepage-sections.destroy', $section) }}" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد أقسام</h3>
                            <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة قسم جديد للصفحة الرئيسية</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.homepage-sections.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    إضافة قسم جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/admin/js/sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable for drag & drop
    const tbody = document.getElementById('sections-tbody');
    if (tbody && tbody.children.length > 0) {
        new Sortable(tbody, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function(evt) {
                updateOrder();
            }
        });
    }

    // Update order when input changes
    document.querySelectorAll('.order-input').forEach(input => {
        input.addEventListener('change', updateOrder);
    });

    function updateOrder() {
        const rows = document.querySelectorAll('#sections-tbody tr[data-section-id]');
        const sections = [];
        
        rows.forEach((row, index) => {
            const id = row.getAttribute('data-section-id');
            const orderInput = row.querySelector('.order-input');
            const newOrder = index + 1; // الترتيب الجديد بناءً على الموقع الحالي
            
            sections.push({ 
                id: parseInt(id), 
                order: newOrder 
            });
            
            // Update order input to reflect new position
            if (orderInput) {
                orderInput.value = newOrder;
            }
        });

        // Send AJAX request
        fetch('{{ route("admin.homepage-sections.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ sections: sections })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('تم تحديث الترتيب بنجاح', 'success');
                
                // Reload page after short delay to show updated order
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'حدث خطأ أثناء تحديث الترتيب', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ أثناء تحديث الترتيب', 'error');
        });
    }

    function showNotification(message, type) {
        // Simple notification (you can use a toast library)
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endpush
@endsection
