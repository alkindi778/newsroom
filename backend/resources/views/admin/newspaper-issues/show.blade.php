@extends('admin.layouts.app')

@section('title', 'عرض إصدار الصحيفة')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">عرض إصدار الصحيفة</h1>
            <p class="text-sm text-gray-600">معاينة تفاصيل الإصدار رقم {{ $issue->issue_number }} لصحيفة {{ $issue->newspaper_name }}.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.newspaper-issues.edit', $issue->id) }}"
               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل
            </a>
            <a href="{{ route('admin.newspaper-issues.index') }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        @if($issue->is_published)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">منشور</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">مسودة</span>
                        @endif

                        @if($issue->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star ml-1 text-yellow-500"></i>
                                إصدار مميز
                            </span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        تم الإنشاء في {{ $issue->created_at?->format('Y/m/d H:i') }}
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $issue->newspaper_name }}</h2>
                <p class="text-sm text-gray-600 mb-4">العدد رقم {{ $issue->issue_number }}</p>

                @if($issue->description)
                    <div class="bg-blue-50 border-r-4 border-blue-400 p-4 rounded mb-4">
                        <p class="text-gray-700 leading-relaxed">{{ $issue->description }}</p>
                    </div>
                @endif

                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>تاريخ النشر: {{ $issue->publication_date?->format('Y/m/d') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <a href="{{ $issue->pdf_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-sm">فتح ملف الـ PDF</a>
                    </div>
                </div>
            </div>

            <!-- Cover Image -->
            @if($issue->cover_image)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">صورة الغلاف</h3>
                    <div class="relative">
                        <img src="{{ asset('storage/'.$issue->cover_image) }}" alt="{{ $issue->newspaper_name }}" class="w-full h-80 object-cover rounded-lg">
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إحصائيات الإصدار</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 flex items-center gap-1">
                            <i class="fas fa-eye text-gray-400"></i>
                            المشاهدات
                        </span>
                        <span class="font-semibold">{{ number_format($issue->views) }}</span>
                    </div>
                    <div class="flex justify بین">
                        <span class="text-gray-600 flex items-center gap-1">
                            <i class="fas fa-download text-gray-400"></i>
                            التحميلات
                        </span>
                        <span class="font-semibold">{{ number_format($issue->downloads) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات إضافية</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="font-medium">{{ $issue->created_at?->format('Y/m/d H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="font-medium">{{ $issue->updated_at?->format('Y/m/d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
