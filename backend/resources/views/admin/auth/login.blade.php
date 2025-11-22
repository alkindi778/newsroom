@extends('admin.layouts.auth')

@section('title', 'تسجيل الدخول')
@section('subtitle', 'أدخل بياناتك للوصول إلى لوحة التحكم')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">تسجيل الدخول</h2>
        <p class="text-gray-600 text-sm mt-2">أدخل بياناتك للوصول إلى لوحة التحكم</p>
    </div>
    
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 p-3 rounded-lg">
            <p class="text-sm text-red-800">
                {{ $errors->first() }}
            </p>
        </div>
    @endif
    
    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                البريد الإلكتروني
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                   placeholder="admin@newsroom.com">
        </div>
        
        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                كلمة المرور
            </label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required
                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                   placeholder="••••••••">
        </div>
        
        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input id="remember" name="remember" type="checkbox" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <span class="mr-2 text-sm text-gray-700">
                    تذكرني
                </span>
            </label>
            
            <a href="{{ route('password.request') }}" 
               class="text-sm text-blue-600 hover:text-blue-800">
                نسيت كلمة المرور؟
            </a>
        </div>
        
        <!-- Submit Button -->
        <div>
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                تسجيل الدخول
            </button>
        </div>
    </form>
    
</div>
@endsection
