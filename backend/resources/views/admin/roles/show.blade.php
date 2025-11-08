@extends('admin.layouts.app')

@section('title', 'تفاصيل الدور: ' . $role->name)
@section('page-title', 'تفاصيل الدور: ' . $role->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى قائمة الأدوار
        </a>
        
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('admin.roles.edit', $role) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل الدور
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Role Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الدور</h3>
            
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">اسم الدور</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">عدد الصلاحيات</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permissions->count() }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">عدد المستخدمين</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->users_count }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('Y-m-d H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Role Permissions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">الصلاحيات المرتبطة</h3>
            
            @if($permissions->count() > 0)
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @foreach($permissions as $permission)
                        <div class="flex items-center px-3 py-2 bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-900">{{ $permission->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">لا توجد صلاحيات مرتبطة بهذا الدور</p>
            @endif
        </div>
    </div>

    <!-- Users with this Role -->
    @if($users->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">المستخدمين الذين لديهم هذا الدور</h3>
                <span class="text-sm text-gray-500">{{ $role->users_count }} من المستخدمين</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $user)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        @if($user->avatar)
                            <img class="h-10 w-10 rounded-full ml-3" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center ml-3">
                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                        <a href="{{ route('admin.users.show', $user) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm">
                            عرض
                        </a>
                    </div>
                @endforeach
            </div>
            
            @if($role->users_count > $users->count())
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" 
                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                        عرض جميع المستخدمين ({{ $role->users_count }})
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
