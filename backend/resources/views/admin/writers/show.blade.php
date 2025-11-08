@extends('admin.layouts.app')

@section('title', 'عرض الكاتب: ' . $writer->name)
@section('page-title', 'عرض الكاتب')

@section('content')
<div class="space-y-6">
    <!-- Messages -->
    <x-admin.alert-messages />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">{{ $writer->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $writer->position ?? 'كاتب رأي' }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            @can('تعديل كُتاب الرأي')
            <a href="{{ route('admin.writers.edit', $writer) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                تعديل الكاتب
            </a>
            @endcan
            
            <a href="{{ route('admin.writers.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Writer Info -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">معلومات الكاتب</h3>
                </div>
                
                <div class="p-6">
                    <!-- Profile Image -->
                    <div class="text-center mb-6">
                        @if($writer->image)
                        <img src="{{ asset('storage/' . $writer->image) }}" 
                             alt="{{ $writer->name }}" 
                             class="mx-auto h-32 w-32 rounded-full object-cover">
                        @else
                        <div class="mx-auto h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center">
                            <svg class="h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        @endif
                        
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $writer->name }}</h2>
                        
                        <div class="mt-2">
                            @if($writer->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">نشط</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">غير نشط</span>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-4">
                        @if($writer->email)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $writer->email }}</span>
                        </div>
                        @endif

                        @if($writer->phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $writer->phone }}</span>
                        </div>
                        @endif

                        @if($writer->specialization)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $writer->specialization }}</span>
                        </div>
                        @endif

                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0V9a2 2 0 00-2 2v9a2 2 0 002 2h6a2 2 0 002-2V11a2 2 0 00-2-2V7"></path>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $writer->opinions_count }} مقال</span>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0V9a2 2 0 00-2 2v9a2 2 0 002 2h6a2 2 0 002 2V11a2 2 0 00-2-2V7"></path>
                            </svg>
                            <span class="text-sm text-gray-600">انضم في {{ $writer->created_at->format('Y/m/d') }}</span>
                        </div>
                    </div>

                    <!-- Social Links -->
                    @if($writer->facebook_url || $writer->twitter_url || $writer->linkedin_url || $writer->website_url)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">الروابط الاجتماعية</h4>
                        <div class="flex space-x-4 space-x-reverse">
                            @if($writer->facebook_url)
                            <a href="{{ $writer->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            @endif

                            @if($writer->twitter_url)
                            <a href="{{ $writer->twitter_url }}" target="_blank" class="text-sky-500 hover:text-sky-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                            </a>
                            @endif

                            @if($writer->linkedin_url)
                            <a href="{{ $writer->linkedin_url }}" target="_blank" class="text-blue-700 hover:text-blue-900">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            @endif

                            @if($writer->website_url)
                            <a href="{{ $writer->website_url }}" target="_blank" class="text-gray-600 hover:text-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9-9a9 9 0 00-9 9m0 0a9 9 0 009 9"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($writer->bio)
            <!-- Bio -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">السيرة الذاتية</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $writer->bio }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Writer's Articles -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">مقالات الكاتب ({{ $writer->opinions_count }})</h3>
                        @can('إنشاء مقالات الرأي')
                        <a href="{{ route('admin.opinions.create', ['writer_id' => $writer->id]) }}" 
                           class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            إضافة مقال
                        </a>
                        @endcan
                    </div>
                </div>

                @if($writer->opinions && $writer->opinions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($writer->opinions()->latest()->take(10)->get() as $opinion)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                    <a href="{{ route('admin.opinions.show', $opinion) }}">{{ $opinion->title }}</a>
                                </h4>
                                @if($opinion->excerpt)
                                <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $opinion->excerpt }}</p>
                                @endif
                                
                                <div class="mt-2 flex items-center space-x-4 space-x-reverse text-xs text-gray-500">
                                    <span>{{ $opinion->created_at->format('Y/m/d') }}</span>
                                    
                                    @if($opinion->is_published)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">منشور</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">مسودة</span>
                                    @endif
                                    
                                    @if($opinion->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">مميز</span>
                                    @endif
                                    
                                    @if($opinion->views > 0)
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ $opinion->views }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 space-x-reverse mr-4">
                                @can('عرض مقالات الرأي')
                                <a href="{{ route('admin.opinions.show', $opinion) }}" class="text-blue-600 hover:text-blue-900" title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endcan
                                
                                @can('تعديل مقالات الرأي')
                                <a href="{{ route('admin.opinions.edit', $opinion) }}" class="text-indigo-600 hover:text-indigo-900" title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($writer->opinions()->count() > 10)
                <div class="px-6 py-4 bg-gray-50 text-center">
                    <a href="{{ route('admin.opinions.index', ['writer' => $writer->id]) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        عرض جميع المقالات ({{ $writer->opinions_count }})
                    </a>
                </div>
                @endif
                @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد مقالات</h3>
                    <p class="mt-1 text-sm text-gray-500">لم يكتب هذا الكاتب أي مقالات بعد.</p>
                    @can('إنشاء مقالات الرأي')
                    <div class="mt-6">
                        <a href="{{ route('admin.opinions.create', ['writer_id' => $writer->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            إضافة أول مقال
                        </a>
                    </div>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
