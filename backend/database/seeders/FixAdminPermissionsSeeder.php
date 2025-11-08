<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class FixAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== إصلاح صلاحيات المدير العام ===\n\n";
        
        // Get admin user
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if (!$admin) {
            echo "❌ لم يتم العثور على المدير\n";
            return;
        }
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        echo "📊 عدد الصلاحيات الموجودة: " . $allPermissions->count() . "\n";
        echo "👤 المستخدم: {$admin->name}\n\n";
        
        // Sync all permissions
        $admin->syncPermissions($allPermissions);
        
        echo "✅ تم منح المدير جميع الصلاحيات!\n\n";
        
        // Show some of the permissions
        echo "الصلاحيات الإنجليزية:\n";
        $englishPerms = Permission::where('name', 'not like', '%ا%')
            ->where('name', 'not like', '%ر%')
            ->limit(10)
            ->get(['name']);
        
        foreach ($englishPerms as $perm) {
            echo "  - {$perm->name}\n";
        }
        
        echo "\nالصلاحيات العربية:\n";
        $arabicPerms = Permission::where('name', 'like', '%ا%')
            ->orWhere('name', 'like', '%ر%')
            ->limit(10)
            ->get(['name']);
        
        foreach ($arabicPerms as $perm) {
            echo "  - {$perm->name}\n";
        }
        
        echo "\n✅ تم الإصلاح بنجاح!\n";
    }
}
