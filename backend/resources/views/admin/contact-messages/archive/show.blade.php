@extends('admin.layouts.app')

@section('title', 'عرض رسالة مؤرشفة')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                رسالة مؤرشفة #{{ $message->id }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">أُرشفت {{ $message->archived_at?->diffForHumans() }}</p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.contact-messages.archive.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                العودة للأرشيف
            </a>
            <form action="{{ route('admin.contact-messages.unarchive', $message->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    إلغاء الأرشفة
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات المرسل -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">معلومات المرسل</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">الاسم</label>
                            <p class="text-gray-900 font-semibold">{{ $message->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                            <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:underline">{{ $message->email }}</a>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">الهاتف</label>
                            <a href="tel:{{ $message->phone }}" class="text-green-600 hover:underline">{{ $message->phone }}</a>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الإرسال</label>
                            <p class="text-gray-900">{{ $message->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى الرسالة -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ $message->subject }}</h2>
                </div>
                <div class="p-6">
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($message->message)) !!}
                    </div>
                </div>
            </div>

            <!-- سجل الردود -->
            @if($message->replies->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">سجل الردود ({{ $message->replies->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($message->replies as $reply)
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ mb_substr($reply->user->name ?? 'م', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900">{{ $reply->user->name ?? 'مجهول' }}</span>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    <span class="px-2 py-0.5 rounded text-xs {{ $reply->type == 'email' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $reply->type == 'email' ? 'بريد' : 'ملاحظة' }}
                                    </span>
                                </div>
                                <div class="text-gray-700 text-sm">
                                    {!! nl2br(e($reply->content)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="lg:col-span-1 space-y-6">
            <!-- معلومات الأرشفة -->
            <div class="bg-white rounded-lg shadow-sm border-2 border-amber-200 overflow-hidden">
                <div class="bg-amber-50 border-b border-amber-200 px-6 py-4">
                    <h3 class="text-lg font-bold text-amber-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        معلومات الأرشفة
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">التصنيف</label>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                            {{ $message->archive_category ?? 'غير مصنف' }}
                        </span>
                    </div>
                    
                    @if($message->archive_summary)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">الملخص</label>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $message->archive_summary }}</p>
                    </div>
                    @endif
                    
                    @if($message->archive_tags && count($message->archive_tags) > 0)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">الوسوم</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($message->archive_tags as $tag)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="pt-4 border-t border-gray-200">
                        <div class="text-xs text-gray-500">
                            <p>أُرشفت بواسطة: <span class="font-medium text-gray-700">{{ $message->archiver->name ?? 'غير معروف' }}</span></p>
                            <p>تاريخ الأرشفة: <span class="font-medium text-gray-700">{{ $message->archived_at?->format('Y-m-d H:i') }}</span></p>
                        </div>
                    </div>
                    
                    <!-- إعادة التحليل -->
                    <form action="{{ route('admin.contact-messages.archive.reanalyze', $message->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            إعادة التحليل بالذكاء الاصطناعي
                        </button>
                    </form>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">معلومات إضافية</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($message->assignedUser)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">كانت مكلفة إلى</label>
                        <p class="text-gray-900 font-medium">{{ $message->assignedUser->name }}</p>
                    </div>
                    @endif
                    
                    @if($message->admin_notes)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">ملاحظات الإدارة</label>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $message->admin_notes }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">حالة الموافقة</label>
                        @if($message->approval_status == 'approved')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">موافق عليها</span>
                        @elseif($message->approval_status == 'rejected')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">مرفوضة</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $message->approval_status }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
