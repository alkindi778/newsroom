<!-- Enhanced Sidebar with Modern Design & Mobile Support -->
<aside class="w-72 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white shadow-2xl fixed inset-y-0 right-0 z-50 transform translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 overflow-hidden" id="sidebar">
    <!-- Decorative gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 pointer-events-none"></div>
    
    <div class="flex flex-col h-full relative z-10">
        <!-- Logo Header -->
        <div class="flex items-center justify-between px-5 h-20 bg-gradient-to-l from-blue-800/50 to-transparent border-b border-blue-700/30 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold bg-gradient-to-l from-blue-200 to-white bg-clip-text text-transparent">غرفة الأخبار</h1>
                    <p class="text-xs text-blue-300/70">لوحة التحكم</p>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button class="lg:hidden text-blue-300 hover:text-white hover:rotate-90 transition-all" id="sidebar-close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto sidebar-scroll">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-l from-blue-600 to-blue-700 shadow-lg shadow-blue-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="flex-1">لوحة التحكم</span>
                        @if(request()->routeIs('admin.dashboard'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @can('view_newspaper_issues')
                <li>
                    <a href="{{ route('admin.newspaper-issues.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.newspaper-issues.*') ? 'bg-gradient-to-l from-blue-600 to-blue-700 shadow-lg shadow-blue-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.newspaper-issues.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h11a1 1 0 011 1v13a1 1 0 01-1 1H6a2 2 0 01-2-2V6zm3 1h8M7 10h8M7 13h4" />
                            </svg>
                        </div>
                        <span class="flex-1">إصدارات الصحف</span>
                        @if(request()->routeIs('admin.newspaper-issues.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                <li>
                    <a href="{{ route('admin.articles.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.articles.*') ? 'bg-gradient-to-l from-blue-600 to-blue-700 shadow-lg shadow-blue-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.articles.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الاخبار</span>
                        @if(request()->routeIs('admin.articles.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @can('view_categories')
                <li>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-l from-blue-600 to-blue-700 shadow-lg shadow-blue-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الأقسام</span>
                        @if(request()->routeIs('admin.categories.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                
                <!-- Writers & Opinions Section -->
                @canany(['عرض كُتاب الرأي', 'عرض مقالات الرأي'])
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-purple-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-purple-300/80 uppercase tracking-wider">مقالات الرأي</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-purple-500/30 to-transparent"></div>
                    </div>
                </li>
                @endcanany
                @can('عرض كُتاب الرأي')
                <li>
                    <a href="{{ route('admin.writers.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.writers.*') ? 'bg-gradient-to-l from-purple-600 to-purple-700 shadow-lg shadow-purple-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.writers.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">كُتاب الرأي</span>
                        @if(request()->routeIs('admin.writers.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                @can('عرض مقالات الرأي')
                <li>
                    <a href="{{ route('admin.opinions.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.opinions.*') ? 'bg-gradient-to-l from-purple-600 to-purple-700 shadow-lg shadow-purple-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.opinions.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">مقالات الرأي</span>
                        @if(request()->routeIs('admin.opinions.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                
                <!-- Media Library Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-green-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-green-300/80 uppercase tracking-wider">الوسائط</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-green-500/30 to-transparent"></div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.media.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.media.*') ? 'bg-gradient-to-l from-green-600 to-green-700 shadow-lg shadow-green-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.media.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">مكتبة الصور</span>
                        @if(request()->routeIs('admin.media.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.videos.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.videos.*') ? 'bg-gradient-to-l from-green-600 to-green-700 shadow-lg shadow-green-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.videos.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الفيديوهات</span>
                        @if(request()->routeIs('admin.videos.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                
                @can('view_advertisements')
                <!-- Advertisements Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-purple-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-purple-300/80 uppercase tracking-wider">الإعلانات</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-purple-500/30 to-transparent"></div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.advertisements.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.advertisements.*') ? 'bg-gradient-to-l from-purple-600 to-purple-700 shadow-lg shadow-purple-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.advertisements.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الإعلانات</span>
                        @if(request()->routeIs('admin.advertisements.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                
                <!-- Contact Messages Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-cyan-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-cyan-300/80 uppercase tracking-wider">التواصل</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-cyan-500/30 to-transparent"></div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.contact-messages.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.contact-messages.*') ? 'bg-gradient-to-l from-cyan-600 to-cyan-700 shadow-lg shadow-cyan-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.contact-messages.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="flex-1">رسائل التواصل</span>
                        @php
                            $newMessagesCount = \App\Models\ContactMessage::where('status', 'new')->count();
                        @endphp
                        @if($newMessagesCount > 0)
                        <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">{{ $newMessagesCount }}</span>
                        @endif
                        @if(request()->routeIs('admin.contact-messages.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                
                <!-- رسائلي للمراجعة (للمدراء فقط) -->
                @can('assign_contact_messages')
                <li>
                    <a href="{{ route('admin.contact-messages.review.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.contact-messages.review.*') ? 'bg-gradient-to-l from-blue-600 to-blue-700 shadow-lg shadow-blue-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.contact-messages.review.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="flex-1">رسائل للمراجعة</span>
                        @php
                            $myPendingCount = \App\Models\ContactMessage::assignedTo(auth()->id())->where('approval_status', 'forwarded')->count();
                        @endphp
                        @if($myPendingCount > 0)
                        <span class="px-2 py-0.5 bg-blue-500 text-white text-xs font-bold rounded-full">{{ $myPendingCount }}</span>
                        @endif
                        @if(request()->routeIs('admin.contact-messages.review.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                
                @canany(['view_users', 'manage_roles', 'manage_permissions'])
                <!-- System Management Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-amber-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-amber-300/80 uppercase tracking-wider">النظام</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-amber-500/30 to-transparent"></div>
                    </div>
                </li>
                @can('view_users')
                <li>
                    <a href="{{ route('admin.users.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-l from-amber-600 to-amber-700 shadow-lg shadow-amber-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة المستخدمين</span>
                        @if(request()->routeIs('admin.users.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                @can('manage_roles')
                <li>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-gradient-to-l from-amber-600 to-amber-700 shadow-lg shadow-amber-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الأدوار</span>
                        @if(request()->routeIs('admin.roles.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                @can('manage_permissions')
                <li>
                    <a href="{{ route('admin.permissions.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.permissions.*') ? 'bg-gradient-to-l from-amber-600 to-amber-700 shadow-lg shadow-amber-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.permissions.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إدارة الصلاحيات</span>
                        @if(request()->routeIs('admin.permissions.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                @endcanany
                
                <!-- Social Media Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-pink-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-pink-300/80 uppercase tracking-wider">التواصل الاجتماعي</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-pink-500/30 to-transparent"></div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.social-media.settings') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.social-media.settings') ? 'bg-gradient-to-l from-pink-600 to-pink-700 shadow-lg shadow-pink-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.social-media.settings') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                            </svg>
                        </div>
                        <span class="flex-1">إعدادات المنصات</span>
                        @if(request()->routeIs('admin.social-media.settings'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.social-media.posts') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.social-media.posts') ? 'bg-gradient-to-l from-pink-600 to-pink-700 shadow-lg shadow-pink-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.social-media.posts') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">المنشورات</span>
                        @if(request()->routeIs('admin.social-media.posts'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>

                <!-- Settings Section -->
                <li class="pt-3 pb-2">
                    <div class="flex items-center gap-2 px-4 py-2">
                        <div class="h-px flex-1 bg-gradient-to-l from-slate-500/30 to-transparent"></div>
                        <span class="text-xs font-bold text-slate-300/80 uppercase tracking-wider">الإعدادات</span>
                        <div class="h-px flex-1 bg-gradient-to-r from-slate-500/30 to-transparent"></div>
                    </div>
                </li>
                @can('view_homepage_sections')
                <li>
                    <a href="{{ route('admin.homepage-sections.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.homepage-sections.*') ? 'bg-gradient-to-l from-indigo-600 to-indigo-700 shadow-lg shadow-indigo-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.homepage-sections.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">قوالب الصفحة الرئيسية</span>
                        @if(request()->routeIs('admin.homepage-sections.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
                <li>
                    <a href="{{ route('admin.settings.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-l from-slate-600 to-slate-700 shadow-lg shadow-slate-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إعدادات الموقع</span>
                        @if(request()->routeIs('admin.settings.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.security.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.security.*') ? 'bg-gradient-to-l from-slate-600 to-slate-700 shadow-lg shadow-slate-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.security.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="flex-1">إعدادات الأمان</span>
                        @if(request()->routeIs('admin.security.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                
                <!-- Trash Section at the end -->
                @canany(['manage_trash', 'delete_articles'])
                <li class="pt-3 pb-2">
                    <div class="h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent"></div>
                </li>
                <li>
                    <a href="{{ route('admin.trash.index') }}" 
                       class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.trash.*') ? 'bg-gradient-to-l from-red-600 to-red-700 shadow-lg shadow-red-500/30' : 'hover:bg-white/5 hover:translate-x-[-4px]' }}">
                        <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('admin.trash.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <span class="flex-1">سلة المهملات</span>
                        @if(request()->routeIs('admin.trash.*'))
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </li>
                @endcan
            </ul>
        </nav>
        
        <!-- User Info & Logout -->
        <div class="border-t border-blue-700/30 p-4 bg-gradient-to-t from-black/20 to-transparent">
            @if(auth()->check())
            <div class="flex items-center gap-3 mb-3 p-2 rounded-xl bg-white/5 backdrop-blur-sm">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-blue-300/70">مرحباً،</div>
                    <div class="text-sm font-bold truncate text-white">{{ auth()->user()->name ?? 'مستخدم' }}</div>
                    <div class="text-xs text-blue-300/90 truncate">{{ auth()->user()->getRoleNames()->first() ?? 'مستخدم' }}</div>
                </div>
            </div>
            @endif
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-l from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-red-500/30 group">
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </div>
</aside>
