<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class Check2FASeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== فحص المصادقة الثنائية ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ المستخدم غير موجود\n";
            return;
        }
        
        echo "👤 المستخدم: {$admin->name}\n";
        echo "📧 البريد: {$admin->email}\n\n";
        
        // التحقق من حالة 2FA
        if ($admin->two_factor_secret) {
            echo "✅ المصادقة الثنائية مفعلة\n";
            
            $google2fa = new Google2FA();
            
            // فك تشفير السر
            $secret = decrypt($admin->two_factor_secret);
            
            echo "\n📱 معلومات التطبيق:\n";
            echo "Secret (مخفي): " . substr($secret, 0, 4) . "****\n";
            
            // توليد رمز حالي للمقارنة
            $currentCode = $google2fa->getCurrentOtp($secret);
            echo "\n🔢 الرمز الحالي (للاختبار): {$currentCode}\n";
            
            echo "\n⏰ التوقيت:\n";
            echo "Server Time: " . now()->format('Y-m-d H:i:s') . "\n";
            echo "Timezone: " . config('app.timezone') . "\n";
            
            if ($admin->two_factor_confirmed_at) {
                echo "\n✅ تم تأكيد 2FA في: {$admin->two_factor_confirmed_at}\n";
            } else {
                echo "\n⚠️  لم يتم تأكيد 2FA بعد\n";
            }
            
        } else {
            echo "❌ المصادقة الثنائية غير مفعلة\n";
        }
        
        echo "\n💡 نصيحة: إذا كان الرمز لا يعمل، حاول:\n";
        echo "  1. تأكد من صحة التوقيت على هاتفك\n";
        echo "  2. استخدم رمز الاسترداد المحفوظ\n";
        echo "  3. أعد مسح QR Code في التطبيق\n";
    }
}
