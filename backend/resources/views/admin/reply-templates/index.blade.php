@extends('admin.layouts.app')

@section('title', 'قوالب الردود')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">📝 قوالب الردود</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة قوالب الردود الجاهزة للرسائل</p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.contact-messages.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors">
                لوحة الإحصائيات
            </a>
            <a href="{{ route('admin.contact-messages.templates.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                إنشاء قالب جديد
            </a>
        </div>
    </div>

    <!-- القوالب -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase">#</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase">الاسم</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase">التصنيف</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase">الاستخدام</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase">الحالة</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($templates as $template)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $template->id }}</td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $template->name }}</div>
                                @if($template->subject)
                                    <div class="text-xs text-gray-500">{{ Str::limit($template->subject, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $template->category_color }}-100 text-{{ $template->category_color }}-800">
                                    {{ $template->category_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-medium text-gray-700">{{ $template->usage_count }}</span>
                                <span class="text-xs text-gray-500">مرة</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($template->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        نشط
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        معطل
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.contact-messages.templates.edit', $template->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.contact-messages.templates.toggle', $template->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 {{ $template->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors" title="{{ $template->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            @if($template->is_active)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.contact-messages.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا القالب؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📝</div>
                                لا توجد قوالب ردود
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($templates->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
