<!-- Breaking News -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">الأخبار العاجلة</h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <i class="fas fa-bolt ml-1"></i>
            عاجل
        </span>
    </div>
    
    @if($breakingNews->count() > 0)
        <div class="space-y-3">
            @foreach($breakingNews as $news)
            <div class="border-r-4 border-red-500 pr-3 py-2 hover:bg-gray-50 rounded transition-colors">
                <a href="{{ route('admin.articles.edit', $news->id) }}" 
                   class="text-sm font-medium text-gray-900 hover:text-blue-600 line-clamp-2 mb-1">
                    {{ $news->title }}
                </a>
                <p class="text-xs text-gray-500">
                    <i class="far fa-clock ml-1"></i>
                    {{ $news->created_at->diffForHumans() }}
                </p>
            </div>
            @endforeach
        </div>
        
        <a href="{{ route('admin.articles.index', ['is_breaking_news' => 1]) }}" 
           class="block mt-4 text-center text-sm text-blue-600 hover:text-blue-700 font-medium">
            عرض الكل
            <i class="fas fa-arrow-left mr-1"></i>
        </a>
    @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-bolt text-3xl mb-2 opacity-50"></i>
            <p class="text-sm">لا توجد أخبار عاجلة حالياً</p>
        </div>
    @endif
</div>
