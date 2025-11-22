@extends('admin.layouts.auth')

@section('title', 'تعيين كلمة المرور الجديدة')
@section('subtitle', 'إنشاء كلمة مرور جديدة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">تعيين كلمة المرور</h2>
        <p class="text-gray-600 text-sm mt-2">
            الرجاء إدخال كلمة المرور الجديدة أدناه.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                البريد الإلكتروني
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}" 
                   required 
                   readonly
                   class="block w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-not-allowed"
                   placeholder="admin@example.com">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                كلمة المرور الجديدة
            </label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required 
                   autofocus
                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                   placeholder="أدخل كلمة المرور الجديدة">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                تأكيد كلمة المرور
            </label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required 
                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="أعد إدخال كلمة المرور">
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                حفظ كلمة المرور الجديدة
            </button>
        </div>
    </form>
</div>
@endsection
