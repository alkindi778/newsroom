@extends('admin.layouts.app')

@section('title', 'رموز الاسترداد')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('admin.security.index') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى إعدادات الأمان
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">رموز الاسترداد</h1>
        <p class="text-gray-600 mt-1">احتفظ بهذه الرموز في مكان آمن</p>
    </div>

    <!-- Recovery Codes Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 bg-yellow-50">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl ml-3"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        تحذير مهم
                    </h2>
                    <p class="text-sm text-gray-700">
                        هذه الرموز هي الطريقة الوحيدة لاستعادة حسابك إذا فقدت الوصول إلى جهازك.
                        احفظها في مكان آمن ولا تشاركها مع أي شخص.
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($codes as $index => $code)
                        <div class="flex items-center bg-white p-3 rounded border border-gray-200">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded ml-2">
                                {{ $index + 1 }}
                            </span>
                            <code class="text-gray-900 font-mono text-sm">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button onclick="printCodes()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-print ml-2"></i>
                    طباعة الرموز
                </button>
                
                <button onclick="copyCodes()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-copy ml-2"></i>
                    نسخ الرموز
                </button>
                
                <form method="POST" action="{{ route('admin.security.regenerate-recovery-codes') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg transition-colors duration-200"
                            onclick="return confirm('هل أنت متأكد من إنشاء رموز جديدة؟\n\nسيتم استبدال الرموز الحالية برموز جديدة.')">
                        <i class="fas fa-sync-alt ml-2"></i>
                        إنشاء رموز جديدة
                    </button>
                </form>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-3">كيفية استخدام رموز الاسترداد:</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 ml-2 mt-1"></i>
                    <span>يمكنك استخدام أي رمز من هذه الرموز مرة واحدة فقط</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 ml-2 mt-1"></i>
                    <span>استخدم الرمز عند فقدان الوصول إلى جهاز المصادقة الثنائية</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 ml-2 mt-1"></i>
                    <span>بعد استخدام جميع الرموز، يمكنك إنشاء رموز جديدة</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printCodes() {
    window.print();
}

function copyCodes() {
    const codes = @json($codes);
    const text = codes.join('\n');
    
    navigator.clipboard.writeText(text).then(() => {
        alert('تم نسخ الرموز إلى الحافظة!');
    }).catch(err => {
        console.error('فشل النسخ:', err);
        alert('فشل نسخ الرموز. حاول مرة أخرى.');
    });
}
</script>
@endpush
@endsection
