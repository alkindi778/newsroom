<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Reset2FASeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== إعادة تعيين المصادقة الثنائية ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ المستخدم غير موجود\n";
            return;
        }
        
        $google2fa = new Google2FA();
        
        // توليد secret جديد
        $secret = $google2fa->generateSecretKey();
        
        // حفظ في قاعدة البيانات
        $admin->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                return rand(100000, 999999) . '-' . rand(100000, 999999);
            })->all())),
            'two_factor_confirmed_at' => now(),
        ])->save();
        
        echo "✅ تم إعادة تعيين المصادقة الثنائية\n\n";
        
        // عرض QR Code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $secret
        );
        
        echo "📱 امسح هذا الرمز في تطبيق المصادقة:\n\n";
        echo "QR Code URL:\n{$qrCodeUrl}\n\n";
        
        // عرض Secret للإدخال اليدوي
        echo "أو أدخل السر يدوياً:\n";
        echo "Secret Key: {$secret}\n\n";
        
        // عرض الرمز الحالي للاختبار
        $currentCode = $google2fa->getCurrentOtp($secret);
        echo "🔢 الرمز الحالي (للاختبار): {$currentCode}\n\n";
        
        // عرض رموز الاسترداد
        $recoveryCodes = json_decode(decrypt($admin->two_factor_recovery_codes), true);
        echo "💾 رموز الاسترداد (احفظها في مكان آمن):\n";
        foreach ($recoveryCodes as $code) {
            echo "  - {$code}\n";
        }
        
        echo "\n✅ الآن امسح QR Code في تطبيق المصادقة وجرب تسجيل الدخول!\n";
    }
}
