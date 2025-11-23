<!-- Quick Actions Section -->
<div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            اختصارات سريعة
        </h3>
    </div>
    
    <!-- Quick Action Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
        
        <!-- إضافة خبر -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('articles.create'))
        <a href="{{ route('admin.articles.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 rounded-lg transition-all duration-200 hover:shadow-md border border-red-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-red-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-red-700 text-center">إضافة خبر</span>
        </a>
        @endif
        
        <!-- إضافة مقال رأي -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('opinions.create'))
        <a href="{{ route('admin.opinions.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 rounded-lg transition-all duration-200 hover:shadow-md border border-orange-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-orange-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-orange-700 text-center">إضافة مقال رأي</span>
        </a>
        @endif
        
        <!-- إضافة كاتب رأي -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('writers.create'))
        <a href="{{ route('admin.writers.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-lg transition-all duration-200 hover:shadow-md border border-purple-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-purple-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-purple-700 text-center">إضافة كاتب</span>
        </a>
        @endif
        
        <!-- إضافة فيديو -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('videos.create'))
        <a href="{{ route('admin.videos.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-lg transition-all duration-200 hover:shadow-md border border-blue-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-blue-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-blue-700 text-center">إضافة فيديو</span>
        </a>
        @endif
        
        <!-- إضافة إعلان -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('advertisements.create'))
        <a href="{{ route('admin.advertisements.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-lg transition-all duration-200 hover:shadow-md border border-green-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-green-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-green-700 text-center">إضافة إعلان</span>
        </a>
        @endif
        
        <!-- إضافة قسم -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('categories.create'))
        <a href="{{ route('admin.categories.create') }}" 
           class="group flex flex-col items-center justify-center p-4 lg:p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-lg transition-all duration-200 hover:shadow-md border border-indigo-200">
            <div class="flex items-center justify-center w-12 h-12 lg:w-14 lg:h-14 bg-indigo-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <span class="text-xs lg:text-sm font-semibold text-indigo-700 text-center">إضافة قسم</span>
        </a>
        @endif
        
    </div>
</div>
