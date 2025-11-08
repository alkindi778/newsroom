@extends('admin.layouts.auth')

@section('title', 'المصادقة الثنائية')
@section('subtitle', 'أدخل رمز التحقق للمتابعة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">المصادقة الثنائية</h2>
        <p class="text-gray-600">أدخل الرمز المكون من 6 أرقام من تطبيق المصادقة</p>
    </div>

    <!-- Authentication Code Form -->
    <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                رمز المصادقة
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input type="text" 
                       id="code" 
                       name="code" 
                       maxlength="6"
                       autofocus 
                       autocomplete="one-time-code"
                       class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 text-center text-lg tracking-widest @error('code') border-red-300 @enderror"
                       placeholder="000000">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            التحقق من الرمز
        </button>
    </form>

    <!-- Divider -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">أو</span>
        </div>
    </div>

    <!-- Recovery Code Form -->
    <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-2">
                رمز الاسترداد
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <input type="text" 
                       id="recovery_code" 
                       name="recovery_code" 
                       autocomplete="one-time-code"
                       class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('recovery_code') border-red-300 @enderror"
                       placeholder="أدخل رمز الاسترداد">
                @error('recovery_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            استخدام رمز الاسترداد
        </button>
    </form>

    <!-- Help Text -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="mr-3">
                <p class="text-sm text-yellow-800">
                    <span class="font-medium">تواجه مشكلة؟</span> 
                    استخدم رمز الاسترداد الذي حفظته أثناء إعداد المصادقة الثنائية.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
