<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class FixAndFreeze2FASeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== تثبيت وإصلاح المصادقة الثنائية ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ المستخدم غير موجود\n";
            return;
        }
        
        $google2fa = new Google2FA();
        
        // استخدام السر الحالي أو توليد واحد جديد
        if ($admin->two_factor_secret) {
            $secret = decrypt($admin->two_factor_secret);
            echo "📌 استخدام السر الموجود: {$secret}\n";
        } else {
            $secret = $google2fa->generateSecretKey();
            echo "🆕 توليد سر جديد: {$secret}\n";
        }
        
        // تثبيت السر وتأكيده
        $admin->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode([
                '123456-789012',
                '234567-890123',
                '345678-901234',
                '456789-012345',
                '567890-123456',
                '678901-234567',
                '789012-345678',
                '890123-456789',
            ])),
            'two_factor_confirmed_at' => null, // إلغاء التأكيد لإجباره على إعادة التأكيد
        ])->save();
        
        echo "\n✅ تم تثبيت السر بنجاح!\n";
        echo "🔑 السر الثابت: {$secret}\n\n";
        
        // عرض الرمز الحالي
        $currentCode = $google2fa->getCurrentOtp($secret);
        echo "🔢 الرمز الحالي: {$currentCode}\n\n";
        
        // عرض QR Code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $secret
        );
        
        echo "📱 رابط QR Code:\n{$qrCodeUrl}\n\n";
        
        echo "📋 خطوات الاستخدام:\n";
        echo "  1. احذف أي حساب قديم من التطبيق\n";
        echo "  2. أضف حساب جديد بالسر: {$secret}\n";
        echo "  3. استخدم الرمز من التطبيق في الصفحة\n";
        echo "  4. السر الآن ثابت ولن يتغير!\n";
    }
}
