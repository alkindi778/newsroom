@extends('admin.layouts.app')

@section('title', 'مكتبة الصور')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">مكتبة الصور</h1>
                <p class="mt-2 text-gray-600">إدارة جميع ملفات الوسائط والصور</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="openMediaUpload()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    رفع ملف جديد
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-shadow duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                <i class="fas fa-images text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">إجمالي الملفات</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-shadow duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                <i class="fas fa-image text-green-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['images'] }}</h3>
            <p class="text-gray-600 text-sm">الصور</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-shadow duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mb-4">
                <i class="fas fa-video text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['videos'] }}</h3>
            <p class="text-gray-600 text-sm">الفيديوهات</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-shadow duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg mb-4">
                <i class="fas fa-file text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['documents'] }}</h3>
            <p class="text-gray-600 text-sm">المستندات</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="p-6">
            <form method="GET" id="media-filters" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="البحث في الملفات..."
                               class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>الصور</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>الفيديوهات</option>
                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>الصوتيات</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>المستندات</option>
                    </select>
                </div>

                <!-- Collection Filter -->
                <div>
                    <select name="collection" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="all" {{ request('collection') == 'all' ? 'selected' : '' }}>جميع المجموعات</option>
                        @foreach($stats['collections'] as $collectionName => $count)
                            <option value="{{ $collectionName }}" {{ request('collection') == $collectionName ? 'selected' : '' }}>
                                {{ ucfirst($collectionName) }} ({{ $count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- View Toggle -->
                <div>
                    <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="view" value="grid" class="sr-only" {{ $view == 'grid' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center py-2 px-3 text-sm {{ $view == 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                                <i class="fas fa-th"></i>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="view" value="list" class="sr-only" {{ $view == 'list' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center py-2 px-3 text-sm {{ $view == 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                                <i class="fas fa-list"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div>
                    <button type="submit" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-filter ml-2"></i>
                        فلترة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Media Grid/List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($view == 'grid')
            @include('admin.media-library.partials.grid', ['media' => $media])
        @else
            @include('admin.media-library.partials.list', ['media' => $media])
        @endif
    </div>

    <!-- Pagination -->
    @if($media->hasPages())
        <div class="flex justify-center mt-8">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{ $media->appends(request()->query())->links() }}
            </nav>
        </div>
    @endif
</div>

<!-- Upload Modal (Tailwind CSS) -->
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">رفع ملف جديد</h3>
                <button type="button" onclick="closeUploadModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <div id="upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer">
                    <div class="upload-placeholder">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-700 mb-2">اسحب الملفات هنا أو انقر للاختيار</h4>
                        <p class="text-gray-500 mb-4">الحد الأقصى: 10MB لكل ملف</p>
                        <input type="file" id="file-input" multiple 
                               accept="image/*,video/*,audio/*,.pdf,.doc,.docx" 
                               class="hidden">
                        <button type="button" 
                                onclick="document.getElementById('file-input').click()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            اختيار الملفات
                        </button>
                    </div>
                </div>
                
                <!-- Upload Progress -->
                <div id="upload-progress" class="mt-6 hidden">
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="upload-status text-sm text-gray-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Details Modal (Tailwind CSS) -->
<div id="mediaDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">تفاصيل الملف</h3>
                <button type="button" onclick="closeDetailsModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6" id="media-details-content">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simple Media Library Functions (Tailwind CSS)

function closeDetailsModal() {
    const modal = document.getElementById('mediaDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filters on change
    document.querySelectorAll('#media-filters select, #media-filters input[type="radio"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('media-filters').submit();
        });
    });

    // Search on enter or after typing pause
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('media-filters').submit();
            }, 500);
        });
    }

    // Handle file input change
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    
    if (fileInput && uploadArea) {
        fileInput.addEventListener('change', function(e) {
            handleFileUpload(e.target.files);
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
            handleFileUpload(e.dataTransfer.files);
        });
    }
});

function openMediaUpload() {
    const modal = document.getElementById('uploadModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function handleFileUpload(files) {
    if (!files || files.length === 0) return;
    
    const progressContainer = document.getElementById('upload-progress');
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
                        <div class="font-bold mb-2 text-lg">✅ تم رفع وضغط الصورة بنجاح!</div>
                        <div class="text-sm space-y-1 bg-gray-50 p-3 rounded-lg">
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
                            <div class="flex justify-between text-green-800 border-t pt-2 mt-2">
                                <span class="font-bold">نسبة الضغط:</span>
                                <span class="text-xl font-bold">${compression.reduction}</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = '<div class="text-green-600 font-bold text-lg">✅ تم رفع الملفات بنجاح!</div>';
            }
            
            progressBar.style.width = '100%';
            progressBar.classList.add('bg-green-500');
            
            setTimeout(() => {
                progressContainer.classList.add('hidden');
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-green-500');
                closeUploadModal();
                window.location.reload();
            }, 4000); // عرض النتيجة لمدة 4 ثواني
        } else {
            throw new Error(data.message || 'فشل الرفع');
        }
    })
    .catch(error => {
        clearInterval(progressInterval); // إيقاف محاكاة التقدم
        console.error('خطأ في الرفع:', error);
        statusDiv.innerHTML = '<div class="text-red-600 font-bold text-lg">❌ ' + error.message + '</div>';
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

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadModal();
    }
});
</script>
@endpush
