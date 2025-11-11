@extends('admin.layouts.app')

@section('title', 'مراجعة الرسالة')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مراجعة الرسالة #{{ $message->id }}</h1>
            <p class="text-sm text-gray-600">قم بمراجعة الرسالة والموافقة أو الرفض</p>
        </div>
        <a href="{{ route('admin.contact-messages.review.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <div class="space-y-6">
        <!-- معلومات المرسل -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900">معلومات المرسل</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">الاسم الكامل</label>
                        <div class="flex items-center gap-2 text-gray-900 bg-gray-50 px-4 py-3 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ $message->full_name }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                        <a href="mailto:{{ $message->email }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-4 py-3 rounded-lg transition-colors group">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            <span class="font-medium group-hover:underline">{{ $message->email }}</span>
                        </a>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف</label>
                        <a href="tel:{{ $message->phone }}" class="flex items-center gap-2 text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-4 py-3 rounded-lg transition-colors group">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                            <span class="font-medium group-hover:underline">{{ $message->phone }}</span>
                        </a>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نوع اللقاء</label>
                        <div class="bg-gray-50 px-4 py-3 rounded-lg">
                            @if($message->meeting_type == 'private')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    لقاء خاص
                                </span>
                            @elseif($message->meeting_type == 'urgent')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    عاجل
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                    عام
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الموضوع</label>
                    <div class="bg-blue-50 border-r-4 border-blue-500 px-4 py-3 rounded-lg">
                        <p class="text-lg font-semibold text-blue-900">{{ $message->subject }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسالة -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900">محتوى الرسالة</h2>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap text-lg">{{ $message->message }}</p>
                </div>
            </div>
        </div>

        <!-- قرار الموافقة/الرفض -->
        @if($message->approval_status == 'forwarded')
        <div class="bg-white rounded-lg shadow-sm border-2 border-blue-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 border-b border-blue-200 px-6 py-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    اتخذ القرار
                </h2>
                <p class="text-sm text-gray-600 mt-1">هذه الرسالة بانتظار قرارك</p>
            </div>
            <div class="p-6 space-y-6">
                <!-- الموافقة -->
                <div class="bg-green-50 rounded-lg p-6 border-2 border-green-200">
                    <h3 class="text-lg font-bold text-green-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        الموافقة على الرسالة
                    </h3>
                    <form action="{{ route('admin.contact-messages.review.approve', $message->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات الموافقة (اختياري)</label>
                            <textarea name="approval_notes" rows="4" class="w-full px-4 py-3 bg-white border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none" placeholder="أضف ملاحظاتك حول الموافقة..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-bold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 shadow-lg">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            الموافقة
                        </button>
                    </form>
                </div>
                
                <div class="border-t-2 border-gray-300 my-6"></div>
                
                <!-- الرفض -->
                <div class="bg-red-50 rounded-lg p-6 border-2 border-red-200">
                    <h3 class="text-lg font-bold text-red-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        رفض الرسالة
                    </h3>
                    <form action="{{ route('admin.contact-messages.review.reject', $message->id) }}" method="POST" class="space-y-4" onsubmit="return confirm('هل أنت متأكد من رفض هذه الرسالة؟')">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-red-900 mb-2">سبب الرفض (مطلوب)</label>
                            <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-3 bg-white border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none" placeholder="يرجى توضيح سبب الرفض..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-4 bg-red-600 hover:bg-red-700 text-white text-lg font-bold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 shadow-lg">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            رفض
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        
        <!-- إذا تمت الموافقة أو الرفض -->
        @if($message->approval_status == 'approved' || $message->approval_status == 'rejected')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-50 border-b border-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-200 px-6 py-4">
                <h2 class="text-lg font-bold text-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-900">
                    {{ $message->approval_status == 'approved' ? 'تمت الموافقة' : 'تم الرفض' }}
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-50 rounded-lg p-6 border border-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-200">
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">
                        {{ $message->approval_status == 'approved' ? $message->approval_notes : $message->rejection_reason }}
                    </p>
                    <div class="mt-4 pt-4 border-t border-{{ $message->approval_status == 'approved' ? 'green' : 'red' }}-200 text-sm text-gray-600">
                        <strong>{{ $message->approver->name }}</strong> - {{ $message->approved_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
