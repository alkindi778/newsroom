<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
    <!-- Total Articles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center">
            <div class="p-2 lg:p-3 rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="mr-3 lg:mr-4 min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 truncate">إجمالي الأخبار</p>
                <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['articles_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center">
            <div class="p-2 lg:p-3 rounded-full bg-green-100 text-green-600 flex-shrink-0">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <div class="mr-3 lg:mr-4 min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 truncate">الأقسام</p>
                <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['categories_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center">
            <div class="p-2 lg:p-3 rounded-full bg-purple-100 text-purple-600 flex-shrink-0">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <div class="mr-3 lg:mr-4 min-w-0 flex-1">
                <p class="text-xs lg:text-sm font-medium text-gray-600 truncate">المستخدمين</p>
                <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['users_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>

</div>
