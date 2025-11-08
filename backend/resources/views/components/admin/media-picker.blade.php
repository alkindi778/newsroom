{{-- 
    Media Picker Component (Tailwind CSS)
    Usage: <x-admin.media-picker field="image" label="اختيار الصورة" :value="$article->image_url ?? ''" />
--}}

@props([
    'field' => 'image',
    'label' => 'اختيار الصورة',
    'value' => '',
    'collection' => 'all',
    'multiple' => false,
    'required' => false,
    'help' => ''
])

<div class="mb-6">
    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    {{-- Hidden input to store selected media URL/ID --}}
    <input type="hidden" name="{{ $field }}" id="{{ $field }}_input" value="{{ $value }}">
    
    {{-- Media Preview & Selection Area --}}
    <div class="media-picker-container border border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200">
        {{-- Preview Area --}}
        <div class="media-preview-area" id="{{ $field }}_preview" 
             style="{{ $value ? '' : 'display: none;' }}">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                {{-- Image Preview --}}
                <div class="relative bg-gray-100">
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ $value ?: 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23e5e7eb\' width=\'100\' height=\'100\'/%3E%3Ctext fill=\'%239ca3af\' font-size=\'50\' font-family=\'Arial\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3E📷%3C/text%3E%3C/svg%3E' }}" 
                             alt="معاينة" 
                             class="w-full h-full object-contain" 
                             id="{{ $field }}_thumbnail">
                    </div>
                    {{-- Badge in top-right corner --}}
                    <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-lg flex items-center">
                        <i class="fas fa-check-circle ml-1.5"></i>
                        محددة
                    </div>
                </div>
                
                {{-- Info Section --}}
                <div class="p-4">
                    <div class="mb-3">
                        <h6 class="font-semibold text-gray-900 text-base mb-1" id="{{ $field }}_name">
                            {{ $value ? 'صورة محددة' : 'لا توجد صورة' }}
                        </h6>
                        <p class="text-sm text-gray-500" id="{{ $field }}_details">
                            {{ $value ? 'انقر "تغيير الصورة" لاختيار صورة أخرى' : 'لم يتم اختيار صورة بعد' }}
                        </p>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <button type="button" 
                                onclick="openMediaPickerFor('{{ $field }}', '{{ $collection }}')"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-images ml-2"></i>
                            {{ $value ? 'تغيير الصورة' : 'اختيار صورة' }}
                        </button>
                        @if($value)
                            <button type="button" 
                                    onclick="clearMediaSelection('{{ $field }}')"
                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-trash ml-2"></i>
                                إزالة
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Empty State --}}
        <div class="media-empty-state text-center py-8 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-400 transition-colors duration-200" 
             id="{{ $field }}_empty" style="{{ $value ? 'display: none;' : '' }}">
            <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
            <h6 class="text-gray-600 font-medium mb-2">لم يتم اختيار صورة</h6>
            <p class="text-sm text-gray-500 mb-4">اختر صورة من مكتبة الوسائط أو ارفع صورة جديدة</p>
            <button type="button" 
                    onclick="openMediaPickerFor('{{ $field }}', '{{ $collection }}')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                اختيار صورة
            </button>
        </div>
    </div>
    
    {{-- Help Text --}}
    @if($help)
        <p class="mt-2 text-sm text-gray-500">{{ $help }}</p>
    @endif
    
    {{-- Error Display --}}
    @error($field)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Include Media Picker Modal --}}
@once
    @push('modals')
        {{-- Media Picker Modal (Tailwind CSS) --}}
        <div id="mediaPickerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeMediaPicker()"></div>
            
            {{-- Modal --}}
            <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4">
                <div class="relative w-full max-w-6xl bg-white sm:rounded-lg shadow-xl h-screen sm:h-[85vh]">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-3 sm:p-4 border-b bg-blue-600 text-white sm:rounded-t-lg">
                        <div class="flex items-center gap-2 sm:gap-4 flex-1">
                            <h3 class="text-base sm:text-lg font-semibold">اختيار الوسائط</h3>
                            <div class="hidden sm:flex rounded-lg overflow-hidden bg-blue-500">
                                <button type="button" id="tab-library" class="px-4 py-2 text-sm bg-blue-400 hover:bg-blue-300 transition-colors picker-tab active" data-tab="library">
                                    <i class="fas fa-images ml-2"></i>
                                    مكتبة الوسائط
                                </button>
                                <button type="button" id="tab-upload" class="px-4 py-2 text-sm bg-blue-700 hover:bg-blue-600 transition-colors picker-tab" data-tab="upload">
                                    <i class="fas fa-upload ml-2"></i>
                                    رفع جديد
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="closeMediaPicker()" class="text-white hover:text-gray-200 p-2">
                            <i class="fas fa-times text-lg sm:text-xl"></i>
                        </button>
                    </div>
                    
                    {{-- Mobile Tabs --}}
                    <div class="sm:hidden flex border-b bg-white">
                        <button type="button" id="tab-library-mobile" class="flex-1 px-4 py-3 text-sm font-medium bg-blue-50 text-blue-600 border-b-2 border-blue-600 picker-tab" data-tab="library">
                            <i class="fas fa-images ml-2"></i>
                            المكتبة
                        </button>
                        <button type="button" id="tab-upload-mobile" class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 picker-tab" data-tab="upload">
                            <i class="fas fa-upload ml-2"></i>
                            رفع جديد
                        </button>
                    </div>
                    
                    {{-- Body --}}
                    <div class="flex flex-col sm:flex-row h-[calc(100vh-140px)] sm:h-[calc(85vh-140px)]">
                        {{-- Sidebar --}}
                        <div class="hidden sm:block w-80 bg-gray-50 border-l border-gray-200 overflow-y-auto">
                            {{-- Search & Filters --}}
                            <div class="p-4 border-b border-gray-200">
                                <h6 class="text-base font-semibold text-gray-900 mb-4">تصفية الملفات</h6>
                                
                                {{-- Search --}}
                                <div class="mb-4">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" id="media-search" placeholder="البحث..."
                                               class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                </div>
                                
                                {{-- Type Filter --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الملف</label>
                                    <select id="media-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                        <option value="all">جميع الملفات</option>
                                        <option value="image">الصور فقط</option>
                                        <option value="video">الفيديوهات فقط</option>
                                        <option value="document">المستندات فقط</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- Upload Area (Desktop Only) --}}
                            <div id="upload-section" class="p-4 hidden">
                                <h6 class="text-base font-semibold text-gray-900 mb-4">رفع ملفات جديدة</h6>
                                
                                {{-- Custom Name Input --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الصورة (اختياري)</label>
                                    <input type="text" id="media-name" placeholder="اترك فارغاً لاستخدام الاسم الأصلي" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <p class="text-xs text-gray-500 mt-1">سيتم استخدام هذا الاسم لجميع الصور المرفوعة</p>
                                </div>
                                
                                <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-700 mb-2">اسحب الملفات هنا</p>
                                    <p class="text-sm text-gray-500 mb-4">أو</p>
                                    <input type="file" id="file-input" multiple accept="image/*" class="hidden">
                                    <button type="button" onclick="document.getElementById('file-input').click()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        اختيار الملفات
                                    </button>
                                    <p class="text-xs text-gray-500 mt-3">الحد الأقصى: 10MB لكل ملف</p>
                                </div>
                                
                                {{-- Upload Progress --}}
                                <div id="upload-progress" class="mt-6 hidden">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <div id="upload-status" class="text-sm text-gray-600"></div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Mobile Upload Area --}}
                        <div id="upload-section-mobile" class="sm:hidden p-3 hidden">
                            <div class="mb-4">
                                <h6 class="text-base font-semibold text-gray-900 mb-3">رفع ملفات جديدة</h6>
                                
                                {{-- Custom Name Input --}}
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم الصورة (اختياري)</label>
                                    <input type="text" id="media-name-mobile" placeholder="اترك فارغاً لاستخدام الاسم الأصلي" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                
                                <div id="drop-area-mobile" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-700 mb-3">اضغط للاختيار من معرض الصور</p>
                                    <input type="file" id="file-input-mobile" multiple accept="image/*" class="hidden">
                                    <button type="button" onclick="document.getElementById('file-input-mobile').click()" class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg">
                                        <i class="fas fa-images ml-2"></i>
                                        اختيار الصور
                                    </button>
                                    <p class="text-xs text-gray-500 mt-3">الحد الأقصى: 10MB لكل ملف</p>
                                </div>
                                
                                {{-- Upload Progress Mobile --}}
                                <div id="upload-progress-mobile" class="mt-4 hidden">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div id="progress-bar-mobile" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <div id="upload-status-mobile" class="text-sm text-gray-600"></div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Mobile Search Bar --}}
                        <div class="sm:hidden p-3 border-b bg-white sticky top-0 z-10">
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="media-search-mobile" placeholder="البحث في المكتبة..."
                                       class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>
                        
                        {{-- Main Content --}}
                        <div class="flex-1 flex flex-col overflow-hidden bg-white">
                            <div class="flex-1 overflow-y-auto p-2 sm:p-4">
                                <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
                                    {{-- Media items will be loaded here --}}
                                    <div class="text-center py-12 sm:py-16 col-span-full">
                                        <div id="loading-spinner" class="inline-block animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600 mb-3 sm:mb-4"></div>
                                        <p class="text-sm sm:text-base text-gray-600">جاري تحميل الصور...</p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Pagination Controls --}}
                            <div id="pagination-controls" class="border-t bg-gray-50 px-3 sm:px-4 py-3 hidden">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs sm:text-sm text-gray-600">
                                        عرض <span id="pagination-from">0</span> - <span id="pagination-to">0</span> من <span id="pagination-total">0</span>
                                    </div>
                                    <div class="flex gap-1 sm:gap-2">
                                        <button type="button" id="pagination-prev" class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        <div class="flex items-center gap-1 px-2 sm:px-3">
                                            <span class="text-xs sm:text-sm text-gray-700"><span id="pagination-current">1</span> / <span id="pagination-last">1</span></span>
                                        </div>
                                        <button type="button" id="pagination-next" class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer --}}
                    <div class="flex items-center justify-between p-3 sm:p-4 border-t bg-gray-50 sm:rounded-b-lg">
                        <div class="text-xs sm:text-sm text-gray-600">
                            <span id="selected-count">0</span> محدد
                        </div>
                        <div class="flex gap-2 sm:gap-3">
                            <button type="button" onclick="closeMediaPicker()" class="px-3 sm:px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                إلغاء
                            </button>
                            <button type="button" id="insert-selected" disabled class="px-3 sm:px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check ml-1 sm:ml-2"></i>
                                إدراج
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endpush
    
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/media-library.css') }}">
        <style>
            .media-picker-container {
                transition: all 0.3s ease;
            }
            
            .media-picker-container:hover {
                border-color: #0d6efd;
                background-color: rgba(13, 110, 253, 0.05);
            }
            
            .media-preview-area .media-thumbnail {
                border: 2px solid #e9ecef;
                border-radius: 8px;
                overflow: hidden;
            }
            
            .media-empty-state {
                border: 2px dashed #dee2e6;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            
            .media-empty-state:hover {
                border-color: #0d6efd;
                background-color: rgba(13, 110, 253, 0.02);
            }
        </style>
    @endpush
    
    @push('scripts')
        <script>
            // Media Picker JavaScript (Complete Implementation)
            let currentMediaField = null;
            let currentMediaCollection = 'library';
            let selectedMedia = new Set();
            let mediaData = [];
            let currentPage = 1;
            let totalPages = 1;
            let paginationData = {};
            
            function openMediaPickerFor(fieldName, collection = 'all') {
                // Set the current field
                currentMediaField = fieldName;
                // Save collection for upload, but always show 'all' in picker
                currentMediaCollection = collection !== 'all' ? collection : 'library';
                
                // Show modal
                const modal = document.getElementById('mediaPickerModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    
                    // Always load all media, not filtered by collection
                    loadMediaLibrary('all');
                }
            }
            
            function closeMediaPicker() {
                const modal = document.getElementById('mediaPickerModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    
                    // Clear selection
                    selectedMedia.clear();
                    updateSelectedCount();
                }
            }
            
            // Load media from server
            async function loadMediaLibrary(collection = 'all', bustCache = false, page = 1) {
                const mediaGrid = document.getElementById('media-grid');
                const spinner = document.getElementById('loading-spinner');
                
                try {
                    // Show loading
                    mediaGrid.innerHTML = `
                        <div class="text-center py-16 col-span-full">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                            <p class="text-gray-600">جاري تحميل الصور...</p>
                        </div>
                    `;
                    
                    // Fetch media from API with cache busting if needed
                const apiUrl = '{{ route("admin.media.api") }}';
                const timestamp = bustCache ? `&_t=${Date.now()}` : '';
                const response = await fetch(`${apiUrl}?collection=${collection}&page=${page}&per_page=10${timestamp}`, {
                    cache: bustCache ? 'no-cache' : 'default'
                });
                    const data = await response.json();
                    
                    if (data.success) {
                        mediaData = data.media;
                        paginationData = data.pagination;
                        currentPage = paginationData.current_page;
                        totalPages = paginationData.last_page;
                        
                        renderMediaGrid(mediaData);
                        updatePaginationControls();
                    } else {
                        console.error('Error loading media:', data.message);
                        showEmptyState();
                    }
                } catch (error) {
                    console.error('Error loading media:', error);
                    showEmptyState();
                }
            }
            
            // Update pagination controls
            function updatePaginationControls() {
                const paginationControls = document.getElementById('pagination-controls');
                const prevBtn = document.getElementById('pagination-prev');
                const nextBtn = document.getElementById('pagination-next');
                const currentSpan = document.getElementById('pagination-current');
                const lastSpan = document.getElementById('pagination-last');
                const fromSpan = document.getElementById('pagination-from');
                const toSpan = document.getElementById('pagination-to');
                const totalSpan = document.getElementById('pagination-total');
                
                if (paginationData && paginationData.total > 0) {
                    paginationControls.classList.remove('hidden');
                    
                    currentSpan.textContent = paginationData.current_page;
                    lastSpan.textContent = paginationData.last_page;
                    fromSpan.textContent = paginationData.from || 0;
                    toSpan.textContent = paginationData.to || 0;
                    totalSpan.textContent = paginationData.total;
                    
                    prevBtn.disabled = paginationData.current_page === 1;
                    nextBtn.disabled = paginationData.current_page === paginationData.last_page;
                } else {
                    paginationControls.classList.add('hidden');
                }
            }
            
            // Render media grid
            function renderMediaGrid(media) {
                const mediaGrid = document.getElementById('media-grid');
                
                if (!media || media.length === 0) {
                    showEmptyState();
                    return;
                }
                
                const mediaItems = media.map(item => `
                    <div class="media-item bg-white rounded-lg shadow-sm border-2 border-blue-200 cursor-pointer hover:border-blue-400 hover:shadow-md transition-all duration-200 overflow-hidden ${selectedMedia.has(item.id) ? 'border-blue-600 ring-2 ring-blue-200' : ''}" 
                         data-media-id="${item.id}" onclick="toggleMediaSelection(${item.id})">
                        <div class="relative aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                            ${item.mime_type && item.mime_type.startsWith('image/') ? 
                                `<img src="${item.url}" alt="${item.name}" class="w-full h-full object-cover">` :
                                `<div class="w-full h-full flex items-center justify-center bg-gray-600">
                                    <i class="fas fa-file-alt text-white text-2xl"></i>
                                </div>`
                            }
                            
                            <div class="absolute top-2 right-2 selection-checkmark ${selectedMedia.has(item.id) ? 'opacity-100' : 'opacity-0'} transition-opacity duration-200">
                                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="p-3 bg-white rounded-b-lg">
                            <div class="text-sm font-medium text-gray-900 truncate" title="${item.name}">
                                ${item.name}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                ${item.human_readable_size || '0 KB'}
                            </div>
                        </div>
                    </div>
                `).join('');
                
                mediaGrid.innerHTML = mediaItems;
            }
            
            // Show empty state
            function showEmptyState() {
                const mediaGrid = document.getElementById('media-grid');
                mediaGrid.innerHTML = `
                    <div class="text-center py-16 col-span-full">
                        <i class="fas fa-images text-6xl text-gray-300 mb-6"></i>
                        <h4 class="text-xl font-semibold text-gray-600 mb-3">لا توجد ملفات</h4>
                        <p class="text-gray-500 mb-6">لا توجد ملفات في مكتبة الوسائط حالياً</p>
                        <button type="button" onclick="switchToUpload()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus ml-2"></i>
                            رفع ملف جديد
                        </button>
                    </div>
                `;
            }
            
            // Toggle media selection
            function toggleMediaSelection(mediaId) {
                if (selectedMedia.has(mediaId)) {
                    selectedMedia.delete(mediaId);
                } else {
                    selectedMedia.add(mediaId);
                }
                
                // Update visual state
                const mediaElement = document.querySelector(`[data-media-id="${mediaId}"]`);
                if (mediaElement) {
                    const checkmark = mediaElement.querySelector('.selection-checkmark');
                    if (selectedMedia.has(mediaId)) {
                        mediaElement.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
                        checkmark.classList.remove('opacity-0');
                        checkmark.classList.add('opacity-100');
                    } else {
                        mediaElement.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200');
                        checkmark.classList.remove('opacity-100');
                        checkmark.classList.add('opacity-0');
                    }
                }
                
                updateSelectedCount();
            }
            
            // Update selected count
            function updateSelectedCount() {
                const count = selectedMedia.size;
                document.getElementById('selected-count').textContent = count;
                document.getElementById('insert-selected').disabled = count === 0;
            }
            
            // Insert selected media
            function insertSelectedMedia() {
                if (selectedMedia.size === 0 || !currentMediaField) return;
                
                // Get first selected media (for single selection)
                const mediaId = Array.from(selectedMedia)[0];
                const media = mediaData.find(item => item.id === mediaId);
                
                if (media) {
                    updateMediaField(currentMediaField, {
                        id: media.id,
                        url: media.url,
                        name: media.name
                    });
                }
                
                closeMediaPicker();
            }
            
            // Update media field
            function updateMediaField(fieldName, mediaData) {
                const input = document.getElementById(fieldName + '_input');
                const preview = document.getElementById(fieldName + '_preview');
                const empty = document.getElementById(fieldName + '_empty');
                const thumbnail = document.getElementById(fieldName + '_thumbnail');
                const name = document.getElementById(fieldName + '_name');
                const details = document.getElementById(fieldName + '_details');
                
                if (mediaData && mediaData.url) {
                    // Extract relative path from full URL
                    let relativePath = mediaData.url;
                    if (relativePath.includes('/storage/')) {
                        // Extract everything after /storage/
                        relativePath = relativePath.split('/storage/')[1];
                    }
                    
                    // Update input value with relative path only
                    if (input) input.value = relativePath;
                    
                    // Update thumbnail
                    if (thumbnail) thumbnail.src = mediaData.url;
                    
                    // Generate a better display name from the file name
                    let displayName = 'صورة محددة';
                    if (mediaData.name) {
                        // Extract filename from path if needed
                        const fileName = mediaData.name.split('/').pop().split('\\').pop();
                        // Remove hash and extension for cleaner display
                        displayName = fileName.replace(/^[a-zA-Z0-9]{40}_/, '').replace(/\.[^/.]+$/, '') || fileName;
                        // If still looks like a hash, just show "صورة محددة"
                        if (displayName.length > 40 || /^[a-zA-Z0-9]{20,}$/.test(displayName)) {
                            displayName = 'صورة محددة';
                        }
                    }
                    
                    // Update name and details
                    if (name) name.textContent = displayName;
                    if (details) {
                        const fileSize = mediaData.human_readable_size || mediaData.size || '';
                        details.textContent = fileSize ? `${fileSize} • انقر "تغيير الصورة" للتبديل` : 'انقر "تغيير الصورة" لاختيار صورة أخرى';
                    }
                    
                    // Show preview, hide empty state
                    if (preview) preview.style.display = 'block';
                    if (empty) empty.style.display = 'none';
                } else {
                    clearMediaSelection(fieldName);
                }
            }
            
            function clearMediaSelection(fieldName) {
                const input = document.getElementById(fieldName + '_input');
                const preview = document.getElementById(fieldName + '_preview');
                const empty = document.getElementById(fieldName + '_empty');
                
                // Clear input
                if (input) input.value = '';
                
                // Hide preview, show empty state
                if (preview) preview.style.display = 'none';
                if (empty) empty.style.display = 'block';
            }
            
            // Tab switching
            function switchToUpload() {
                document.getElementById('tab-upload').click();
            }
            
            // Initialize when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Tab switching
                document.querySelectorAll('.picker-tab').forEach(tab => {
                    tab.addEventListener('click', function() {
                        const tabType = this.dataset.tab;
                        
                        // Update tab states for desktop tabs
                        document.querySelectorAll('.picker-tab').forEach(t => {
                            const isMobile = t.id.includes('mobile');
                            const isCurrentMobile = this.id.includes('mobile');
                            
                            // Only update tabs of the same type (desktop or mobile)
                            if (isMobile === isCurrentMobile) {
                                if (t.dataset.tab === tabType) {
                                    if (isMobile) {
                                        // Mobile tab styles
                                        t.classList.add('bg-blue-50', 'text-blue-600', 'border-b-2', 'border-blue-600');
                                        t.classList.remove('text-gray-600');
                                    } else {
                                        // Desktop tab styles
                                        t.classList.remove('bg-blue-700');
                                        t.classList.add('bg-blue-400');
                                    }
                                } else {
                                    if (isMobile) {
                                        // Mobile tab styles
                                        t.classList.remove('bg-blue-50', 'text-blue-600', 'border-b-2', 'border-blue-600');
                                        t.classList.add('text-gray-600');
                                    } else {
                                        // Desktop tab styles
                                        t.classList.remove('bg-blue-400');
                                        t.classList.add('bg-blue-700');
                                    }
                                }
                            }
                        });
                        
                        // Show/hide sections
                        if (tabType === 'upload') {
                            document.getElementById('upload-section').classList.remove('hidden');
                            const uploadMobile = document.getElementById('upload-section-mobile');
                            if (uploadMobile) uploadMobile.classList.remove('hidden');
                        } else {
                            document.getElementById('upload-section').classList.add('hidden');
                            const uploadMobile = document.getElementById('upload-section-mobile');
                            if (uploadMobile) uploadMobile.classList.add('hidden');
                        }
                    });
                });
                
                // Insert button handler
                document.getElementById('insert-selected').addEventListener('click', insertSelectedMedia);
                
                // Pagination button handlers
                document.getElementById('pagination-prev').addEventListener('click', function() {
                    if (currentPage > 1) {
                        loadMediaLibrary('all', false, currentPage - 1);
                    }
                });
                
                document.getElementById('pagination-next').addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        loadMediaLibrary('all', false, currentPage + 1);
                    }
                });
                
                // Search functionality (Desktop)
                document.getElementById('media-search').addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    const filteredMedia = mediaData.filter(item => 
                        item.name.toLowerCase().includes(query)
                    );
                    renderMediaGrid(filteredMedia);
                });
                
                // Search functionality (Mobile)
                const mobileSearch = document.getElementById('media-search-mobile');
                if (mobileSearch) {
                    mobileSearch.addEventListener('input', function() {
                        const query = this.value.toLowerCase();
                        const filteredMedia = mediaData.filter(item => 
                            item.name.toLowerCase().includes(query)
                        );
                        renderMediaGrid(filteredMedia);
                    });
                }
                
                // Type filter
                document.getElementById('media-type-filter').addEventListener('change', function() {
                    const type = this.value;
                    let filteredMedia = mediaData;
                    
                    if (type !== 'all') {
                        filteredMedia = mediaData.filter(item => {
                            if (type === 'image') return item.mime_type && item.mime_type.startsWith('image/');
                            if (type === 'video') return item.mime_type && item.mime_type.startsWith('video/');
                            if (type === 'document') return item.mime_type && !item.mime_type.startsWith('image/') && !item.mime_type.startsWith('video/');
                            return true;
                        });
                    }
                    
                    renderMediaGrid(filteredMedia);
                });
                
                // File upload functionality
                const fileInput = document.getElementById('file-input');
                const dropArea = document.getElementById('drop-area');
                
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, unhighlight, false);
                });
                
                dropArea.addEventListener('drop', handleDrop, false);
                fileInput.addEventListener('change', handleFiles, false);
                
                // Mobile file upload functionality
                const fileInputMobile = document.getElementById('file-input-mobile');
                if (fileInputMobile) {
                    fileInputMobile.addEventListener('change', handleFiles, false);
                }
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                function highlight(e) {
                    dropArea.classList.add('border-blue-400', 'bg-blue-50');
                }
                
                function unhighlight(e) {
                    dropArea.classList.remove('border-blue-400', 'bg-blue-50');
                }
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    handleFiles({ target: { files: files } });
                }
                
                function handleFiles(e) {
                    const files = Array.from(e.target.files);
                    if (files.length > 0) {
                        uploadFiles(files);
                    }
                }
                
                async function uploadFiles(files) {
                    const progressContainer = document.getElementById('upload-progress');
                    const progressBar = document.getElementById('progress-bar');
                    const statusDiv = document.getElementById('upload-status');
                    
                    progressContainer.classList.remove('hidden');
                    
                    const formData = new FormData();
                    files.forEach(file => {
                        console.log('إضافة ملف للـ FormData:', file.name, file.type, file.size);
                        formData.append('files[]', file);
                    });
                    
                    // إضافة collection للرفع
                    formData.append('collection', currentMediaCollection || 'library');
                    
                    // إضافة الاسم المخصص للصورة
                    const customName = document.getElementById('media-name')?.value || 
                                      document.getElementById('media-name-mobile')?.value;
                    if (customName && customName.trim()) {
                        formData.append('name', customName.trim());
                        console.log('اسم مخصص للصورة:', customName.trim());
                    }
                    
                    // Log FormData contents
                    console.log('محتويات FormData:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key, value);
                    }
                    
                    try {
                        statusDiv.textContent = `جاري رفع ${files.length} ملف...`;
                        progressBar.style.width = '0%';
                        
                        const uploadUrl = '{{ route("admin.media.upload") }}';
                        console.log('رفع إلى URL:', uploadUrl);
                        
                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        progressBar.style.width = '100%';
                        
                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);
                        
                        if (response.ok) {
                            const result = await response.json();
                            console.log('نتيجة الرفع:', result);
                            statusDiv.textContent = 'تم رفع الملفات بنجاح!';
                            
                            setTimeout(() => {
                                progressContainer.classList.add('hidden');
                                progressBar.style.width = '0%';
                                
                                // Switch to library tab and reload with cache busting
                                document.getElementById('tab-library').click();
                                // Always reload all media to show newly uploaded files
                                loadMediaLibrary('all', true); // true = bust cache
                            }, 1000);
                        } else {
                            const errorText = await response.text();
                            console.error('خطأ في الاستجابة:', {
                                status: response.status,
                                statusText: response.statusText,
                                body: errorText
                            });
                            throw new Error(`Upload failed: ${response.status} - ${response.statusText}`);
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        statusDiv.textContent = 'حدث خطأ أثناء رفع الملفات: ' + error.message;
                        setTimeout(() => {
                            progressContainer.classList.add('hidden');
                        }, 2000);
                    }
                }
            });
            
            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMediaPicker();
                }
            });
        </script>
    @endpush
@endonce
