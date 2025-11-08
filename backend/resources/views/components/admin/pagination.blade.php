@props(['paginator', 'class' => ''])

@if ($paginator->hasPages())
    {{-- Desktop Pagination --}}
    <nav class="hidden sm:flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 {{ $class }}" aria-label="Pagination">
        <div class="hidden sm:block">
            <p class="text-sm text-gray-700">
                عرض
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                إلى
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                من
                <span class="font-medium">{{ $paginator->total() }}</span>
                نتيجة
            </p>
        </div>
        <div class="flex flex-1 justify-between sm:justify-end">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">السابق</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    السابق
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden md:-mt-px md:flex">
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="relative z-10 inline-flex items-center border border-blue-500 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 focus:z-20" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" 
                           class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="relative mr-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    التالي
                </a>
            @else
                <span class="relative mr-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">التالي</span>
            @endif
        </div>
    </nav>

    {{-- Mobile Pagination --}}
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:hidden">
        <div class="flex flex-1 justify-between">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">السابق</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    السابق
                </a>
            @endif

            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                {{ $paginator->currentPage() }} من {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    التالي
                </a>
            @else
                <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">التالي</span>
            @endif
        </div>
    </div>
@endif
