w@extends('admin.layouts.app')

@section('title', 'المنشورات على وسائل التواصل')
@section('page-title', 'المنشورات على وسائل التواصل')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-pink-50 to-purple-50 -mx-6 -my-6 px-6 py-6">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">المنشورات على وسائل التواصل</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة المنشورات والنشر على المنصات المختلفة</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي المنشورات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $posts->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Published -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">منشورة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SocialMediaPost::where('status', 'published')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">قيد الانتظار</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SocialMediaPost::where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Scheduled -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">مجدولة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SocialMediaPost::where('status', 'scheduled')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Failed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">فاشلة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SocialMediaPost::where('status', 'failed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.social-media.posts') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Platform Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المنصة</label>
                <select name="platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">الكل</option>
                    <option value="facebook" {{ request('platform') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="twitter" {{ request('platform') === 'twitter' ? 'selected' : '' }}>Twitter</option>
                    <option value="telegram" {{ request('platform') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">الكل</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>منشورة</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فاشلة</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    تصفية
                </button>
                <a href="{{ route('admin.social-media.posts') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الخبر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">المنصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">تاريخ النشر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Article -->
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $post->article->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $post->article->category->name ?? 'بدون فئة' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Platform -->
                        <td class="px-6 py-4 text-sm">
                            @if ($post->platform === 'facebook')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Facebook</span>
                            @elseif ($post->platform === 'twitter')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Twitter</span>
                            @else
                            <span class="px-3 py-1 bg-cyan-100 text-cyan-800 rounded-full text-xs font-medium">Telegram</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-sm">
                            @if ($post->status === 'published')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ منشورة</span>
                            @elseif ($post->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ قيد الانتظار</span>
                            @elseif ($post->status === 'scheduled')
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">📅 مجدولة</span>
                            @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">✗ فاشلة</span>
                            @endif
                        </td>

                        <!-- Published Date -->
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '-' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                @if ($post->status === 'failed')
                                <form method="POST" action="{{ route('admin.social-media.retry-post', $post) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 text-xs font-medium" title="إعادة محاولة">
                                        إعادة محاولة
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('admin.social-media.delete-post', $post) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Error Message Row -->
                    @if ($post->error_message)
                    <tr class="bg-red-50">
                        <td colspan="5" class="px-6 py-3">
                            <p class="text-xs text-red-600"><strong>رسالة الخطأ:</strong> {{ $post->error_message }}</p>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500">لا توجد منشورات</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            {{ $posts->links() }}
        </div>
    </div>
</div>
</div>
@endsection
