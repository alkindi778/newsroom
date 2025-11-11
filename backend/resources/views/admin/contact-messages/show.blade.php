@extends('admin.layouts.app')

@section('title', 'عرض الرسالة')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل الرسالة #{{ $message->id }}</h1>
            <p class="text-sm text-gray-600">عرض ومعالجة الرسالة</p>
        </div>
        <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
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
                        <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
                    </div>
                </div>
            </div>

            <!-- قرار الموافقة/الرفض -->
            @if($message->approval_status == 'approved' && $message->approval_notes)
            <div class="bg-white rounded-lg shadow-sm border border-green-200 overflow-hidden">
                <div class="bg-green-50 border-b border-green-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-green-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        ملاحظات الموافقة
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->approval_notes }}</p>
                        @if($message->approver)
                        <div class="mt-4 pt-4 border-t border-green-200 text-sm text-gray-600">
                            <strong>{{ $message->approver->name }}</strong> - {{ $message->approved_at->format('Y-m-d H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            @if($message->approval_status == 'rejected' && $message->rejection_reason)
            <div class="bg-white rounded-lg shadow-sm border border-red-200 overflow-hidden">
                <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-red-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        سبب الرفض
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                        <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->rejection_reason }}</p>
                        @if($message->approver)
                        <div class="mt-4 pt-4 border-t border-red-200 text-sm text-gray-600">
                            <strong>{{ $message->approver->name }}</strong> - {{ $message->approved_at->format('Y-m-d H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <!-- ملاحظات الإدارة -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">ملاحظات الإدارة</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.contact-messages.update', $message->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الملاحظات الداخلية</label>
                            @if(in_array($message->approval_status, ['approved', 'rejected']))
                                <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 whitespace-pre-wrap min-h-[100px]">{{ $message->admin_notes ?: 'لا توجد ملاحظات' }}</div>
                                <input type="hidden" name="admin_notes" value="{{ $message->admin_notes }}">
                            @else
                                <textarea name="admin_notes" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all" placeholder="أضف ملاحظاتك هنا...">{{ $message->admin_notes }}</textarea>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">الحالة</label>
                                @if(in_array($message->approval_status, ['approved', 'rejected']))
                                    {{-- الحالة للقراءة فقط بعد الموافقة/الرفض --}}
                                    <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-medium">
                                        @if($message->status == 'new')
                                            جديد
                                        @elseif($message->status == 'read')
                                            مقروء
                                        @elseif($message->status == 'in_progress')
                                            قيد المعالجة
                                        @else
                                            مغلق
                                        @endif
                                        <span class="text-xs text-gray-500 mr-2">(تم تحديدها تلقائياً)</span>
                                    </div>
                                    <input type="hidden" name="status" value="{{ $message->status }}">
                                @else
                                    {{-- يمكن التعديل قبل الموافقة/الرفض --}}
                                    <select name="status" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="new" {{ $message->status == 'new' ? 'selected' : '' }}>جديد</option>
                                        <option value="read" {{ $message->status == 'read' ? 'selected' : '' }}>مقروء</option>
                                        <option value="in_progress" {{ $message->status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                                        <option value="closed" {{ $message->status == 'closed' ? 'selected' : '' }}>مغلق</option>
                                    </select>
                                @endif
                            </div>
                            
                            @can('assign_contact_messages')
                                @if(in_array($message->approval_status, ['approved', 'rejected']))
                                    {{-- عرض المكلف للقراءة فقط بعد الموافقة/الرفض --}}
                                    @if($message->assignedUser)
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">مكلف إلى</label>
                                        <div class="flex items-center gap-2 text-gray-900 bg-gray-100 px-4 py-3 rounded-lg">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="font-medium">{{ $message->assignedUser->name }}</span>
                                            <span class="text-xs text-gray-500">(لا يمكن التغيير)</span>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    {{-- يمكن التعديل قبل الموافقة/الرفض --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">تكليف إلى</label>
                                        <select name="assigned_to" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                            <option value="">غير مكلف</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $message->assigned_to == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @else
                                {{-- للمستخدمين بدون صلاحية - عرض فقط --}}
                                @if($message->assignedUser)
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">مكلف إلى</label>
                                    <div class="flex items-center gap-2 text-gray-900 bg-gray-100 px-4 py-3 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">{{ $message->assignedUser->name }}</span>
                                    </div>
                                </div>
                                @endif
                            @endcan
                        </div>

                        @if(!in_array($message->approval_status, ['approved', 'rejected']))
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            حفظ التحديثات
                        </button>
                        @else
                        <div class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-600 font-medium rounded-lg flex items-center justify-center gap-2 border border-gray-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            لا يمكن التعديل بعد {{ $message->approval_status == 'approved' ? 'الموافقة' : 'الرفض' }}
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- معلومات الرسالة -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        معلومات الرسالة
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="text-xs font-semibold text-blue-600 uppercase mb-1">رقم الرسالة</div>
                        <div class="text-2xl font-bold text-gray-900">#{{ $message->id }}</div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            تاريخ الإرسال
                        </div>
                        <div class="text-sm font-semibold text-gray-900">{{ $message->created_at->format('Y-m-d H:i') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $message->created_at->diffForHumans() }}</div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-2">حالة القراءة</div>
                        @if($message->read_at)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 mb-2">
                                <svg class="w-4 h-4 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                مقروء
                            </span>
                            <div class="text-xs text-gray-500">{{ $message->read_at->format('Y-m-d H:i') }}</div>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                <svg class="w-4 h-4 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                غير مقروء
                            </span>
                        @endif
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-2">حالة الموافقة</div>
                        @if($message->approval_status == 'pending')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                معلقة
                            </span>
                        @elseif($message->approval_status == 'forwarded')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                محولة للمسؤول
                            </span>
                        @elseif($message->approval_status == 'approved')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                موافق عليها
                            </span>
                            @if($message->approver)
                                <div class="text-xs text-gray-500 mt-2">بواسطة: {{ $message->approver->name }}</div>
                                <div class="text-xs text-gray-500">{{ $message->approved_at->diffForHumans() }}</div>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                مرفوضة
                            </span>
                            @if($message->approver)
                                <div class="text-xs text-gray-500 mt-2">بواسطة: {{ $message->approver->name }}</div>
                                <div class="text-xs text-gray-500">{{ $message->approved_at->diffForHumans() }}</div>
                            @endif
                        @endif
                    </div>
                    
                    @if($message->assignedUser)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-2">مكلف إلى</div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ mb_substr($message->assignedUser->name, 0, 1) }}
                                </div>
                                <div class="text-sm font-semibold text-gray-900">{{ $message->assignedUser->name }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- رابط المراجعة للمدراء -->
            @can('assign_contact_messages')
            @if($message->approval_status == 'forwarded' && $message->assigned_to == auth()->id())
            <div class="bg-white rounded-lg shadow-sm border-2 border-blue-500 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        رسالة تحتاج مراجعتك
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">هذه الرسالة محولة إليك للمراجعة والموافقة أو الرفض</p>
                    <a href="{{ route('admin.contact-messages.review.show', $message->id) }}" class="w-full inline-flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold rounded-lg transition-colors duration-200 gap-2 shadow-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        انتقل لصفحة المراجعة
                    </a>
                </div>
            </div>
            @endif
            @endcan

            <!-- إجراءات سريعة -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        إجراءات سريعة
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="mailto:{{ $message->email }}?subject=رد على: {{ $message->subject }}" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        إرسال بريد
                    </a>
                    
                    <a href="tel:{{ $message->phone }}" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        اتصال
                    </a>
                    
                    @can('delete_contact_messages')
                    <div class="border-t border-gray-200 my-4"></div>
                    
                    <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف الرسالة
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
