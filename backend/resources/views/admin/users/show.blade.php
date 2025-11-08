@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')
@section('page-title', 'تفاصيل المستخدم: ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى قائمة المستخدمين
        </a>
        
        <div class="flex space-x-3 space-x-reverse">
            @can('edit_users')
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل المستخدم
            </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    @if($user->avatar)
                        <img class="mx-auto h-24 w-24 rounded-full object-cover" 
                             src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <div class="mx-auto h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    
                    <!-- Status Badge -->
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    
                    <!-- Roles -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">الأدوار</h4>
                        <div class="flex flex-wrap justify-center gap-2">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="text-sm text-gray-500">لا توجد أدوار مخصصة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:col-span-2">
            <div class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الشخصية</h3>
                    
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">الاسم الكامل</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">البريد الإلكتروني</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">رقم الهاتف</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'غير محدد' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">تاريخ التسجيل</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">آخر دخول</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول بعد' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">حالة البريد الإلكتروني</dt>
                            <dd class="mt-1 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $user->email_verified_at ? 'مفعل' : 'غير مفعل' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    
                    @if($user->bio)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500 mb-2">نبذة شخصية</dt>
                            <dd class="text-sm text-gray-900 bg-gray-50 rounded-lg p-3">{{ $user->bio }}</dd>
                        </div>
                    @endif
                </div>

                <!-- User Statistics -->
                @if(isset($insights))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">إحصائيات المستخدم</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $insights['articles']['total'] }}</div>
                            <div class="text-sm text-blue-600">إجمالي المقالات</div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $insights['articles']['published'] }}</div>
                            <div class="text-sm text-green-600">المقالات المنشورة</div>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-yellow-600">{{ $insights['articles']['draft'] }}</div>
                            <div class="text-sm text-yellow-600">المسودات</div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-600">{{ $insights['articles']['recent'] }}</div>
                            <div class="text-sm text-purple-600">مقالات هذا الشهر</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Permissions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">الصلاحيات</h3>
                    
                    @if($user->getAllPermissions()->count() > 0)
                        <div class="space-y-4">
                            <!-- Permissions from Roles -->
                            @if($user->getPermissionsViaRoles()->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">صلاحيات من الأدوار</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->getPermissionsViaRoles() as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Direct Permissions -->
                            @if($user->getDirectPermissions()->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">صلاحيات مباشرة</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->getDirectPermissions() as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد صلاحيات مخصصة لهذا المستخدم</p>
                    @endif
                </div>

                <!-- Recent Articles -->
                @if($user->articles()->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">المقالات الأخيرة</h3>
                        <span class="text-sm text-gray-500">{{ $user->articles()->count() }} مقال</span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($user->articles()->latest()->take(5)->get() as $article)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $article->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $article->created_at->format('Y-m-d') }}</p>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $article->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $article->is_published ? 'منشور' : 'مسودة' }}
                                    </span>
                                    <a href="{{ route('admin.articles.show', $article) }}" class="text-blue-600 hover:text-blue-900 text-xs">
                                        عرض
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($user->articles()->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.articles.index', ['author' => $user->id]) }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                عرض جميع المقالات ({{ $user->articles()->count() }})
                            </a>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
