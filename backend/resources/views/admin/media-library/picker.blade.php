{{-- Media Picker Modal (Tailwind CSS) --}}
<div id="mediaPickerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeMediaPicker()"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-screen-xl bg-white rounded-lg shadow-xl" style="height: 90vh;">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b bg-blue-600 text-white rounded-t-lg">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-semibold">اختيار الوسائط</h3>
                    <div class="flex rounded-lg overflow-hidden bg-blue-500">
                        <label class="cursor-pointer">
                            <input type="radio" name="picker-mode" value="select" class="sr-only" id="picker-select-mode" checked>
                            <div class="px-4 py-2 text-sm font-medium bg-blue-400 hover:bg-blue-300 transition-colors duration-200 picker-mode-tab" data-tab="library-tab">
                                <i class="fas fa-images ml-1"></i>
                                مكتبة الوسائط
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="picker-mode" value="upload" class="sr-only" id="picker-upload-mode">
                            <div class="px-4 py-2 text-sm font-medium bg-blue-700 hover:bg-blue-600 transition-colors duration-200 picker-mode-tab" data-tab="upload-tab">
                                <i class="fas fa-upload ml-1"></i>
                                رفع جديد
                            </div>
                        </label>
                    </div>
                </div>
                <button type="button" onclick="closeMediaPicker()" class="text-white hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            
            <!-- Body -->
            <div class="flex" style="height: calc(90vh - 160px);">
                <!-- Left Sidebar -->
                <div class="w-80 bg-gray-50 border-l border-gray-200 overflow-y-auto">
                    <!-- Upload Tab -->
                    <div class="picker-tab hidden" id="upload-tab">
                        <div class="p-4">
                            <h6 class="text-base font-semibold text-gray-900 mb-4">رفع ملفات جديدة</h6>
                            
                            <!-- Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center mb-4 hover:border-blue-400 transition-colors duration-200 cursor-pointer" id="picker-upload-area">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-700 mb-2">اسحب الملفات هنا</p>
                                <p class="text-sm text-gray-500 mb-4">أو</p>
                                <input type="file" id="picker-file-input" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx" class="hidden">
                                <button type="button" 
                                        onclick="document.getElementById('picker-file-input').click()"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    اختيار الملفات
                                </button>
                                <p class="text-xs text-gray-500 mt-3">الحد الأقصى: 10MB لكل ملف</p>
                            </div>
                            
                            <!-- Upload Progress -->
                            <div class="hidden" id="picker-upload-progress">
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="progress-bar bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <div class="upload-status text-sm text-gray-600"></div>
                            </div>
                            
                            <!-- Recently Uploaded -->
                            <div class="recently-uploaded hidden" id="recently-uploaded">
                                <h6 class="text-base font-semibold text-gray-900 mb-3">تم رفعها حديثاً</h6>
                                <div class="uploaded-list space-y-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Library Tab -->
                    <div class="picker-tab" id="library-tab">
                        <div class="p-4 border-b border-gray-200">
                            <h6 class="text-base font-semibold text-gray-900 mb-4">تصفية الملفات</h6>
                            
                            <!-- Search -->
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" id="picker-search" placeholder="البحث..."
                                           class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                            
                            <!-- Type Filter -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الملف</label>
                                <select id="picker-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="all">جميع الملفات</option>
                                    <option value="image">الصور فقط</option>
                                    <option value="video">الفيديوهات فقط</option>
                                    <option value="audio">الصوتيات فقط</option>
                                    <option value="document">المستندات فقط</option>
                                </select>
                            </div>
                            
                            <!-- Collection Filter -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">المجموعة</label>
                                <select id="picker-collection-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="all">جميع المجموعات</option>
                                    @if(isset($media) && $media->isNotEmpty())
                                        @foreach($media->groupBy('collection_name') as $collection => $items)
                                            <option value="{{ $collection }}">{{ ucfirst($collection) }} ({{ $items->count() }})</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <!-- Date Filter -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الرفع</label>
                                <select id="picker-date-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="all">جميع التواريخ</option>
                                    <option value="today">اليوم</option>
                                    <option value="week">هذا الأسبوع</option>
                                    <option value="month">هذا الشهر</option>
                                    <option value="year">هذا العام</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Selected Files Info -->
                        <div class="selected-info p-4 border-t border-gray-200 hidden" id="selected-info">
                            <h6 class="text-base font-semibold text-blue-600 mb-3">محدد حالياً</h6>
                            <div class="selected-preview max-h-48 overflow-y-auto space-y-2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col bg-gray-100">
                    <!-- Media Grid -->
                    <div class="flex-1 overflow-auto p-4 bg-white" id="picker-media-grid">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3" id="media-items-container">
                            @if(isset($media) && $media->count() > 0)
                                @foreach($media as $item)
                                    <div class="picker-media-item bg-white rounded-lg shadow-sm border-2 border-transparent cursor-pointer hover:border-blue-400 hover:shadow-md transition-all duration-200" 
                                         data-media-id="{{ $item->id }}" onclick="selectMediaItem({{ $item->id }})">
                                        <div class="relative aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                                            @if(str_starts_with($item->mime_type, 'image/'))
                                                <img src="{{ $item->fixed_url ?? $item->getUrl() }}" 
                                                     alt="{{ $item->custom_properties['alt'] ?? $item->name }}" 
                                                     class="w-full h-full object-cover"
                                                     loading="lazy">
                                            @elseif(str_starts_with($item->mime_type, 'video/'))
                                                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                                                    <i class="fas fa-play-circle text-white text-3xl"></i>
                                                </div>
                                            @elseif(str_starts_with($item->mime_type, 'audio/'))
                                                <div class="w-full h-full flex items-center justify-center bg-blue-600">
                                                    <i class="fas fa-music text-white text-3xl"></i>
                                                </div>
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-600">
                                                    <i class="fas fa-file-alt text-white text-3xl"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Selection Checkmark -->
                                            <div class="absolute top-2 right-2 selection-checkmark opacity-0 transition-opacity duration-200">
                                                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <div class="text-sm font-medium text-gray-900 truncate" title="{{ $item->name }}">
                                                {{ $item->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $item->humanReadableSize ?? '0 KB' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full">
                                    <div class="text-center py-16">
                                        <i class="fas fa-images text-6xl text-gray-300 mb-6"></i>
                                        <h5 class="text-xl font-semibold text-gray-600 mb-3">لا توجد ملفات</h5>
                                        <p class="text-gray-500">ابدأ برفع بعض الملفات</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Loading Spinner -->
                        <div class="text-center py-8 hidden" id="picker-loading">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="text-gray-600 mt-2">جاري التحميل...</p>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50" id="picker-pagination">
                        @if(isset($media) && $media->hasPages())
                            <nav class="flex justify-center">
                                <div class="flex space-x-1">
                                    {{ $media->links() }}
                                </div>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <div class="text-sm text-gray-600">
                    <span id="picker-selected-count">0</span> عنصر محدد
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeMediaPicker()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        إلغاء
                    </button>
                    <button type="button" id="picker-insert-button" disabled
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check ml-1"></i>
                        إدراج المحدد
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden data for JavaScript --}}
<script type="text/javascript">
window.MediaPickerData = {
    media: @json($media ?? []),
    mode: '{{ $mode ?? "select" }}',
    multiple: {{ $multiple ? 'true' : 'false' }},
    field: '{{ $field ?? "image" }}',
    collection: '{{ $collection ?? "all" }}'
};
</script>

<!-- Tailwind CSS Styles (No custom CSS needed) -->
<script>
// Media Picker JavaScript (Tailwind CSS version)
let selectedMediaItems = new Set();
let currentMode = 'select';

// Initialize media picker functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    document.querySelectorAll('.picker-mode-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            switchPickerMode(tabId);
        });
    });

    // Filter change handlers
    document.getElementById('picker-search').addEventListener('input', debounce(filterMedia, 300));
    document.getElementById('picker-type-filter').addEventListener('change', filterMedia);
    document.getElementById('picker-collection-filter').addEventListener('change', filterMedia);
    document.getElementById('picker-date-filter').addEventListener('change', filterMedia);

    // Upload area drag and drop
    const uploadArea = document.getElementById('picker-upload-area');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('dragleave', handleDragLeave);
        uploadArea.addEventListener('drop', handleDrop);
    }

    // File input change
    const fileInput = document.getElementById('picker-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', handleFileSelect);
    }

    // Insert button handler
    document.getElementById('picker-insert-button').addEventListener('click', insertSelectedMedia);
});

function switchPickerMode(mode) {
    // Hide all tabs
    document.querySelectorAll('.picker-tab').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show selected tab
    document.getElementById(mode).classList.remove('hidden');
    
    // Update tab buttons
    document.querySelectorAll('.picker-mode-tab').forEach(tab => {
        if (tab.dataset.tab === mode) {
            tab.classList.remove('bg-blue-700');
            tab.classList.add('bg-blue-400');
        } else {
            tab.classList.remove('bg-blue-400');
            tab.classList.add('bg-blue-700');
        }
    });
    
    currentMode = mode === 'upload-tab' ? 'upload' : 'select';
}

function selectMediaItem(mediaId) {
    const item = document.querySelector(`[data-media-id="${mediaId}"]`);
    if (!item) return;

    if (selectedMediaItems.has(mediaId)) {
        // Deselect
        selectedMediaItems.delete(mediaId);
        item.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200');
        const checkmark = item.querySelector('.selection-checkmark');
        if (checkmark) checkmark.classList.add('opacity-0');
    } else {
        // Select
        selectedMediaItems.add(mediaId);
        item.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
        const checkmark = item.querySelector('.selection-checkmark');
        if (checkmark) checkmark.classList.remove('opacity-0');
    }

    updateSelectedCount();
    updateSelectedPreview();
}

function updateSelectedCount() {
    const count = selectedMediaItems.size;
    document.getElementById('picker-selected-count').textContent = count;
    document.getElementById('picker-insert-button').disabled = count === 0;

    // Show/hide selected info panel
    const selectedInfo = document.getElementById('selected-info');
    if (count > 0) {
        selectedInfo.classList.remove('hidden');
    } else {
        selectedInfo.classList.add('hidden');
    }
}

function updateSelectedPreview() {
    const preview = document.querySelector('.selected-preview');
    if (!preview) return;

    preview.innerHTML = '';
    selectedMediaItems.forEach(mediaId => {
        const item = document.querySelector(`[data-media-id="${mediaId}"]`);
        if (item) {
            const name = item.querySelector('.text-sm').textContent;
            const size = item.querySelector('.text-xs').textContent;
            const img = item.querySelector('img');
            
            const previewItem = document.createElement('div');
            previewItem.className = 'flex items-center p-2 bg-white border border-gray-200 rounded-lg';
            previewItem.innerHTML = `
                ${img ? `<img src="${img.src}" class="w-10 h-10 object-cover rounded mr-3">` : '<div class="w-10 h-10 bg-gray-200 rounded mr-3"></div>'}
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">${name}</div>
                    <div class="text-xs text-gray-500">${size}</div>
                </div>
                <button onclick="selectMediaItem(${mediaId})" class="text-red-600 hover:text-red-800 p-1">
                    <i class="fas fa-times"></i>
                </button>
            `;
            preview.appendChild(previewItem);
        }
    });
}

function filterMedia() {
    // Implement media filtering logic here
    console.log('Filtering media...');
}

function handleDragOver(e) {
    e.preventDefault();
    this.classList.add('border-blue-400', 'bg-blue-50');
}

function handleDragLeave(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
    const files = e.dataTransfer.files;
    handleFileUpload(files);
}

function handleFileSelect(e) {
    handleFileUpload(e.target.files);
}

function handleFileUpload(files) {
    if (!files || files.length === 0) return;
    
    const progressContainer = document.getElementById('picker-upload-progress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const statusDiv = progressContainer.querySelector('.upload-status');
    
    progressContainer.classList.remove('hidden');
    statusDiv.textContent = `جاري رفع ${files.length} ملف...`;
    progressBar.style.width = '0%';
    
    // إعداد FormData للرفع
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('collection', 'articles'); // يمكن تخصيصه حسب الحاجة
    
    // متغير لـ progress interval
    let progressInterval;
    
    // رفع الملفات باستخدام fetch
    fetch('{{ route("admin.media.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('فشل الرفع');
        }
        return response.json();
    })
    .then(data => {
        clearInterval(progressInterval); // إيقاف محاكاة التقدم
        
        if (data.success) {
            // التحقق من وجود بيانات ضغط
            const hasCompression = data.data && data.data.length > 0 && data.data[0].compression;
            
            if (hasCompression) {
                const compression = data.data[0].compression;
                statusDiv.innerHTML = `
                    <div class="text-green-600">
                        <div class="font-bold mb-2 text-base">✅ تم رفع وضغط الصورة بنجاح!</div>
                        <div class="text-xs space-y-1 bg-gray-50 p-2 rounded-lg">
                            <div class="flex justify-between">
                                <span>الحجم الأصلي:</span>
                                <span class="font-semibold">${compression.original_size}</span>
                            </div>
                            <div class="flex justify-between text-green-700">
                                <span>الحجم الجديد:</span>
                                <span class="font-semibold">${compression.new_size}</span>
                            </div>
                            <div class="flex justify-between text-blue-700">
                                <span>تم توفير:</span>
                                <span class="font-semibold">${compression.saved}</span>
                            </div>
                            <div class="flex justify-between text-green-800 border-t pt-1 mt-1">
                                <span class="font-bold">نسبة الضغط:</span>
                                <span class="text-lg font-bold">${compression.reduction}</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = '<div class="text-green-600 font-bold">✅ تم رفع الملفات بنجاح!</div>';
            }
            
            progressBar.style.width = '100%';
            progressBar.classList.add('bg-green-500');
            
            setTimeout(() => {
                progressContainer.classList.add('hidden');
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-green-500');
                // Switch to library tab to show uploaded files
                switchPickerMode('library-tab');
                // يمكن إضافة reload للمحتوى هنا
            }, 3000); // عرض النتيجة لمدة 3 ثواني
        } else {
            throw new Error(data.message || 'فشل الرفع');
        }
    })
    .catch(error => {
        clearInterval(progressInterval); // إيقاف محاكاة التقدم
        console.error('خطأ في الرفع:', error);
        statusDiv.innerHTML = '<div class="text-red-600 font-bold">❌ ' + error.message + '</div>';
        progressBar.classList.add('bg-red-500');
        
        setTimeout(() => {
            progressContainer.classList.add('hidden');
            progressBar.style.width = '0%';
            progressBar.classList.remove('bg-red-500');
        }, 3000);
    });
    
    // محاكاة progress أثناء الرفع والضغط
    let progress = 0;
    let stage = 'uploading'; // uploading, compressing
    progressInterval = setInterval(() => {
        if (progress < 50) {
            progress += Math.random() * 10;
            progress = Math.min(progress, 50);
            statusDiv.innerHTML = `
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                    <span>جاري رفع الملفات... ${Math.round(progress)}%</span>
                </div>
            `;
        } else if (progress < 90 && stage === 'uploading') {
            stage = 'compressing';
            statusDiv.innerHTML = `
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600"></div>
                    <span class="text-green-600 font-semibold">🔄 جاري ضغط الصورة...</span>
                </div>
            `;
            progress += Math.random() * 10;
            progress = Math.min(progress, 90);
        } else {
            clearInterval(progressInterval);
        }
        progressBar.style.width = progress + '%';
    }, 400);
}

function insertSelectedMedia() {
    // Implement insert logic here
    console.log('Inserting selected media:', Array.from(selectedMediaItems));
    closeMediaPicker();
}

function closeMediaPicker() {
    const modal = document.getElementById('mediaPickerModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function openMediaPicker() {
    const modal = document.getElementById('mediaPickerModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMediaPicker();
    }
});
</script>
