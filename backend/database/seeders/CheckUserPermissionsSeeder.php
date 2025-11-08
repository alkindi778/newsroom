<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CheckUserPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ لم يتم العثور على المدير\n";
            return;
        }
        
        echo "\n=== فحص صلاحيات المدير ===\n\n";
        echo "👤 المستخدم: {$admin->name}\n";
        echo "📧 البريد: {$admin->email}\n\n";
        
        // Check specific permissions
        $permsToCheck = [
            'view_dashboard',
            'view_articles',
            'create_articles',
            'view_categories',
            'view_users',
            'manage_roles',
        ];
        
        echo "الصلاحيات المطلوبة:\n";
        foreach ($permsToCheck as $perm) {
            $has = $admin->can($perm) ? '✅' : '❌';
            echo "  {$has} {$perm}\n";
        }
        
        echo "\nعدد الصلاحيات الكلي: " . $admin->getAllPermissions()->count() . "\n";
        
        // Show roles
        echo "\nالأدوار:\n";
        foreach ($admin->getRoleNames() as $role) {
            echo "  - {$role}\n";
        }
    }
}
