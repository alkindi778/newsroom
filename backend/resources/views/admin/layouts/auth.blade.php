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
<body class="min-h-screen bg-gray-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                @php
                    $logo = $settingsGeneral['site_logo'] ?? '';
                @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="h-16 w-auto mx-auto mb-4">
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-lg mb-4">
                        <i class="fas fa-newspaper text-2xl text-white"></i>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-gray-900">{{ $siteName }}</h1>
                <p class="text-gray-600 text-sm mt-1">@yield('subtitle', 'لوحة التحكم')</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-gray-500 text-xs">
                    © {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
