<!-- Recent News -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base lg:text-lg font-medium text-gray-900">آخر الأخبار</h3>
        <a href="{{ route('admin.articles.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            <span class="hidden sm:inline">عرض الكل →</span>
            <span class="sm:hidden">الكل</span>
        </a>
    </div>
    
    <div class="divide-y divide-gray-100">
        @forelse($recentArticles ?? [] as $article)
        <div class="flex items-start gap-3 lg:gap-4 py-3 px-2 hover:bg-gray-50 transition-colors duration-200 first:pt-0 last:pb-0">
            <div class="flex-shrink-0">
                @if($article->image)
                <img class="w-14 h-14 lg:w-16 lg:h-16 rounded-lg object-cover shadow-sm" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                @else
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm lg:text-base font-semibold text-gray-900 line-clamp-2 mb-1.5">
                    {{ $article->title }}
                </h4>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fas fa-folder text-[10px] ml-1"></i>
                        {{ $article->category->name ?? 'بدون قسم' }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $article->is_published ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                        <i class="fas fa-circle text-[6px] ml-1"></i>
                        {{ $article->is_published ? 'منشور' : 'مسودة' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-clock text-[10px]"></i>
                    <span>{{ $article->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="flex-shrink-0 flex items-start">
                <a href="{{ route('admin.articles.edit', $article) }}" 
                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                   title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-sm font-medium text-gray-900 mb-1">لا توجد أخبار</h3>
            <p class="text-sm text-gray-500 mb-4">ابدأ بكتابة أول خبر لك</p>
            <a href="{{ route('admin.articles.create') }}" 
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                إضافة خبر جديد
            </a>
        </div>
        @endforelse
    </div>
</div>
