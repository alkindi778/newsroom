@extends('admin.layouts.app')

@section('title', 'إضافة إصدار صحيفة جديد')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">إضافة إصدار صحيفة جديد</h1>
        <p class="text-gray-600">قم بإضافة بيانات الإصدار، رابط الـ PDF، وصورة الغلاف.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.newspaper-issues.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">اسم الصحيفة</label>
                <input type="text" name="newspaper_name" value="{{ old('newspaper_name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">رقم العدد</label>
                <input type="number" name="issue_number" value="{{ old('issue_number') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">وصف العدد</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="وصف مختصر لمحتوى العدد">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">رابط الـ PDF</label>
                <input type="url" name="pdf_url" value="{{ old('pdf_url') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://example.com/issue.pdf" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">تاريخ النشر</label>
                <input type="date" name="publication_date" value="{{ old('publication_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">صورة الغلاف</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500">يفضل استخدام صورة عمودية (مثلاً 1200x1700).</p>
            </div>
            <div class="border border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center text-gray-400 text-sm">
                <span>ستظهر صورة الغلاف هنا في لوحة التحكم والواجهة الأمامية.</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
                <input type="checkbox" name="is_published" id="is_published" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_published', true) ? 'checked' : '' }}>
                <label for="is_published" class="mr-2 text-sm text-gray-700">منشور</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" class="w-4 h-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400" {{ old('is_featured') ? 'checked' : '' }}>
                <label for="is_featured" class="mr-2 text-sm text-gray-700">إصدار مميز</label>
            </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
            <a href="{{ route('admin.newspaper-issues.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">العودة إلى الإصدارات</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
                <i class="fas fa-save"></i>
                <span>حفظ الإصدار</span>
            </button>
        </div>
    </form>
</div>
@endsection
