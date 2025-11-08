@extends('admin.layouts.app')

@section('title', 'تعديل الصلاحية')
@section('page-title', 'تعديل الصلاحية')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى قائمة الصلاحيات
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الصلاحية *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    استخدم تنسيق: action_resource (مثل: create_articles, edit_users, manage_settings)
                </p>
            </div>

            <!-- Show associated roles -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">الأدوار المرتبطة</label>
                <div class="bg-gray-50 rounded-md p-3">
                    @if($permission->roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($permission->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">لا توجد أدوار مرتبطة بهذه الصلاحية</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.permissions.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
