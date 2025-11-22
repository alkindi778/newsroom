@extends('admin.layouts.auth')

@section('title', 'تسجيل الدخول')
@section('subtitle', 'أدخل بياناتك للوصول إلى لوحة التحكم')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
            <i class="fas fa-user-shield text-2xl text-white"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">مرحباً بعودتك</h2>
        <p class="text-gray-600">أدخل بياناتك للوصول إلى لوحة التحكم</p>
    </div>
    
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-r-4 border-red-500 p-4 rounded-xl shadow-md animate-shake">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm text-red-900 font-semibold">
                        {{ $errors->first() }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
        <!-- Email Field -->
        <div class="group">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-envelope text-blue-500 ml-1"></i>
                البريد الإلكتروني
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-at text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                </div>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="block w-full pr-11 pl-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white @error('email') border-red-300 @enderror"
                       placeholder="admin@newsroom.com">
            </div>
        </div>
        
        <!-- Password Field -->
        <div class="group">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock text-blue-500 ml-1"></i>
                كلمة المرور
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-key text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                </div>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="block w-full pr-11 pl-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white @error('password') border-red-300 @enderror"
                       placeholder="••••••••">
            </div>
        </div>
        
        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-2">
            <label class="flex items-center cursor-pointer group">
                <input id="remember" name="remember" type="checkbox" 
                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-md transition-all duration-200 cursor-pointer">
                <span class="mr-2 text-sm text-gray-700 group-hover:text-gray-900 font-medium">
                    <i class="fas fa-check-circle text-blue-500 ml-1"></i>
                    تذكرني
                </span>
            </label>
            
            <div class="text-sm">
                <a href="{{ route('password.request') }}" 
                   class="font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200 flex items-center group">
                    <i class="fas fa-question-circle ml-1 group-hover:rotate-12 transition-transform duration-200"></i>
                    نسيت كلمة المرور؟
                </a>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    class="group relative w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl active:scale-[0.98]">
                <i class="fas fa-sign-in-alt text-lg group-hover:translate-x-1 transition-transform duration-200"></i>
                <span>تسجيل الدخول</span>
            </button>
        </div>
    </form>
    
    <!-- Security Notice -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-4 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-shield-alt text-white text-lg"></i>
                </div>
            </div>
            <div class="mr-3 flex-1">
                <h4 class="text-sm font-bold text-blue-900 mb-1">
                    <i class="fas fa-check-circle text-green-500 ml-1"></i>
                    نظام أمان متقدم
                </h4>
                <p class="text-xs text-blue-800 leading-relaxed">
                    يدعم هذا النظام المصادقة الثنائية وحماية ضد الهجمات المتكررة
                </p>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .animate-shake {
            animation: shake 0.5s;
        }
    </style>
</div>
@endsection
