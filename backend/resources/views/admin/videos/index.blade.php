@extends('admin.layouts.app')

@section('title', 'إدارة الفيديوهات')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إدارة الفيديوهات</h1>
        <a href="{{ route('admin.videos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة فيديو جديد</span>
        </a>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Section Title Setting -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 mb-6 border border-blue-100">
        <form method="POST" action="{{ route('admin.videos.update-section-title') }}" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label for="section_title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading text-blue-600 ml-1"></i>
                    عنوان قسم الفيديو في الموقع
                </label>
                <input type="text" 
                       name="section_title" 
                       id="section_title" 
                       value="{{ $sectionTitle ?? 'فيديو العربية' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="فيديو العربية"
                       required>
                <p class="mt-1 text-xs text-gray-600">
                    <i class="fas fa-info-circle ml-1"></i>
                    هذا العنوان سيظهر في الصفحة الرئيسية وصفحة الفيديوهات في الموقع
                </p>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors whitespace-nowrap">
                <i class="fas fa-save"></i>
                <span>حفظ العنوان</span>
            </button>
        </form>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.videos.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="البحث..." value="{{ request('search') }}">
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
                    <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>مميز</option>
                    <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>غير مميز</option>
                </select>
            </div>
            <div>
                <select name="video_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">نوع الفيديو</option>
                    <option value="youtube" {{ request('video_type') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="vimeo" {{ request('video_type') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                    <option value="local" {{ request('video_type') == 'local' ? 'selected' : '' }}>محلي</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulkActionForm" method="POST" action="{{ route('admin.videos.bulk-action') }}">
        @csrf
        <input type="hidden" name="action" id="bulkAction">
        
        <!-- Bulk Actions Bar -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6 hidden" id="bulkActionsBar">
            <div class="flex items-center gap-3">
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm" onclick="submitBulkAction('publish')">
                    <i class="fas fa-check"></i>
                    <span>نشر المحدد</span>
                </button>
                <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm" onclick="submitBulkAction('unpublish')">
                    <i class="fas fa-times"></i>
                    <span>إلغاء النشر</span>
                </button>
                <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors text-sm" onclick="submitBulkAction('delete')">
                    <i class="fas fa-trash"></i>
                    <span>حذف المحدد</span>
                </button>
                <span class="mr-auto text-gray-700 font-medium">
                    <span id="selectedCount">0</span> فيديو محدد
                </span>
            </div>
        </div>

        <!-- Videos Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">صورة مصغرة</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاهدات</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مميز</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ النشر</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($videos as $video)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="ids[]" value="{{ $video->id }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 video-checkbox">
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" 
                                             class="w-16 h-10 object-cover rounded-lg shadow-sm">
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('admin.videos.show', $video->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                            {{ Str::limit($video->title, 50) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800">{{ ucfirst($video->video_type) }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $video->duration }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <i class="fas fa-eye text-gray-400 ml-1"></i>
                                        {{ number_format($video->views) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($video->is_published)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">منشور</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">مسودة</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        @if($video->is_featured)
                                            <i class="fas fa-star text-yellow-500 text-lg"></i>
                                        @else
                                            <i class="far fa-star text-gray-400 text-lg"></i>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $video->published_at ? $video->published_at->format('Y-m-d') : '-' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('admin.videos.edit', $video->id) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                               title="تعديل">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            
                                            @if($video->is_published)
                                                <form action="{{ route('admin.videos.unpublish', $video->id) }}" 
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-yellow-600 hover:bg-yellow-700 text-white rounded transition-colors" title="إلغاء النشر">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.videos.publish', $video->id) }}" 
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-green-600 hover:bg-green-700 text-white rounded transition-colors" title="نشر">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('admin.videos.toggle-featured', $video->id) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-purple-600 hover:bg-purple-700 text-white rounded transition-colors" title="تبديل التمييز">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.videos.destroy', $video->id) }}" 
                                                  method="POST" class="inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الفيديو؟')">
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
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <i class="fas fa-video text-5xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-lg">لا توجد فيديوهات</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Bulk selection
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.video-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateBulkActionsBar();
});

document.querySelectorAll('.video-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionsBar);
});

function updateBulkActionsBar() {
    const checked = document.querySelectorAll('.video-checkbox:checked').length;
    const bar = document.getElementById('bulkActionsBar');
    const count = document.getElementById('selectedCount');
    
    if (checked > 0) {
        bar.classList.remove('hidden');
        count.textContent = checked;
    } else {
        bar.classList.add('hidden');
    }
}

function submitBulkAction(action) {
    if (confirm('هل أنت متأكد من تنفيذ هذا الإجراء؟')) {
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkActionForm').submit();
    }
}
</script>
@endpush
@endsection
