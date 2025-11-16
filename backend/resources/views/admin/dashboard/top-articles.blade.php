<!-- Top Viewed Articles -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">الأخبار الأكثر مشاهدة</h3>
        <i class="fas fa-fire text-orange-500"></i>
    </div>
    
    @if($topArticles->count() > 0)
        <div class="space-y-4">
            @foreach($topArticles as $article)
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                @if($article->image)
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         alt="{{ $article->title }}"
                         class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                @endif
                
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.articles.edit', $article->id) }}" 
                       class="text-sm font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                        {{ $article->title }}
                    </a>
                </div>
                
                <div class="flex items-center text-green-600 flex-shrink-0">
                    <i class="fas fa-eye text-xs ml-1"></i>
                    <span class="text-sm font-semibold">{{ number_format($article->views) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-chart-line text-3xl mb-2 opacity-50"></i>
            <p class="text-sm">لا توجد بيانات بعد</p>
        </div>
    @endif
</div>
