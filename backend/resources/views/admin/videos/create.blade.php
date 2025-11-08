@extends('admin.layouts.app')

@section('title', 'إضافة فيديو جديد')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إضافة فيديو جديد</h1>
        <a href="{{ route('admin.videos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Video Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">معلومات الفيديو</h2>
                    
                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            العنوان <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video URL -->
                    <div class="mb-4">
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                            رابط الفيديو <span class="text-red-500">*</span>
                        </label>
                        <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" required
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('video_url') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">أدخل رابط YouTube أو Vimeo</p>
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="mb-4">
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">المدة</label>
                        <input type="text" id="duration" name="duration" value="{{ old('duration') }}"
                               placeholder="00:00"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('duration') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">مثال: 03:45 أو 01:22:30</p>
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div class="mb-4">
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">صورة مصغرة</label>
                        
                        <!-- Preview from video URL -->
                        <div id="thumbnail_preview" class="hidden mb-3">
                            <img id="thumbnail_preview_img" src="" alt="Thumbnail" class="w-full max-w-xs rounded-lg shadow-md">
                            <p class="text-xs text-gray-500 mt-1">الصورة المصغرة من الفيديو (سيتم استخدامها تلقائياً)</p>
                        </div>
                        
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('thumbnail') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">اختياري - لتحميل صورة مخصصة، وإلا سيتم استخدام صورة الفيديو</p>
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SEO Hidden Fields - Auto Generated -->
                <input type="hidden" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                <input type="hidden" id="meta_description" name="meta_description" value="{{ old('meta_description') }}">
                <input type="hidden" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publishing Options -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">خيارات النشر</h2>
                    
                    <!-- Published -->
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="mr-2 text-sm font-medium text-gray-700">نشر الفيديو</span>
                        </label>
                    </div>

                    <!-- Featured -->
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="mr-2 text-sm font-medium text-gray-700">فيديو مميز</span>
                        </label>
                    </div>

                    <!-- Published At -->
                    <div class="mb-4">
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ النشر</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('published_at') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">اتركه فارغاً لاستخدام التاريخ الحالي</p>
                        @error('published_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors mb-3">
                        <i class="fas fa-save"></i>
                        <span>حفظ الفيديو</span>
                    </button>
                    <a href="{{ route('admin.videos.index') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-generate SEO fields from title and description
document.getElementById('title').addEventListener('input', function() {
    const title = this.value;
    document.getElementById('meta_title').value = title;
    
    // Generate keywords from title (extract main words)
    const keywords = title.split(' ').filter(word => word.length > 3).slice(0, 5).join(', ');
    document.getElementById('meta_keywords').value = keywords;
});

document.getElementById('description').addEventListener('input', function() {
    const description = this.value;
    // Limit meta description to 160 characters
    document.getElementById('meta_description').value = description.substring(0, 160);
});

// Fetch video info from URL
let fetchTimeout;
document.getElementById('video_url').addEventListener('input', function() {
    const url = this.value.trim();
    
    // Clear previous timeout
    clearTimeout(fetchTimeout);
    
    // Only fetch if URL looks valid
    if (url && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com'))) {
        // Show loading indicator
        const urlInput = this;
        urlInput.classList.add('border-blue-500');
        
        // Debounce the request
        fetchTimeout = setTimeout(async () => {
            try {
                const response = await fetch('{{ route("admin.videos.fetch-info") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ url: url })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Fill form fields
                    if (data.title && !document.getElementById('title').value) {
                        document.getElementById('title').value = data.title;
                        // Trigger input event to update SEO fields
                        document.getElementById('title').dispatchEvent(new Event('input'));
                    }
                    
                    if (data.description && !document.getElementById('description').value) {
                        document.getElementById('description').value = data.description;
                        document.getElementById('description').dispatchEvent(new Event('input'));
                    }
                    
                    if (data.duration && !document.getElementById('duration').value) {
                        document.getElementById('duration').value = data.duration;
                    }
                    
                    // Show thumbnail preview
                    if (data.thumbnail) {
                        const preview = document.getElementById('thumbnail_preview');
                        const img = document.getElementById('thumbnail_preview_img');
                        img.src = data.thumbnail;
                        preview.classList.remove('hidden');
                    }
                    
                    // Show success
                    urlInput.classList.remove('border-blue-500');
                    urlInput.classList.add('border-green-500');
                    setTimeout(() => {
                        urlInput.classList.remove('border-green-500');
                    }, 2000);
                    
                    // Build notification message
                    let message = 'تم جلب معلومات الفيديو بنجاح!';
                    if (data.type === 'youtube' && !data.description) {
                        message = '✅ العنوان والصورة فقط (أضف YOUTUBE_API_KEY في .env للحصول على الوصف والمدة)';
                    }
                    showNotification(message, 'success');
                } else {
                    urlInput.classList.remove('border-blue-500');
                    urlInput.classList.add('border-red-500');
                    setTimeout(() => {
                        urlInput.classList.remove('border-red-500');
                    }, 2000);
                }
            } catch (error) {
                console.error('Error fetching video info:', error);
                urlInput.classList.remove('border-blue-500');
            }
        }, 1000); // Wait 1 second after user stops typing
    }
});

// Show notification helper
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection
