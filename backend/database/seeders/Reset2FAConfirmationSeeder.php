<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class Reset2FAConfirmationSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== إعادة تعيين تأكيد 2FA ===\n\n";
        
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ المستخدم غير موجود\n";
            return;
        }
        
        // إلغاء التأكيد لإجبار المستخدم على إدخال رمز صحيح
        $admin->two_factor_confirmed_at = null;
        $admin->save();
        
        echo "✅ تم إلغاء التأكيد\n";
        echo "📱 الآن اذهب لصفحة الأمان وأدخل رمزاً صحيحاً من التطبيق\n";
        echo "\nالرمز السري: F33QLFCQW5P4HCDF\n";
    }
}
