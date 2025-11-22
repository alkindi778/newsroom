@extends('admin.layouts.auth')

@section('title', 'نسيت كلمة المرور')
@section('subtitle', 'استعادة كلمة المرور')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">نسيت كلمة المرور؟</h2>
        <p class="text-gray-600 text-sm mt-2">
            لا مشكلة. أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.
        </p>
    </div>

    @if (session('status'))
        <div class="bg-green-50 border border-green-200 p-3 rounded-lg">
            <p class="text-sm text-green-800">
                {{ session('status') }}
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

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
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                إرسال رابط إعادة التعيين
            </button>
        </div>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-right ml-1"></i>
            العودة لتسجيل الدخول
        </a>
    </div>
</div>
@endsection
