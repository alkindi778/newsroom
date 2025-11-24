@extends('admin.layouts.app')

@section('title', 'معاينة قالب البريد')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">معاينة قالب البريد الإلكتروني</h1>
            <p class="text-sm text-gray-600">هكذا ستظهر الرسالة للمستلم</p>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors">
            العودة
        </a>
    </div>

    <!-- Preview Frame -->
    <div class="bg-gray-200 rounded-xl p-4 shadow-inner">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Email Client Header Mock -->
            <div class="bg-gray-100 border-b px-4 py-3 flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                </div>
                <div class="flex-1 text-center">
                    <span class="text-sm text-gray-500">معاينة البريد الإلكتروني</span>
                </div>
            </div>
            
            <!-- Email Content -->
            <iframe srcdoc="{{ $emailHtml }}" style="width: 100%; height: 800px; border: none;"></iframe>
        </div>
    </div>
</div>
@endsection
