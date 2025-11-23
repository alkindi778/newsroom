@extends('admin.layouts.app')

@section('title', 'تعديل الفيديو')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تعديل الفيديو</h1>
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

    <form action="{{ route('admin.videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                        <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $video->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video URL -->
                    <div class="mb-4">
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                            رابط الفيديو <span class="text-red-500">*</span>
                        </label>
                        <input type="url" id="video_url" name="video_url" value="{{ old('video_url', $video->video_url) }}" required
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
                        <input type="text" id="duration" name="duration" value="{{ old('duration', $video->duration) }}"
                               placeholder="00:00"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('duration') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">مثال: 03:45 أو 01:22:30</p>
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Thumbnail -->
                    @if($video->thumbnail)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المصغرة الحالية</label>
                            <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}" 
                                 class="max-w-xs rounded-lg shadow-md">
                        </div>
                    @endif

                    <!-- Preview from video URL -->
                    <div id="thumbnail_preview" class="hidden mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المصغرة من الرابط الجديد</label>
                        <img id="thumbnail_preview_img" src="" alt="Thumbnail" class="max-w-xs rounded-lg shadow-md">
                        <p class="text-xs text-gray-500 mt-1">يمكنك استخدامها بدلاً من رفع صورة جديدة</p>
                    </div>

                    <!-- Thumbnail -->
                    <div class="mb-4">
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">تغيير الصورة المصغرة</label>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('thumbnail') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">اختياري - اتركه فارغاً للاحتفاظ بالصورة الحالية</p>
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SEO Hidden Fields - Auto Generated -->
                <input type="hidden" id="meta_title" name="meta_title" value="{{ old('meta_title', $video->meta_title) }}">
                <input type="hidden" id="meta_description" name="meta_description" value="{{ old('meta_description', $video->meta_description) }}">
                <input type="hidden" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $video->meta_keywords) }}">

                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">الإحصائيات</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-eye text-3xl text-blue-600"></i>
                            <div>
                                <p class="text-xs text-gray-500">المشاهدات</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($video->views) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg">
                            <i class="fas fa-thumbs-up text-3xl text-green-600"></i>
                            <div>
                                <p class="text-xs text-gray-500">الإعجابات</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($video->likes) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-share text-3xl text-purple-600"></i>
                            <div>
                                <p class="text-xs text-gray-500">المشاركات</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($video->shares) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publishing Options -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">خيارات النشر</h2>
                    
                    <!-- Published -->
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $video->is_published) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="mr-2 text-sm font-medium text-gray-700">نشر الفيديو</span>
                        </label>
                    </div>

                    <!-- Featured -->
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $video->is_featured) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="mr-2 text-sm font-medium text-gray-700">فيديو مميز</span>
                        </label>
                    </div>

                    <!-- Published At -->
                    <div class="mb-4">
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ النشر</label>
                        <input type="datetime-local" id="published_at" name="published_at" 
                               value="{{ old('published_at', $video->published_at ? $video->published_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('published_at') border-red-500 @enderror">
                        @error('published_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Video Info -->
                    <hr class="my-4">
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>نوع الفيديو:</strong> {{ ucfirst($video->video_type) }}</p>
                        @if($video->video_id)
                            <p><strong>معرف الفيديو:</strong> {{ $video->video_id }}</p>
                        @endif
                        <p><strong>تاريخ الإنشاء:</strong> {{ $video->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>آخر تحديث:</strong> {{ $video->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors mb-3">
                        <i class="fas fa-save"></i>
                        <span>حفظ التغييرات</span>
                    </button>
                    <a href="{{ route('admin.videos.show', $video->id) }}" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors mb-3">
                        <i class="fas fa-eye"></i>
                        <span>عرض الفيديو</span>
                    </a>
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
    if (url && (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com') || url.includes('facebook.com') || url.includes('fb.watch'))) {
        // Show loading indicator
        const urlInput = this;
        urlInput.classList.add('border-blue-500');
        
        // Debounce the request
        fetchTimeout = setTimeout(async () => {
            console.log('Fetching video info for:', url); // Debug log
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
                console.log('Video info received:', data); // Debug log
                
                if (data.success) {
                    // Show thumbnail preview first
                    if (data.thumbnail) {
                        const preview = document.getElementById('thumbnail_preview');
                        const img = document.getElementById('thumbnail_preview_img');
                        img.src = data.thumbnail;
                        preview.classList.remove('hidden');
                    }
                    
                    // Ask before overwriting existing values
                    const shouldUpdate = confirm('هل تريد تحديث معلومات الفيديو من الرابط الجديد؟');
                    
                    if (shouldUpdate) {
                        // Fill form fields
                        if (data.title) {
                            document.getElementById('title').value = data.title;
                            document.getElementById('title').dispatchEvent(new Event('input'));
                        }
                        
                        if (data.description) {
                            document.getElementById('description').value = data.description;
                            document.getElementById('description').dispatchEvent(new Event('input'));
                        }
                        
                        if (data.duration) {
                            document.getElementById('duration').value = data.duration;
                        }
                        
                        // Build notification message
                        let message = 'تم تحديث معلومات الفيديو بنجاح!';
                        if (data.type === 'youtube' && !data.description) {
                            message = '✅ العنوان والصورة فقط (أضف YOUTUBE_API_KEY في .env للحصول على الوصف والمدة)';
                        } else if (data.type === 'facebook') {
                            message = '✅ تم جلب بيانات الفيديو من فيسبوك';
                        }
                        showNotification(message, 'success');
                    }
                    
                    // Show success
                    urlInput.classList.remove('border-blue-500');
                    urlInput.classList.add('border-green-500');
                    setTimeout(() => {
                        urlInput.classList.remove('border-green-500');
                    }, 2000);
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
