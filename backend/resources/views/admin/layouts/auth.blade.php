<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $settingsGeneral = \App\Models\SiteSetting::where('group', 'general')->pluck('value', 'key');
        $siteName = $settingsGeneral['site_name'] ?? '';
    @endphp
    <title>@yield('title', 'المصادقة') - {{ $siteName }}</title>
    
    <!-- Cairo Font (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/cairo/cairo.css') }}">
    
    <!-- FontAwesome Icons (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/fontawesome-free-6.4.0-web/css/all.min.css') }}">
    
    <!-- Vite CSS (Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                    <span class="text-3xl">📰</span>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $siteName }}</h1>
                <p class="text-blue-100">@yield('subtitle', 'نظام إدارة المحتوى')</p>
            </div>
            
            <!-- Auth Card -->
            <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-2xl shadow-2xl p-8">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-blue-100 text-sm">
                    © {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
