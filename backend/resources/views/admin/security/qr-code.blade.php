@extends('admin.layouts.app')

@section('title', 'رمز QR للمصادقة الثنائية')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('admin.security.index') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى إعدادات الأمان
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">رمز QR للمصادقة الثنائية</h1>
        <p class="text-gray-600 mt-1">امسح الرمز باستخدام تطبيق المصادقة</p>
    </div>

    <!-- QR Code Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 bg-blue-50">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-2xl ml-3"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        خطوات الإعداد
                    </h2>
                    <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>قم بتحميل تطبيق مصادقة مثل Google Authenticator أو Authy على هاتفك</li>
                        <li>افتح التطبيق واضغط على "إضافة حساب" أو زر "+"</li>
                        <li>امسح رمز QR الموجود أدناه</li>
                        <li>أدخل الرمز المكون من 6 أرقام الذي يظهر في التطبيق لتأكيد الإعداد</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- QR Code -->
            <div class="flex flex-col items-center justify-center">
                @if(isset($qrCode) && !empty($qrCode))
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-6">
                        {!! $qrCode !!}
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <p class="text-yellow-800 text-center">
                            <i class="fas fa-exclamation-triangle ml-2"></i>
                            لا يمكن عرض رمز QR. يرجى استخدام الرمز السري أدناه.
                        </p>
                    </div>
                @endif
                
                <!-- Secret Key for Manual Entry -->
                @if(isset($user) && $user->two_factor_secret)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 w-full">
                        <p class="text-sm font-medium text-gray-700 mb-2 text-center">الرمز السري (للإدخال اليدوي):</p>
                        <p class="text-xl font-mono text-center text-blue-600 tracking-wider select-all">
                            {{ decrypt($user->two_factor_secret) }}
                        </p>
                        <p class="text-xs text-gray-500 text-center mt-2">انقر لنسخ الرمز</p>
                    </div>
                @endif
                
                <p class="text-sm text-gray-600 text-center mb-6">
                    إذا لم تتمكن من مسح الرمز، يمكنك إدخال الرمز السري يدوياً في التطبيق
                </p>
            </div>

            <!-- Confirmation Form -->
            <form method="POST" action="{{ route('admin.security.confirm-2fa') }}" class="mt-8 max-w-md mx-auto">
                @csrf
                
                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        أدخل رمز التحقق من التطبيق
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code"
                           placeholder="123456"
                           maxlength="6"
                           class="w-full px-4 py-3 text-center text-2xl tracking-widest border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required
                           autofocus>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-check ml-2"></i>
                    تأكيد وتفعيل المصادقة الثنائية
                </button>
            </form>
        </div>

        <div class="p-6 bg-yellow-50 border-t border-yellow-200">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 ml-2 mt-1"></i>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold mb-1">تحذير مهم:</p>
                    <p>احتفظ برموز الاسترداد في مكان آمن. ستحتاجها في حال فقدان الوصول إلى جهازك.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Supported Apps -->
    <div class="bg-white rounded-lg shadow-md mt-6 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">تطبيقات المصادقة المدعومة:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <i class="fab fa-google text-2xl text-blue-600 ml-3"></i>
                <div>
                    <p class="font-medium text-gray-900">Google Authenticator</p>
                    <p class="text-xs text-gray-600">iOS & Android</p>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <i class="fas fa-shield-alt text-2xl text-red-600 ml-3"></i>
                <div>
                    <p class="font-medium text-gray-900">Authy</p>
                    <p class="text-xs text-gray-600">iOS & Android</p>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <i class="fas fa-key text-2xl text-green-600 ml-3"></i>
                <div>
                    <p class="font-medium text-gray-900">Microsoft Authenticator</p>
                    <p class="text-xs text-gray-600">iOS & Android</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
