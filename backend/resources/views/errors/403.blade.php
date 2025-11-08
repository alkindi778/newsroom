@extends('admin.layouts.app')

@section('title', 'ليس لديك صلاحية')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- خطأ 403 Icon -->
            <svg class="mx-auto h-20 w-20 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            
            <!-- رقم الخطأ -->
            <h1 class="mt-6 text-6xl font-bold text-gray-900">403</h1>
            
            <!-- رسالة الخطأ -->
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                ليس لديك صلاحية
            </h2>
            
            <!-- وصف الخطأ -->
            <p class="mt-4 text-lg text-gray-600">
                عذراً، لا تملك الصلاحية المطلوبة للوصول إلى هذه الصفحة.
                <br>
                يرجى التواصل مع الإدارة إذا كنت تعتقد أن هذا خطأ.
            </p>
            
            <!-- أزرار التنقل -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    العودة للرئيسية
                </a>
                
                <button onclick="history.back()" 
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    الرجوع للخلف
                </button>
            </div>
            
            <!-- معلومات إضافية -->
            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 mb-2">
                    💡 نصائح مفيدة:
                </h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>تأكد من أن لديك الدور المناسب</li>
                    <li>تواصل مع المدير لطلب الصلاحيات</li>
                    <li>تحقق من صحة الرابط المدخل</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
