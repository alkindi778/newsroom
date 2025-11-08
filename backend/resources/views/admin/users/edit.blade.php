@extends('admin.layouts.app')

@section('title', 'تعديل المستخدم')
@section('page-title', 'تعديل المستخدم: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى قائمة المستخدمين
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الشخصية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>

                <!-- Bio -->
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">نبذة شخصية</label>
                    <textarea name="bio" id="bio" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <!-- Avatar -->
                <div class="mt-6">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">الصورة الشخصية</label>
                    <div class="flex items-center space-x-6 space-x-reverse">
                        <div class="shrink-0">
                            @if($user->avatar)
                                <img id="avatar-preview" class="h-16 w-16 object-cover rounded-full" 
                                     src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <img id="avatar-preview" class="h-16 w-16 object-cover rounded-full" 
                                     src="https://via.placeholder.com/64x64.png?text=صورة" alt="صورة المستخدم">
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatar" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF حتى 2MB</p>
                            @if($user->avatar)
                                <p class="mt-1 text-xs text-gray-400">الصورة الحالية: {{ basename($user->avatar) }}</p>
                            @endif
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Security -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الأمان</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
                        <input type="password" name="password" id="password"
                               class="w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }}">
                        <p class="mt-1 text-xs text-gray-500">اتركها فارغة إذا كنت لا تريد تغييرها</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">الأدوار والصلاحيات</h3>
                
                <!-- Current Roles Display -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الأدوار الحالية</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $currentRole)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $currentRole->name }}
                            </span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span class="text-sm text-gray-500">لا توجد أدوار مخصصة</span>
                        @endif
                    </div>
                </div>
                
                <!-- Roles -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">تعديل الأدوار</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($roles as $id => $name)
                            <div class="flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $name }}" id="role_{{ $id }}"
                                       {{ $user->hasRole($name) || in_array($name, old('roles', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="role_{{ $id }}" class="mr-2 text-sm text-gray-700">{{ $name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Permissions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">الصلاحيات الإضافية</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-4">
                        @foreach($permissions as $id => $name)
                            <div class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $name }}" id="permission_{{ $id }}"
                                       {{ $user->hasPermissionTo($name) || in_array($name, old('permissions', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="permission_{{ $id }}" class="mr-2 text-sm text-gray-700">{{ $name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">إحصائيات المستخدم</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <span class="font-medium text-gray-700">تاريخ التسجيل:</span>
                        <div class="text-gray-900">{{ $user->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <span class="font-medium text-gray-700">عدد المقالات:</span>
                        <div class="text-gray-900">{{ $user->articles()->count() }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <span class="font-medium text-gray-700">آخر دخول:</span>
                        <div class="text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول بعد' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
