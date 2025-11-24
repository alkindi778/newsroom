@extends('admin.layouts.app')

@section('title', 'أرشيف الرسائل')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                أرشيف الرسائل
            </h1>
            <p class="mt-1 text-sm text-gray-600">{{ $stats['total'] }} رسالة مؤرشفة</p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.contact-messages.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors">
                الرسائل النشطة
            </a>
            <a href="{{ route('admin.contact-messages.archive.export') }}?{{ http_build_query(request()->query()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                تصدير CSV
            </a>
        </div>
    </div>

    <!-- إحصائيات التصنيفات -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        @php
            $categories = [
                'استفسار_عام' => ['label' => 'استفسار عام', 'color' => 'blue', 'icon' => 'question-mark-circle'],
                'شكوى' => ['label' => 'شكوى', 'color' => 'red', 'icon' => 'exclamation-circle'],
                'اقتراح' => ['label' => 'اقتراح', 'color' => 'green', 'icon' => 'light-bulb'],
                'طلب_معلومات' => ['label' => 'طلب معلومات', 'color' => 'purple', 'icon' => 'information-circle'],
                'تعاون_إعلامي' => ['label' => 'تعاون إعلامي', 'color' => 'indigo', 'icon' => 'users'],
                'دعم_فني' => ['label' => 'دعم فني', 'color' => 'yellow', 'icon' => 'cog'],
                'أخرى' => ['label' => 'أخرى', 'color' => 'gray', 'icon' => 'dots-horizontal'],
            ];
        @endphp
        
        @foreach($categories as $key => $cat)
            <a href="{{ route('admin.contact-messages.archive.index', ['category' => $key]) }}" 
               class="bg-white rounded-lg p-4 border-2 {{ request('category') == $key ? 'border-'.$cat['color'].'-500' : 'border-transparent' }} hover:border-{{ $cat['color'] }}-300 transition-colors">
                <div class="text-2xl font-bold text-{{ $cat['color'] }}-600">{{ $stats['categories'][$key] ?? 0 }}</div>
                <div class="text-xs text-gray-600">{{ $cat['label'] }}</div>
            </a>
        @endforeach
    </div>

    <!-- فلاتر البحث -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.contact-messages.archive.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الأرشيف..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            
            <div class="w-48">
                <select name="tag" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">كل الوسوم</option>
                    @foreach($allTags as $tag)
                        <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>{{ $tag }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">
                بحث
            </button>
            
            @if(request()->hasAny(['search', 'category', 'tag']))
                <a href="{{ route('admin.contact-messages.archive.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    إعادة تعيين
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الرسائل المؤرشفة -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">الرسالة</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">التصنيف</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">الملخص</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">الوسوم</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">تاريخ الأرشفة</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($messages as $message)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $message->full_name }}</div>
                                <div class="text-sm text-gray-600">{{ Str::limit($message->subject, 40) }}</div>
                                <div class="text-xs text-gray-500">{{ $message->email }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $catInfo = $categories[$message->archive_category] ?? ['label' => $message->archive_category, 'color' => 'gray'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $catInfo['color'] }}-100 text-{{ $catInfo['color'] }}-800">
                                    {{ $catInfo['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600 max-w-xs truncate" title="{{ $message->archive_summary }}">
                                    {{ Str::limit($message->archive_summary, 60) }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($message->archive_tags ?? [], 0, 3) as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $message->archived_at?->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $message->archiver?->name ?? 'غير معروف' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.contact-messages.archive.show', $message->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="عرض">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.contact-messages.unarchive', $message->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg" title="إلغاء الأرشفة">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                لا توجد رسائل مؤرشفة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
