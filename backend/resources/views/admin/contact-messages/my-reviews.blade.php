@extends('admin.layouts.app')

@section('title', 'رسائل للمراجعة')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">رسائل للمراجعة</h1>
            <p class="mt-1 text-sm text-gray-600">الرسائل المحولة إليك للموافقة أو الرفض</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                <span class="text-gray-600 text-sm font-medium">بانتظار المراجعة</span>
                <span class="text-blue-600 font-bold text-xl mr-2">{{ $pendingCount }}</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200">
        <div class="p-4">
            <form action="{{ route('admin.contact-messages.review.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">حالة الموافقة</label>
                    <select name="approval_status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">الكل</option>
                        <option value="forwarded" {{ request('approval_status') == 'forwarded' ? 'selected' : '' }}>بانتظار المراجعة</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الاسم</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الموضوع</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">نوع اللقاء</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">حالة الموافقة</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">التاريخ</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($messages as $message)
                        <tr class="hover:bg-gray-50 transition-colors {{ $message->approval_status == 'forwarded' ? 'bg-blue-50/30' : '' }}">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $message->id }}</td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $message->full_name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $message->phone }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-800">{{ Str::limit($message->subject, 40) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @if($message->meeting_type == 'private')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        لقاء خاص
                                    </span>
                                @elseif($message->meeting_type == 'urgent')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        عاجل
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        عام
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($message->approval_status == 'forwarded')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        بانتظار المراجعة
                                    </span>
                                @elseif($message->approval_status == 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        موافق عليها
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        مرفوضة
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $message->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.contact-messages.review.show', $message->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors" title="مراجعة">
                                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        مراجعة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">لا توجد رسائل للمراجعة</p>
                                <p class="text-gray-400 text-sm mt-1">لم يتم تحويل أي رسائل إليك</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
