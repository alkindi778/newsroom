<!-- Category Distribution -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">توزيع الأقسام</h3>
    
    @if(isset($categoriesStats) && $categoriesStats->count() > 0)
        @php
            $totalCategoryArticles = $categoriesStats->sum('articles_count');
        @endphp
        <div class="space-y-3">
            @foreach($categoriesStats as $category)
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalCategoryArticles > 0 ? round(($category->articles_count / $totalCategoryArticles * 100), 1) : 0 }}%"></div>
                    </div>
                    <span class="text-sm text-gray-500 w-8 text-left">{{ $category->articles_count }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                عرض جميع الأقسام →
            </a>
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <h3 class="text-sm font-medium text-gray-900 mb-1">لا توجد أقسام</h3>
            <p class="text-sm text-gray-500 mb-4">ابدأ بإضافة أقسام للأخبار</p>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                إضافة قسم جديد
            </a>
        </div>
    @endif
</div>
