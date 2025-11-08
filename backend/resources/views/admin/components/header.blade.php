<!-- Top Navigation -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center">
            <!-- Mobile Menu Button -->
            <button class="lg:hidden p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" 
                    id="mobile-menu-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Page Title -->
            <h1 class="mr-4 text-xl font-semibold text-gray-800">
                @yield('page-title', 'لوحة التحكم')
            </h1>
            
            <!-- Breadcrumb -->
            @if(isset($breadcrumbs))
                <nav class="hidden md:flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 space-x-reverse text-sm text-gray-500">
                        @foreach($breadcrumbs as $breadcrumb)
                            <li>
                                @if($loop->last)
                                    <span class="text-gray-900">{{ $breadcrumb['title'] }}</span>
                                @else
                                    <a href="{{ $breadcrumb['url'] }}" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $breadcrumb['title'] }}
                                    </a>
                                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif
        </div>
        
        <div class="flex items-center space-x-4 space-x-reverse">
            <!-- Search (Optional) -->
            <div class="hidden lg:block">
                <div class="relative">
                    <input type="text" 
                           placeholder="بحث..." 
                           class="w-48 lg:w-64 pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <svg class="w-4 h-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Mobile Search Button -->
            <div class="lg:hidden">
                <button class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Notifications -->
            <div class="relative" id="notifications-wrapper">
                <button id="notifications-button" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 relative transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span id="notifications-badge" class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center hidden">0</span>
                </button>
                
                <!-- Dropdown -->
                <div id="notifications-dropdown" class="hidden absolute left-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50" style="max-height: 500px; overflow-y: auto;">
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">الإشعارات</h3>
                        <button id="mark-all-read" class="text-sm text-blue-600 hover:text-blue-700">
                            تحديد الكل كمقروء
                        </button>
                    </div>
                    
                    <!-- Notifications List -->
                    <div id="notifications-list" class="divide-y divide-gray-100">
                        <!-- سيتم ملؤها بواسطة JavaScript -->
                        <div class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p>لا توجد إشعارات</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                        <button id="clear-all-notifications" class="text-sm text-red-600 hover:text-red-700">
                            مسح جميع الإشعارات
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- View Site -->
            <a href="{{ url('/') }}" 
               target="_blank" 
               class="hidden lg:inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                عرض الموقع
            </a>
            
            <!-- Mobile View Site Button -->
            <a href="{{ url('/') }}" 
               target="_blank" 
               class="lg:hidden p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
               title="عرض الموقع">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
            
            <!-- Current Time -->
            <div class="hidden md:block text-sm text-gray-600">
                <div class="font-medium">{{ now()->format('H:i') }}</div>
                <div class="text-xs">{{ now()->format('Y/m/d') }}</div>
            </div>
            
            <!-- Mobile Time -->
            <div class="md:hidden text-xs text-gray-600 font-medium">
                {{ now()->format('H:i') }}
            </div>
        </div>
    </div>
</header>
