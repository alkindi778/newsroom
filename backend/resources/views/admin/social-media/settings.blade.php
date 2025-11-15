@extends('admin.layouts.app')

@section('title', 'إعدادات وسائل التواصل الاجتماعي')
@section('page-title', 'إعدادات وسائل التواصل الاجتماعي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 -mx-6 -my-6 px-6 py-6">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إعدادات وسائل التواصل الاجتماعي</h1>
            <p class="mt-1 text-sm text-gray-600">تحكم في إعدادات النشر التلقائي على المنصات المختلفة</p>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="mr-3">
                <h3 class="text-sm font-medium text-red-800">حدثت أخطاء:</h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if (session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="mr-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Facebook Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <h2 class="text-xl font-bold text-white mr-3">Facebook</h2>
                </div>
                <span class="px-3 py-1 {{ $platforms['facebook']['enabled'] ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }} text-sm font-semibold rounded-full">
                    {{ $platforms['facebook']['enabled'] ? '✓ مفعّل' : '✗ معطّل' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.social-media.update-settings') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="platform" value="facebook">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Enable/Disable -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="enabled" value="1" {{ $platforms['facebook']['enabled'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">تفعيل النشر على Facebook</span>
                    </label>
                </div>

                <!-- Page ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">معرّف الصفحة (Page ID)</label>
                    <input type="text" name="page_id" value="{{ $platforms['facebook']['page_id'] }}" 
                           placeholder="أدخل معرّف الصفحة"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Access Token -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز الوصول (Access Token)</label>
                    <input type="password" name="access_token" value="{{ $platforms['facebook']['access_token'] }}" 
                           placeholder="أدخل رمز الوصول"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Auto Publish -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_publish" value="1" {{ $platforms['facebook']['auto_publish'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">نشر تلقائي عند إنشاء مقالة</span>
                    </label>
                </div>

                <!-- Include Image -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_image" value="1" {{ $platforms['facebook']['include_image'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة الصورة في المنشور</span>
                    </label>
                </div>

                <!-- Include Link -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_link" value="1" {{ $platforms['facebook']['include_link'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة رابط الخبر</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>

    <!-- Twitter Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-400 to-blue-500 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 002.856-3.915 10 10 0 01-2.856.973 5 5 0 00-8.66 4.57c-4.25-.625-8.01-2.247-10.54-5.347a5 5 0 001.54 6.573 5 5 0 01-2.26-.616v.06a5 5 0 004.002 4.905 5 5 0 01-2.26.086 5 5 0 004.68 3.476 10 10 0 01-6.177 2.13c-.398 0-.79-.023-1.175-.067a14.047 14.047 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    <h2 class="text-xl font-bold text-white mr-3">Twitter</h2>
                </div>
                <span class="px-3 py-1 {{ $platforms['twitter']['enabled'] ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }} text-sm font-semibold rounded-full">
                    {{ $platforms['twitter']['enabled'] ? '✓ مفعّل' : '✗ معطّل' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.social-media.update-settings') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="platform" value="twitter">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Enable/Disable -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="enabled" value="1" {{ $platforms['twitter']['enabled'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-400 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">تفعيل النشر على Twitter</span>
                    </label>
                </div>

                <!-- Bearer Token -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bearer Token</label>
                    <input type="password" name="bearer_token" value="{{ $platforms['twitter']['bearer_token'] }}" 
                           placeholder="أدخل Bearer Token"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                </div>

                <!-- Auto Publish -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_publish" value="1" {{ $platforms['twitter']['auto_publish'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-400 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">نشر تلقائي</span>
                    </label>
                </div>

                <!-- Include Image -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_image" value="1" {{ $platforms['twitter']['include_image'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-400 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة الصورة</span>
                    </label>
                </div>

                <!-- Include Link -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_link" value="1" {{ $platforms['twitter']['include_link'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-400 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة الرابط</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>

    <!-- Telegram Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a11.955 11.955 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.485-1.313.474-.431-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.15.321-.465.922-.910 3.6-2.755 5.404-4.069 6.56-4.947.333-.231.626-.326.846-.346z"/>
                    </svg>
                    <h2 class="text-xl font-bold text-white mr-3">Telegram</h2>
                </div>
                <span class="px-3 py-1 {{ $platforms['telegram']['enabled'] ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }} text-sm font-semibold rounded-full">
                    {{ $platforms['telegram']['enabled'] ? '✓ مفعّل' : '✗ معطّل' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.social-media.update-settings') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="platform" value="telegram">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Enable/Disable -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="enabled" value="1" {{ $platforms['telegram']['enabled'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-cyan-500 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">تفعيل النشر على Telegram</span>
                    </label>
                </div>

                <!-- Bot Token -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز البوت (Bot Token)</label>
                    <input type="password" name="bot_token" value="{{ $platforms['telegram']['bot_token'] }}" 
                           placeholder="أدخل رمز البوت"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>

                <!-- Channel ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">معرّف القناة (Channel ID)</label>
                    <input type="text" name="channel_id" value="{{ $platforms['telegram']['channel_id'] }}" 
                           placeholder="أدخل معرّف القناة"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>

                <!-- Auto Publish -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_publish" value="1" {{ $platforms['telegram']['auto_publish'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-cyan-500 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">نشر تلقائي</span>
                    </label>
                </div>

                <!-- Include Image -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_image" value="1" {{ $platforms['telegram']['include_image'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-cyan-500 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة الصورة</span>
                    </label>
                </div>

                <!-- Include Link -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_link" value="1" {{ $platforms['telegram']['include_link'] ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-cyan-500 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm font-medium text-gray-700">إضافة الرابط</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
