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
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>
    
    <!-- Grid Pattern -->
    <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:50px_50px]"></div>
    
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8 space-y-4">
                @php
                    $logo = $settingsGeneral['site_logo'] ?? '';
                @endphp
                @if($logo)
                    <div class="inline-block bg-white rounded-2xl p-4 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="h-16 w-auto">
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-red-500 rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-newspaper text-3xl text-white"></i>
                    </div>
                @endif
                <h1 class="text-3xl font-bold text-white drop-shadow-lg">{{ $siteName }}</h1>
                <p class="text-blue-200 text-sm">@yield('subtitle', 'لوحة التحكم')</p>
            </div>
            
            <!-- Auth Card -->
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/20">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-8 space-y-2">
                <p class="text-blue-200 text-sm">
                    © {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
                </p>
                <p class="text-blue-300 text-xs">
                    <i class="fas fa-shield-alt"></i> نظام محمي ومشفر
                </p>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }
        .animate-blob {
            animation: blob 10s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        .bg-grid-white\/\[0\.02\] {
            background-image: linear-gradient(white 1px, transparent 1px),
                              linear-gradient(90deg, white 1px, transparent 1px);
        }
    </style>
</body>
</html>
