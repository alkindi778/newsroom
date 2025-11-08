<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class VerifyUserCodeSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== التحقق من رمز المستخدم ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin || !$admin->two_factor_secret) {
            echo "❌ المصادقة الثنائية غير مفعلة\n";
            return;
        }
        
        $google2fa = new Google2FA();
        $secret = decrypt($admin->two_factor_secret);
        
        echo "🔑 السر الحالي في قاعدة البيانات: {$secret}\n";
        echo "⏰ توقيت السيرفر: " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // عرض الرموز الصالحة الحالية (للمقارنة)
        echo "🔢 الرموز الصالحة حالياً:\n";
        
        // الرمز الحالي
        $currentCode = $google2fa->getCurrentOtp($secret);
        echo "  - الحالي: {$currentCode}\n";
        
        // الرمز السابق (قبل 30 ثانية)
        $previousCode = $google2fa->oathTotp($secret, floor(time() / 30) - 1);
        echo "  - السابق: {$previousCode}\n";
        
        // الرمز التالي (بعد 30 ثانية)
        $nextCode = $google2fa->oathTotp($secret, floor(time() / 30) + 1);
        echo "  - التالي: {$nextCode}\n\n";
        
        echo "💡 جرّب أحد هذه الرموز في الصفحة\n";
        echo "💡 إذا لم تنجح، تأكد من:\n";
        echo "   1. السر في التطبيق هو: {$secret}\n";
        echo "   2. حذف أي حساب قديم من التطبيق\n";
        echo "   3. التوقيت التلقائي مفعل على الهاتف\n";
    }
}
