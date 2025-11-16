<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <!-- Total Articles -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <p class="text-xs lg:text-sm font-medium opacity-90 mb-1">إجمالي الأخبار</p>
                <p class="text-2xl lg:text-3xl font-bold">{{ $stats['articles_count'] ?? 0 }}</p>
                <p class="text-xs mt-2 opacity-75">
                    <span class="inline-flex items-center">
                        <i class="fas fa-check-circle ml-1"></i>
                        {{ $stats['articles_published'] ?? 0 }} منشور
                    </span>
                </p>
            </div>
            <div class="p-3 lg:p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-newspaper text-2xl lg:text-3xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Videos -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <p class="text-xs lg:text-sm font-medium opacity-90 mb-1">الفيديوهات</p>
                <p class="text-2xl lg:text-3xl font-bold">{{ $stats['videos_count'] ?? 0 }}</p>
                <p class="text-xs mt-2 opacity-75">
                    <span class="inline-flex items-center">
                        <i class="fas fa-play-circle ml-1"></i>
                        محتوى مرئي
                    </span>
                </p>
            </div>
            <div class="p-3 lg:p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-video text-2xl lg:text-3xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Opinions -->
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <p class="text-xs lg:text-sm font-medium opacity-90 mb-1">مقالات الرأي</p>
                <p class="text-2xl lg:text-3xl font-bold">{{ $stats['opinions_count'] ?? 0 }}</p>
                <p class="text-xs mt-2 opacity-75">
                    <span class="inline-flex items-center">
                        <i class="fas fa-pen-fancy ml-1"></i>
                        مقالات
                    </span>
                </p>
            </div>
            <div class="p-3 lg:p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-pen-nib text-2xl lg:text-3xl text-white"></i>
            </div>
        </div>
    </div>

    <!-- Newspaper Issues -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <p class="text-xs lg:text-sm font-medium opacity-90 mb-1">إصدارات الصحيفة</p>
                <p class="text-2xl lg:text-3xl font-bold">{{ $stats['newspaper_issues_count'] ?? 0 }}</p>
                <p class="text-xs mt-2 opacity-75">
                    <span class="inline-flex items-center">
                        <i class="fas fa-book-open ml-1"></i>
                        عدد
                    </span>
                </p>
            </div>
            <div class="p-3 lg:p-4 rounded-full bg-white bg-opacity-20">
                <i class="fas fa-newspaper text-2xl lg:text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <!-- Total Views -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">إجمالي المشاهدات</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_views'] ?? 0) }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-100">
                <i class="fas fa-eye text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Breaking News -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">أخبار عاجلة</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['breaking_news_count'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-full bg-red-100">
                <i class="fas fa-bolt text-xl text-red-600"></i>
            </div>
        </div>
    </div>

    <!-- Pending Articles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">بانتظار الموافقة</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['articles_pending'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100">
                <i class="fas fa-clock text-xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-600 mb-1">المستخدمين</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['users_count'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-full bg-indigo-100">
                <i class="fas fa-users text-xl text-indigo-600"></i>
            </div>
        </div>
    </div>
</div>
