@extends('admin.layouts.app')

@section('title', 'لوحة إحصائيات الرسائل')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                لوحة إحصائيات الرسائل
            </h1>
            <p class="mt-1 text-sm text-gray-600">نظرة شاملة على رسائل التواصل والأداء</p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.contact-messages.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors">
                كل الرسائل
            </a>
            <a href="{{ route('admin.contact-messages.templates.index') }}" class="px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-800 font-medium rounded-lg transition-colors">
                قوالب الردود
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600">إجمالي</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $stats['new'] }}</div>
            <div class="text-sm text-red-700">جديد</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-4 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</div>
            <div class="text-sm text-yellow-700">قيد المعالجة</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['closed'] }}</div>
            <div class="text-sm text-green-700">مغلق</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['pending_approval'] }}</div>
            <div class="text-sm text-blue-700">بانتظار الموافقة</div>
        </div>
        <div class="bg-emerald-50 rounded-lg shadow-sm border border-emerald-200 p-4 text-center">
            <div class="text-3xl font-bold text-emerald-600">{{ $stats['approved'] }}</div>
            <div class="text-sm text-emerald-700">موافق عليها</div>
        </div>
        <div class="bg-rose-50 rounded-lg shadow-sm border border-rose-200 p-4 text-center">
            <div class="text-3xl font-bold text-rose-600">{{ $stats['rejected'] }}</div>
            <div class="text-sm text-rose-700">مرفوضة</div>
        </div>
    </div>

    <!-- إحصائيات هذا الشهر ومتوسط الاستجابة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-4xl font-bold">{{ $monthlyStats['received'] }}</div>
                    <div class="text-blue-100">رسائل هذا الشهر</div>
                </div>
                <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-4xl font-bold">{{ $monthlyStats['responded'] }}</div>
                    <div class="text-green-100">تم الرد عليها</div>
                </div>
                <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-4xl font-bold">{{ $monthlyStats['closed'] }}</div>
                    <div class="text-purple-100">تم إغلاقها</div>
                </div>
                <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-4xl font-bold">{{ round($avgResponseTime ?? 0) }}</div>
                    <div class="text-orange-100">متوسط الاستجابة (ساعة)</div>
                </div>
                <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- التصنيفات -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    تصنيفات الرسائل (AI)
                </h3>
            </div>
            <div class="p-6">
                @php
                    $categoryLabels = [
                        'complaint' => ['label' => 'شكوى', 'color' => 'red', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                        'inquiry' => ['label' => 'استفسار', 'color' => 'blue', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                        'meeting_request' => ['label' => 'طلب لقاء', 'color' => 'purple', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'],
                        'suggestion' => ['label' => 'اقتراح', 'color' => 'yellow', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>'],
                        'praise' => ['label' => 'إشادة', 'color' => 'green', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>'],
                        'other' => ['label' => 'أخرى', 'color' => 'gray', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'],
                    ];
                    $totalCategorized = array_sum($categoryCounts);
                @endphp
                
                @if($totalCategorized > 0)
                    <div class="space-y-3">
                        @foreach($categoryLabels as $key => $cat)
                            @php $count = $categoryCounts[$key] ?? 0; $percent = $totalCategorized > 0 ? round(($count / $totalCategorized) * 100) : 0; @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-{{ $cat['color'] }}-600">{!! $cat['icon'] !!}</span>
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $cat['label'] }}</span>
                                        <span class="text-gray-500">{{ $count }} ({{ $percent }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $cat['color'] }}-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد بيانات تصنيف متاحة</p>
                @endif
            </div>
        </div>

        <!-- المشاعر -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    تحليل المشاعر (AI)
                </h3>
            </div>
            <div class="p-6">
                @php
                    $sentimentLabels = [
                        'positive' => ['label' => 'إيجابي', 'color' => 'green', 'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                        'negative' => ['label' => 'سلبي', 'color' => 'red', 'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                        'neutral' => ['label' => 'محايد', 'color' => 'gray', 'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                        'urgent' => ['label' => 'عاجل', 'color' => 'orange', 'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'],
                    ];
                    $totalSentiment = array_sum($sentimentCounts);
                @endphp
                
                @if($totalSentiment > 0)
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($sentimentLabels as $key => $sent)
                            @php $count = $sentimentCounts[$key] ?? 0; $percent = $totalSentiment > 0 ? round(($count / $totalSentiment) * 100) : 0; @endphp
                            <div class="bg-{{ $sent['color'] }}-50 rounded-lg p-4 text-center border border-{{ $sent['color'] }}-200">
                                <div class="flex justify-center mb-2 text-{{ $sent['color'] }}-600">{!! $sent['icon'] !!}</div>
                                <div class="text-2xl font-bold text-{{ $sent['color'] }}-600">{{ $count }}</div>
                                <div class="text-sm text-{{ $sent['color'] }}-700">{{ $sent['label'] }} ({{ $percent }}%)</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد بيانات تحليل متاحة</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- الرسائل العاجلة -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-orange-500 border-b px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    رسائل تحتاج اهتمام عاجل
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($urgentMessages as $msg)
                    <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="block p-4 hover:bg-red-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $msg->full_name }}</div>
                                <div class="text-sm text-gray-600">{{ Str::limit($msg->subject, 50) }}</div>
                            </div>
                            <div class="text-xs text-gray-500">{{ $msg->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        لا توجد رسائل عاجلة
                    </div>
                @endforelse
            </div>
        </div>

        <!-- أحدث الردود -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-500 border-b px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    أحدث الردود المرسلة
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentReplies as $reply)
                    <a href="{{ route('admin.contact-messages.show', $reply->contact_message_id) }}" class="block p-4 hover:bg-green-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $reply->contactMessage->full_name ?? 'غير معروف' }}</div>
                                <div class="text-sm text-gray-600">بواسطة: {{ $reply->user->name ?? 'غير معروف' }}</div>
                            </div>
                            <div class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        لا توجد ردود حتى الآن
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- أفضل المستجيبين -->
    @if($topResponders->isNotEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 border-b px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                أفضل المستجيبين هذا الشهر
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4 justify-center">
                @foreach($topResponders as $index => $responder)
                    <div class="text-center px-6 py-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border">
                        <div class="mb-2">
                            @if($index === 0)
                                <svg class="w-8 h-8 mx-auto text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @elseif($index === 1)
                                <svg class="w-8 h-8 mx-auto text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @elseif($index === 2)
                                <svg class="w-8 h-8 mx-auto text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @else
                                <svg class="w-7 h-7 mx-auto text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            @endif
                        </div>
                        <div class="font-semibold text-gray-900">{{ $responder->user->name ?? 'غير معروف' }}</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ $responder->count }}</div>
                        <div class="text-xs text-gray-500">رد</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- أنواع اللقاءات -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                توزيع أنواع اللقاءات
            </h3>
        </div>
        <div class="p-6">
            @php
                $meetingLabels = [
                    'general' => ['label' => 'عام', 'color' => 'gray'],
                    'private' => ['label' => 'لقاء خاص', 'color' => 'blue'],
                    'urgent' => ['label' => 'عاجل', 'color' => 'red'],
                ];
                $totalMeetings = array_sum($meetingTypes);
            @endphp
            
            <div class="flex flex-wrap gap-4 justify-center">
                @foreach($meetingLabels as $key => $meeting)
                    @php $count = $meetingTypes[$key] ?? 0; $percent = $totalMeetings > 0 ? round(($count / $totalMeetings) * 100) : 0; @endphp
                    <div class="text-center px-8 py-4 bg-{{ $meeting['color'] }}-50 rounded-xl border border-{{ $meeting['color'] }}-200">
                        <div class="text-3xl font-bold text-{{ $meeting['color'] }}-600">{{ $count }}</div>
                        <div class="text-sm text-{{ $meeting['color'] }}-700">{{ $meeting['label'] }}</div>
                        <div class="text-xs text-{{ $meeting['color'] }}-500">{{ $percent }}%</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
