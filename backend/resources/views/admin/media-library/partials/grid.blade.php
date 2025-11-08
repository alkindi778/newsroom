{{-- Grid View (Tailwind CSS) --}}
<div class="p-6">
    @if($media->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            @foreach($media as $item)
                <div class="group cursor-pointer" data-media-id="{{ $item->id }}" onclick="selectMediaItem({{ $item->id }})">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-300 transition-all duration-200 overflow-hidden">
                        <div class="relative aspect-square bg-gray-100">
                            {{-- Media Preview --}}
                            @if(str_starts_with($item->mime_type, 'image/'))
                                <img src="{{ $item->fixed_url ?? $item->getUrl() }}" alt="{{ $item->custom_properties['alt'] ?? $item->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                            @elseif(str_starts_with($item->mime_type, 'video/'))
                                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                                    <i class="fas fa-play-circle text-white text-4xl"></i>
                                </div>
                            @elseif(str_starts_with($item->mime_type, 'audio/'))
                                <div class="w-full h-full flex items-center justify-center bg-blue-600">
                                    <i class="fas fa-music text-white text-4xl"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-600">
                                    <i class="fas fa-file-alt text-white text-4xl"></i>
                                </div>
                            @endif

                            {{-- Selection Indicator --}}
                            <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <div class="w-6 h-6 bg-white bg-opacity-90 rounded-full flex items-center justify-center shadow-lg border-2 border-blue-600">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                            </div>

                            {{-- Hover Actions --}}
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                <div class="flex flex-col space-y-1">
                                    <!-- View Button -->
                                    <button onclick="viewMediaDetails({{ $item->id }})" 
                                            title="عرض التفاصيل"
                                            class="w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <!-- Copy Button -->
                                    <button onclick="copyToClipboard('{{ $item->fixed_url ?? $item->getUrl() }}')" 
                                            title="نسخ الرابط"
                                            class="w-8 h-8 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>
                                    <!-- Download Button -->
                                    <a href="{{ $item->fixed_url ?? $item->getUrl() }}" 
                                       download="{{ $item->name }}"
                                       title="تحميل الملف"
                                       class="w-8 h-8 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <!-- Delete Button -->
                                    <button onclick="deleteMedia({{ $item->id }})" 
                                            title="حذف الملف"
                                            class="w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Media Info --}}
                        <div class="p-3">
                            <h6 class="text-sm font-medium text-gray-900 truncate mb-1" title="{{ $item->name }}">
                                {{ $item->name }}
                            </h6>
                            <div class="text-xs text-gray-500">
                                <div class="flex justify-between items-center">
                                    <span>{{ $item->humanReadableSize ?? '0 KB' }}</span>
                                    <span>{{ $item->created_at->format('Y/m/d') }}</span>
                                </div>
                                @if($item->collection_name)
                                    <div class="mt-1">
                                        <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">{{ $item->collection_name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <i class="fas fa-images text-6xl text-gray-300 mb-6"></i>
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

<!-- Simple JavaScript for media grid functionality -->
<script>
let selectedMedia = new Set();

function selectMediaItem(mediaId) {
    // Toggle selection
    if (selectedMedia.has(mediaId)) {
        selectedMedia.delete(mediaId);
    } else {
        selectedMedia.add(mediaId);
    }
    
    // Update visual state
    const mediaElement = document.querySelector(`[data-media-id="${mediaId}"]`);
    if (mediaElement) {
        if (selectedMedia.has(mediaId)) {
            mediaElement.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
        } else {
            mediaElement.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
        }
    }
}

function viewMediaDetails(mediaId) {
    event.stopPropagation();
    // Show media details modal
    console.log('View details for media:', mediaId);
}

function copyToClipboard(url) {
    event.stopPropagation();
    navigator.clipboard.writeText(url).then(() => {
        // Show success message - يمكن إضافة toast notification هنا
        alert('تم نسخ الرابط بنجاح!');
        console.log('Copied to clipboard:', url);
    });
}

function deleteMedia(mediaId) {
    event.stopPropagation();
    if (confirm('هل أنت متأكد من حذف هذا الملف؟')) {
        // هنا يمكن إضافة AJAX request لحذف الملف
        console.log('Delete media:', mediaId);
        // مؤقتاً نعرض رسالة
        alert('سيتم تطبيق وظيفة الحذف قريباً');
    }
}
</script>
