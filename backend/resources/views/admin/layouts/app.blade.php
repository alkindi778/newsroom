<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    @php
        $settingsGeneral = \App\Models\SiteSetting::where('group', 'general')->pluck('value', 'key');
        $siteName = $settingsGeneral['site_name'] ?? ' ';
    @endphp
    <title>@yield('title', 'لوحة التحكم') - {{ $siteName }}</title>
    
    <!-- Cairo Font (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/cairo/cairo.css') }}">
    
    <!-- FontAwesome Icons (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/fontawesome-free-6.4.0-web/css/all.min.css') }}">
    
    <!-- Vite CSS (Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 antialiased">
    <div class="min-h-screen">
        <!-- Include Sidebar Component -->
        @include('admin.components.sidebar')
        
        <!-- Main Content -->
        <main class="lg:mr-72">
            <!-- Include Header Component -->
            @include('admin.components.header')
            
            <!-- Page Content -->
            <div class="p-4 lg:p-6">
                <!-- Include Alerts Component -->
                @include('admin.components.alerts')

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-white/10 backdrop-blur-md z-40 lg:hidden hidden" id="sidebar-overlay"></div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const sidebarClose = document.getElementById('sidebar-close');
            
            function closeSidebar() {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
            
            function openSidebar() {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            }
            
            // Mobile menu toggle
            if (mobileMenuButton && sidebar && overlay) {
                mobileMenuButton.addEventListener('click', function() {
                    if (sidebar.classList.contains('translate-x-full')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                });
                
                // Close button in sidebar
                if (sidebarClose) {
                    sidebarClose.addEventListener('click', closeSidebar);
                }
                
                // Close sidebar when clicking overlay
                overlay.addEventListener('click', closeSidebar);
                
                // Close sidebar when pressing Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeSidebar();
                    }
                });
            }
        });
    </script>
    
    <!-- Include modals before scripts -->
    @stack('modals')
    
    <!-- Notifications System -->
    <script src="{{ asset('js/notifications.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
