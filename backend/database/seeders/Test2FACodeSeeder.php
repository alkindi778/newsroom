<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class Test2FACodeSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== اختبار رمز المصادقة الثنائية ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin || !$admin->two_factor_secret) {
            echo "❌ المصادقة الثنائية غير مفعلة\n";
            return;
        }
        
        $google2fa = new Google2FA();
        $secret = decrypt($admin->two_factor_secret);
        
        echo "🔑 السر: {$secret}\n";
        echo "⏰ التوقيت: " . now()->format('Y-m-d H:i:s') . "\n";
        echo "🌍 Timezone: " . config('app.timezone') . "\n\n";
        
        // توليد الرمز الحالي
        $currentCode = $google2fa->getCurrentOtp($secret);
        echo "🔢 الرمز الحالي: {$currentCode}\n\n";
        
        // طلب إدخال رمز للاختبار
        echo "📝 اختبر رمزاً من التطبيق (أدخله في Terminal):\n";
        echo "   أو استخدم الرمز أعلاه مباشرة\n\n";
        
        // اختبار التحقق
        $testCode = $currentCode; // استخدم الرمز الحالي
        $valid = $google2fa->verifyKey($secret, $testCode);
        
        if ($valid) {
            echo "✅ الرمز {$testCode} صحيح!\n";
            echo "✅ التحقق يعمل بشكل صحيح\n\n";
        } else {
            echo "❌ الرمز {$testCode} خاطئ!\n";
            echo "⚠️  هناك مشكلة في التحقق\n\n";
        }
        
        // اختبار window (للسماح بفارق زمني)
        echo "🔍 اختبار مع window (فارق ±1 دقيقة):\n";
        $validWithWindow = $google2fa->verifyKey($secret, $testCode, 1);
        echo $validWithWindow ? "✅ يعمل مع window\n" : "❌ لا يعمل حتى مع window\n";
        
        echo "\n💡 نصيحة: تأكد من أن:\n";
        echo "  1. السر في التطبيق: {$secret}\n";
        echo "  2. التوقيت على هاتفك صحيح (Auto-sync ON)\n";
        echo "  3. التطبيق مضبوط على Time-based (TOTP)\n";
    }
}
