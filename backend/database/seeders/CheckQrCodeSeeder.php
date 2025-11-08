<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class CheckQrCodeSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== فحص محتوى QR Code ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin || !$admin->two_factor_secret) {
            echo "❌ المصادقة الثنائية غير مفعلة\n";
            return;
        }
        
        $secret = decrypt($admin->two_factor_secret);
        
        echo "🔑 السر في قاعدة البيانات: {$secret}\n\n";
        
        // محاولة توليد QR Code URL مثل ما يفعل Fortify
        $google2fa = new Google2FA();
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $secret
        );
        
        echo "📱 محتوى QR Code:\n";
        echo "{$qrCodeUrl}\n\n";
        
        // محاولة الحصول على QR Code من Fortify
        try {
            $qrCode = $admin->twoFactorQrCodeSvg();
            echo "✅ QR Code من Fortify تم توليده بنجاح\n";
            
            // استخراج السر من QR Code (إذا كان ممكن)
            if (preg_match('/secret=([A-Z0-9]+)/', $qrCode, $matches)) {
                $secretFromQr = $matches[1];
                echo "🔑 السر المستخرج من QR Code: {$secretFromQr}\n\n";
                
                if ($secretFromQr === $secret) {
                    echo "✅ السر في QR Code يطابق قاعدة البيانات!\n";
                } else {
                    echo "❌ السر في QR Code لا يطابق قاعدة البيانات!\n";
                    echo "   - في QR: {$secretFromQr}\n";
                    echo "   - في DB: {$secret}\n";
                }
            }
        } catch (\Exception $e) {
            echo "❌ خطأ في توليد QR Code: " . $e->getMessage() . "\n";
        }
    }
}
