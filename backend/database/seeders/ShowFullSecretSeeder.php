<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ShowFullSecretSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if ($admin && $admin->two_factor_secret) {
            $secret = decrypt($admin->two_factor_secret);
            echo "\n🔑 السر الكامل: {$secret}\n\n";
            echo "استخدم هذا السر في تطبيق المصادقة!\n\n";
        }
    }
}
