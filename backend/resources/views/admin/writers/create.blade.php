@extends('admin.layouts.app')

@section('title', 'إضافة كاتب جديد')
@section('page-title', 'إضافة كاتب جديد')

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    <x-admin.alert-messages />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إضافة كاتب جديد</h1>
            <p class="mt-1 text-sm text-gray-600">أدخل معلومات الكاتب الجديد</p>
        </div>
        
        <a href="{{ route('admin.writers.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('admin.writers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الأساسية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            اسم الكاتب <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name"
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="أدخل اسم الكاتب"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               value="{{ old('email') }}"
                               class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('email') ? 'border border-red-500' : 'border border-gray-300' }}"
                               placeholder="example@domain.com">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                        <input type="text" 
                               name="phone" 
                               id="phone"
                               value="{{ old('phone') }}"
                               class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('phone') ? 'border border-red-500' : 'border border-gray-300' }}"
                               placeholder="05xxxxxxxx">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">المنصب</label>
                        <input type="text" 
                               name="position" 
                               id="position"
                               value="{{ old('position') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('position') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="محرر، كاتب صحفي، إلخ...">
                        @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Specialization -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">التخصص</label>
                        <input type="text" 
                               name="specialization" 
                               id="specialization"
                               value="{{ old('specialization') }}"
                               class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('specialization') ? 'border border-red-500' : 'border border-gray-300' }}"
                               placeholder="السياسة، الاقتصاد، الرياضة، إلخ...">
                        @error('specialization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="is_active" 
                                id="is_active"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('is_active') ? 'border-red-500' : 'border-gray-300' }}">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">السيرة الذاتية</label>
                    <textarea name="bio" 
                              id="bio"
                              rows="4"
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('bio') ? 'border-red-500' : 'border-gray-300' }}"
                              placeholder="نبذة مختصرة عن الكاتب...">{{ old('bio') }}</textarea>
                    @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image using Media Picker -->
                <div class="mt-6">
                    {{-- Use Media Picker Component --}}
                    <x-admin.media-picker 
                        field="image" 
                        label="صورة الكاتب" 
                        :value="old('image', '')"
                        collection="writers"
                        help="اختر صورة شخصية للكاتب من مكتبة الوسائط أو ارفع صورة جديدة. يُفضل أن تكون مربعة الشكل" />
                </div>
            </div>

            <!-- Social Links -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">الروابط الاجتماعية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Facebook -->
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"></path>
                            </svg>
                            فيسبوك
                        </label>
                        <input type="url" 
                               name="facebook_url" 
                               id="facebook_url"
                               value="{{ old('facebook_url') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('facebook_url') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="https://facebook.com/username">
                        @error('facebook_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Twitter -->
                    <div>
                        <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                            </svg>
                            تويتر (X)
                        </label>
                        <input type="url" 
                               name="twitter_url" 
                               id="twitter_url"
                               value="{{ old('twitter_url') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('twitter_url') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="https://twitter.com/username">
                        @error('twitter_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- LinkedIn -->
                    <div>
                        <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                            </svg>
                            لينكد إن
                        </label>
                        <input type="url" 
                               name="linkedin_url" 
                               id="linkedin_url"
                               value="{{ old('linkedin_url') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('linkedin_url') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="https://linkedin.com/in/username">
                        @error('linkedin_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9-9a9 9 0 00-9 9m0 0a9 9 0 009 9"></path>
                            </svg>
                            الموقع الشخصي
                        </label>
                        <input type="url" 
                               name="website_url" 
                               id="website_url"
                               value="{{ old('website_url') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm {{ $errors->has('website_url') ? 'border-red-500' : 'border-gray-300' }}"
                               placeholder="https://example.com">
                        @error('website_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex flex-col sm:flex-row justify-between gap-3">
                <a href="{{ route('admin.writers.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    إلغاء
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    حفظ الكاتب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
