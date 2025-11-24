@extends('admin.layouts.app')

@section('title', 'رسائل التواصل')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">رسائل التواصل</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة ومتابعة رسائل المواطنين والزوار</p>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center">
            <a href="{{ route('admin.contact-messages.archive.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                الأرشيف
                @php $archivedCount = \App\Models\ContactMessage::archived()->count(); @endphp
                @if($archivedCount > 0)
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs">{{ $archivedCount }}</span>
                @endif
            </a>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                <span class="text-gray-600 text-sm font-medium">جديد</span>
                <span class="text-red-600 font-bold text-xl mr-2">{{ $newCount }}</span>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                <span class="text-gray-600 text-sm font-medium">غير مقروء</span>
                <span class="text-amber-600 font-bold text-xl mr-2">{{ $unreadCount }}</span>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                <span class="text-gray-600 text-sm font-medium">رسائلي</span>
                <span class="text-blue-600 font-bold text-xl mr-2">{{ $myMessagesCount }}</span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200">
        <div class="p-4">
            <form action="{{ route('admin.contact-messages.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">فلتر سريع</label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="my_messages" value="1" {{ request('my_messages') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="mr-2 text-sm font-medium text-gray-700">رسائلي فقط</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">حالة الموافقة</label>
                        <select name="approval_status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="forwarded" {{ request('approval_status') == 'forwarded' ? 'selected' : '' }}>محولة</option>
                            <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                            <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">الكل</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديد</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروء</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">نوع اللقاء</label>
                        <select name="meeting_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">الكل</option>
                            <option value="private" {{ request('meeting_type') == 'private' ? 'selected' : '' }}>لقاء خاص</option>
                            <option value="general" {{ request('meeting_type') == 'general' ? 'selected' : '' }}>عام</option>
                            <option value="urgent" {{ request('meeting_type') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                        </select>
                    </div>
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">بحث</label>
                        <input type="text" name="search" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="الاسم، البريد، الهاتف..." value="{{ request('search') }}">
                    </div>
                    <div class="lg:col-span-1 flex items-end">
                        <button type="submit" class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            بحث
                        </button>
                    </div>
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
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">التاريخ</th>
                        @can('assign_contact_messages')
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">مكلف إلى</th>
                        @endcan
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($messages as $message)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$message->read_at ? 'bg-amber-50/30' : '' }}">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $message->id }}</td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $message->full_name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $message->phone }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-800">{{ Str::limit($message->subject, 40) }}</div>
                                @if(!$message->read_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 mt-1">
                                        جديد
                                    </span>
                                @endif
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
                                @if($message->approval_status == 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        معلقة
                                    </span>
                                @elseif($message->approval_status == 'forwarded')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        محولة
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
                                @if($message->approval_status == 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        تمت الموافقة عليها
                                    </span>
                                @elseif($message->approval_status == 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        مرفوضة
                                    </span>
                                @elseif($message->status == 'new')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        جديد
                                    </span>
                                @elseif($message->status == 'read')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        مقروء
                                    </span>
                                @elseif($message->status == 'in_progress')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        قيد المعالجة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        مغلق
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $message->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</div>
                            </td>
                            @can('assign_contact_messages')
                            <td class="px-4 py-4">
                                @if($message->assignedUser)
                                    <div class="text-sm text-gray-900">{{ $message->assignedUser->name }}</div>
                                @else
                                    <div class="text-sm text-gray-400">غير مكلف</div>
                                @endif
                            </td>
                            @endcan
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.contact-messages.show', $message->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors" title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @can('delete_contact_messages')
                                    <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('assign_contact_messages') ? '9' : '8' }}" class="px-4 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">لا توجد رسائل</p>
                                <p class="text-gray-400 text-sm mt-1">لم يتم العثور على أي رسائل تطابق معايير البحث</p>
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
