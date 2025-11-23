@extends('admin.layouts.app')

@section('title', 'عرض الفيديو')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">عرض الفيديو</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.videos.edit', $video->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
            <a href="{{ route('admin.videos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Video Player -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="aspect-w-16 aspect-h-9">
                    @if($video->video_type === 'youtube')
                        <iframe src="{{ $video->embed_url }}" 
                                title="{{ $video->title }}" 
                                frameborder="0" 
                                class="w-full h-96"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    @elseif($video->video_type === 'vimeo')
                        <iframe src="{{ $video->embed_url }}" 
                                title="{{ $video->title }}" 
                                frameborder="0" 
                                class="w-full h-96"
                                allow="autoplay; fullscreen; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    @elseif($video->video_type === 'facebook')
                        <iframe src="{{ $video->embed_url }}" 
                                title="{{ $video->title }}" 
                                frameborder="0" 
                                class="w-full h-96"
                                style="border:none;overflow:hidden" 
                                scrolling="no" 
                                frameborder="0" 
                                allowTransparency="true" 
                                allow="encrypted-media" 
                                allowFullScreen="true">
                        </iframe>
                    @else
                        <video controls class="w-full h-96">
                            <source src="{{ $video->video_url }}" type="video/mp4">
                            متصفحك لا يدعم عرض الفيديوهات
                        </video>
                    @endif
                </div>
            </div>

            <!-- Video Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">تفاصيل الفيديو</h2>
                    <div class="flex gap-2">
                        @if($video->is_published)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">منشور</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">مسودة</span>
                        @endif
                        @if($video->is_featured)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 flex items-center gap-1">
                                <i class="fas fa-star"></i>
                                مميز
                            </span>
                        @endif
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $video->title }}</h3>
                
                @if($video->description)
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">الوصف:</h4>
                        <p class="text-gray-600">{{ $video->description }}</p>
                    </div>
                @endif

                <hr class="my-4">

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="mb-2">
                            <strong class="text-gray-700">نوع الفيديو:</strong> 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800">{{ ucfirst($video->video_type) }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="mb-2">
                            <strong class="text-gray-700">المدة:</strong> 
                            <span class="text-gray-600">{{ $video->duration ?? 'غير محدد' }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="mb-2">
                            <strong class="text-gray-700">تاريخ النشر:</strong> 
                            <span class="text-gray-600">{{ $video->published_at ? $video->published_at->format('Y-m-d H:i') : 'غير منشور' }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="mb-2">
                            <strong class="text-gray-700">الكاتب:</strong> 
                            <span class="text-gray-600">{{ $video->user->name }}</span>
                        </p>
                    </div>
                </div>

                @if($video->video_id)
                    <p class="mt-4"><strong class="text-gray-700">معرف الفيديو:</strong> <span class="text-gray-600">{{ $video->video_id }}</span></p>
                @endif

                <p class="mt-4">
                    <strong class="text-gray-700">رابط الفيديو:</strong> 
                    <a href="{{ $video->video_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline break-all">
                        {{ $video->video_url }}
                    </a>
                </p>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">الإحصائيات</h2>
                
                <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg mb-3">
                    <i class="fas fa-eye text-3xl text-blue-600"></i>
                    <div>
                        <p class="text-xs text-gray-500">المشاهدات</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($video->views) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg mb-3">
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

            <!-- Thumbnail -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">الصورة المصغرة</h2>
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" 
                     class="w-full rounded-lg shadow-sm">
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">إجراءات سريعة</h2>
                
                @if($video->is_published)
                    <form action="{{ route('admin.videos.unpublish', $video->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-times"></i>
                            <span>إلغاء النشر</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.videos.publish', $video->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-check"></i>
                            <span>نشر الفيديو</span>
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.videos.toggle-featured', $video->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                        <i class="fas fa-star"></i>
                        <span>{{ $video->is_featured ? 'إزالة التمييز' : 'تمييز الفيديو' }}</span>
                    </button>
                </form>

                <hr class="my-4">

                <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الفيديو؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                        <i class="fas fa-trash"></i>
                        <span>حذف الفيديو</span>
                    </button>
                </form>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">معلومات إضافية</h2>
                <div class="text-sm text-gray-600 space-y-2">
                    <p><strong>تاريخ الإنشاء:</strong><br>{{ $video->created_at->format('Y-m-d H:i:s') }}</p>
                    <p><strong>آخر تحديث:</strong><br>{{ $video->updated_at->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Slug:</strong><br><span class="break-all">{{ $video->slug }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
