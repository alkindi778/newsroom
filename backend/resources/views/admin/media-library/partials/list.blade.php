{{-- List View (Tailwind CSS Table) --}}
<div class="p-6">
    @if($media->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" id="select-all-media">
                        </th>
                        <th class="w-20 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معاينة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم الملف</th>
                        <th class="w-24 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="w-24 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                        <th class="w-32 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المجموعة</th>
                        <th class="w-32 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الرفع</th>
                        <th class="w-24 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($media as $item)
                        <tr data-media-id="{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 media-checkbox" 
                                       value="{{ $item->id }}" id="media-list-{{ $item->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-16 h-16 relative">
                                    @if(str_starts_with($item->mime_type, 'image/'))
                                        <img src="{{ $item->fixed_url ?? $item->getUrl() }}" 
                                             alt="{{ $item->custom_properties['alt'] ?? $item->name }}" 
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @elseif(str_starts_with($item->mime_type, 'video/'))
                                        <div class="w-16 h-16 flex items-center justify-center bg-gray-900 text-white rounded-lg border border-gray-200">
                                            <i class="fas fa-play-circle text-xl"></i>
                                        </div>
                                    @elseif(str_starts_with($item->mime_type, 'audio/'))
                                        <div class="w-16 h-16 flex items-center justify-center bg-blue-600 text-white rounded-lg border border-gray-200">
                                            <i class="fas fa-music text-xl"></i>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 flex items-center justify-center bg-gray-600 text-white rounded-lg border border-gray-200">
                                            <i class="fas fa-file-alt text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="media-details">
                                    <div class="font-medium text-gray-900 hover:text-blue-600 cursor-pointer" onclick="viewMediaDetails({{ $item->id }})">
                                        {{ $item->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $item->file_name }}</div>
                                    @if($item->custom_properties && isset($item->custom_properties['alt']))
                                        <div class="text-xs text-blue-600 mt-1">Alt: {{ $item->custom_properties['alt'] }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    @if(str_starts_with($item->mime_type, 'image/'))
                                        <i class="fas fa-image ml-1"></i>صورة
                                    @elseif(str_starts_with($item->mime_type, 'video/'))
                                        <i class="fas fa-video ml-1"></i>فيديو
                                    @elseif(str_starts_with($item->mime_type, 'audio/'))
                                        <i class="fas fa-music ml-1"></i>صوت
                                    @else
                                        <i class="fas fa-file ml-1"></i>ملف
                                    @endif
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $item->mime_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $item->humanReadableSize ?? '0 KB' }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($item->size ?? 0) }} بايت</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->collection_name)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $item->collection_name }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->created_at->format('Y/m/d') }}</div>
                                <div class="text-xs text-gray-500">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-1">
                                    <!-- View Button -->
                                    <button type="button"
                                            onclick="viewMediaDetails({{ $item->id }})" 
                                            title="عرض التفاصيل"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-800 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <!-- Copy Button -->
                                    <button type="button"
                                            onclick="copyToClipboard('{{ $item->fixed_url ?? $item->getUrl() }}')" 
                                            title="نسخ الرابط"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-800 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>
                                    <!-- External Link Button -->
                                    <a href="{{ $item->fixed_url ?? $item->getUrl() }}" target="_blank"
                                       title="فتح في نافذة جديدة"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-external-link-alt text-sm"></i>
                                    </a>
                                    <!-- Download Button -->
                                    <a href="{{ $item->fixed_url ?? $item->getUrl() }}" 
                                       download="{{ $item->name }}"
                                       title="تحميل الملف"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 hover:bg-purple-200 text-purple-600 hover:text-purple-800 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <button type="button"
                                            onclick="deleteMedia({{ $item->id }})" 
                                            title="حذف الملف"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-800 rounded-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <i class="fas fa-list text-6xl text-gray-300 mb-6"></i>
            <h4 class="text-xl font-semibold text-gray-600 mb-3">لا توجد ملفات</h4>
            <p class="text-gray-500 mb-6">لم يتم العثور على أي ملفات تطابق البحث الحالي</p>
            <button type="button" 
                    onclick="openMediaUpload()"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                رفع أول ملف
            </button>
        </div>
    @endif
</div>

<!-- Simple JavaScript for list view functionality -->
<script>
// List view specific functions
let selectedMediaList = new Set();

function viewMediaDetails(mediaId) {
    // Show media details modal
    console.log('View details for media:', mediaId);
}

function copyToClipboard(url) {
    navigator.clipboard.writeText(url).then(() => {
        // Show success message
        console.log('Copied to clipboard:', url);
        // You can add a toast notification here
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

function deleteMedia(mediaId) {
    if (confirm('هل أنت متأكد من حذف هذا الملف؟')) {
        // Handle delete operation
        console.log('Delete media:', mediaId);
        // You can implement the actual delete logic here
    }
}

// Select all functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-media');
    const mediaCheckboxes = document.querySelectorAll('.media-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            mediaCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
        });
    }

    // Update select all when individual checkboxes change
    mediaCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.media-checkbox:checked');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedBoxes.length === mediaCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < mediaCheckboxes.length;
            }
        });
    });
});
</script>
