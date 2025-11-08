@extends('admin.layouts.app')

@section('title', 'إعدادات الأمان')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">إعدادات الأمان</h1>
        <p class="text-gray-600 mt-1">إدارة كلمة المرور والمصادقة الثنائية</p>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-key ml-2 text-blue-600"></i>
                تغيير كلمة المرور
            </h2>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.security.update-password') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور الحالية
                    </label>
                    <input type="password" 
                           name="current_password" 
                           id="current_password"
                           class="w-full px-4 py-2 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور الجديدة
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-4 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        تأكيد كلمة المرور الجديدة
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="w-full px-4 py-2 border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    تحديث كلمة المرور
                </button>
            </form>
        </div>
    </div>

    <!-- Two Factor Authentication Section -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-shield-alt ml-2 text-green-600"></i>
                المصادقة الثنائية (2FA)
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <p class="text-gray-700 mb-2">
                        <strong>الحالة:</strong> 
                        @if($two_factor_enabled)
                            @if($two_factor_confirmed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    مُفعّلة ومؤكدة
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock ml-1"></i>
                                    مُفعّلة (تحتاج إلى تأكيد)
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle ml-1"></i>
                                غير مُفعّلة
                            </span>
                        @endif
                    </p>
                    <p class="text-gray-600 text-sm mt-3">
                        المصادقة الثنائية تضيف طبقة أمان إضافية لحسابك عن طريق طلب رمز تحقق من تطبيق المصادقة عند تسجيل الدخول.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($two_factor_enabled)
                    @if(!$two_factor_confirmed)
                        <a href="{{ route('admin.security.qr-code') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-qrcode ml-2"></i>
                            إكمال إعداد المصادقة الثنائية
                        </a>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.security.disable-2fa') }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors duration-200"
                                onclick="return confirm('هل أنت متأكد من إيقاف المصادقة الثنائية؟\n\nسيتم حذف جميع إعدادات المصادقة الثنائية.')">
                            <i class="fas fa-times ml-2"></i>
                            إيقاف المصادقة الثنائية
                        </button>
                    </form>
                    
                    @if($two_factor_confirmed)
                        <a href="{{ route('admin.security.recovery-codes') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-key ml-2"></i>
                            عرض رموز الاسترداد
                        </a>
                        
                        <a href="{{ route('admin.security.qr-code') }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                            <i class="fas fa-qrcode ml-2"></i>
                            عرض رمز QR
                        </a>
                    @endif
                @else
                    <form method="POST" action="{{ route('admin.security.enable-2fa') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-check ml-2"></i>
                            تفعيل المصادقة الثنائية
                        </button>
                    </form>
                @endif
            </div>

            @if($two_factor_enabled && !$two_factor_confirmed)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 ml-2 mt-1"></i>
                    <div>
                        <p class="text-sm text-blue-800">
                            <strong>الخطوة التالية:</strong> اضغط على "إكمال إعداد المصادقة الثنائية" لمسح رمز QR وتأكيد التفعيل.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($two_factor_enabled && $two_factor_confirmed)
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 ml-2 mt-1"></i>
                    <div>
                        <p class="text-sm text-yellow-800">
                            <strong>مهم:</strong> احتفظ برموز الاسترداد في مكان آمن. ستحتاجها إذا فقدت الوصول إلى تطبيق المصادقة على جهازك.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Session Management -->
    <div class="bg-white rounded-lg shadow-md mt-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-history ml-2 text-purple-600"></i>
                سجل الجلسات
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Session -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-desktop text-blue-600 text-2xl"></i>
                        </div>
                        <div class="mr-3 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">الجلسة الحالية</h3>
                            <div class="space-y-1 text-sm text-gray-700">
                                <p class="flex items-center">
                                    <i class="fas fa-network-wired w-4 ml-2 text-gray-500"></i>
                                    <span class="font-medium">عنوان IP:</span>
                                    <span class="mr-2 font-mono">{{ request()->ip() }}</span>
                                </p>
                                <p class="flex items-center">
                                    <i class="fas fa-clock w-4 ml-2 text-gray-500"></i>
                                    <span class="font-medium">الوقت:</span>
                                    <span class="mr-2">{{ now()->format('Y-m-d H:i:s') }}</span>
                                </p>
                                <p class="flex items-center">
                                    <i class="fas fa-browser w-4 ml-2 text-gray-500"></i>
                                    <span class="font-medium">المتصفح:</span>
                                    <span class="mr-2 text-xs">{{ request()->userAgent() ? \Illuminate\Support\Str::limit(request()->userAgent(), 30) : 'غير متوفر' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Login -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-history text-gray-600 text-2xl"></i>
                        </div>
                        <div class="mr-3 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">آخر تسجيل دخول</h3>
                            @if($user->last_login_at)
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p class="flex items-center">
                                        <i class="fas fa-calendar-alt w-4 ml-2 text-gray-500"></i>
                                        <span class="font-medium">التاريخ:</span>
                                        <span class="mr-2">{{ $user->last_login_at->format('Y-m-d') }}</span>
                                    </p>
                                    <p class="flex items-center">
                                        <i class="fas fa-clock w-4 ml-2 text-gray-500"></i>
                                        <span class="font-medium">الوقت:</span>
                                        <span class="mr-2">{{ $user->last_login_at->format('H:i:s') }}</span>
                                    </p>
                                    <p class="flex items-center">
                                        <i class="fas fa-hourglass-half w-4 ml-2 text-gray-500"></i>
                                        <span class="font-medium">منذ:</span>
                                        <span class="mr-2">{{ $user->last_login_at->diffForHumans() }}</span>
                                    </p>
                                    @if($user->last_login_ip)
                                    <p class="flex items-center">
                                        <i class="fas fa-network-wired w-4 ml-2 text-gray-500"></i>
                                        <span class="font-medium">IP:</span>
                                        <span class="mr-2 font-mono">{{ $user->last_login_ip }}</span>
                                    </p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-500">لا توجد سجلات سابقة</p>
                                    <p class="text-xs text-gray-400 mt-1">هذا أول تسجيل دخول لك</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Note -->
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-green-600 ml-2 mt-1"></i>
                    <div class="text-sm text-green-800">
                        <p class="font-semibold mb-1">نصيحة أمنية:</p>
                        <p>إذا لاحظت نشاطاً مشبوهاً أو تسجيل دخول من موقع غير معروف، قم بتغيير كلمة المرور فوراً وتفعيل المصادقة الثنائية.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
