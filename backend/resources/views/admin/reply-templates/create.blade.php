@extends('admin.layouts.app')

@section('title', 'إنشاء قالب رد')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📝 إنشاء قالب رد جديد</h1>
            <p class="text-sm text-gray-600">أنشئ قالباً جديداً للردود السريعة</p>
        </div>
        <a href="{{ route('admin.contact-messages.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
            </svg>
            العودة للقائمة
        </a>
    </div>

    <form action="{{ route('admin.contact-messages.templates.store') }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900">معلومات القالب</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم القالب <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: تأكيد الاستلام">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="acknowledgment" {{ old('category') == 'acknowledgment' ? 'selected' : '' }}>تأكيد استلام</option>
                            <option value="followup" {{ old('category') == 'followup' ? 'selected' : '' }}>متابعة</option>
                            <option value="approval" {{ old('category') == 'approval' ? 'selected' : '' }}>موافقة</option>
                            <option value="rejection" {{ old('category') == 'rejection' ? 'selected' : '' }}>اعتذار</option>
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>عام</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">عنوان البريد الافتراضي</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: تأكيد استلام رسالتكم">
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">محتوى القالب <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="10" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="اكتب نص القالب هنا...">{{ old('content') }}</textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        💡 يمكنك استخدام المتغيرات التالية: 
                        <code class="bg-gray-200 px-1 rounded">{name}</code> اسم المرسل، 
                        <code class="bg-gray-200 px-1 rounded">{subject}</code> موضوع الرسالة، 
                        <code class="bg-gray-200 px-1 rounded">{email}</code> البريد، 
                        <code class="bg-gray-200 px-1 rounded">{phone}</code> الهاتف، 
                        <code class="bg-gray-200 px-1 rounded">{date}</code> التاريخ
                    </p>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="mr-2 text-sm font-medium text-gray-700">نشط (متاح للاستخدام)</span>
                    </label>
                </div>
            </div>
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <a href="{{ route('admin.contact-messages.templates.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    حفظ القالب
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
