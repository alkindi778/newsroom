<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddMissingSystemPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== إضافة صلاحيات النظام المفقودة ===\n\n";
        
        $role = Role::where('name', 'Super Admin')->first();
        
        if (!$role) {
            echo "❌ الدور غير موجود\n";
            return;
        }
        
        // الصلاحيات المفقودة
        $missingPermissions = ['manage_media', 'manage_settings', 'view_reports'];
        
        foreach ($missingPermissions as $permName) {
            $permission = Permission::where('name', $permName)->first();
            
            if ($permission) {
                if (!$role->hasPermissionTo($permName)) {
                    $role->givePermissionTo($permName);
                    echo "✅ تمت إضافة: {$permName}\n";
                } else {
                    echo "⏭️  موجودة مسبقاً: {$permName}\n";
                }
            } else {
                echo "❌ الصلاحية غير موجودة: {$permName}\n";
            }
        }
        
        echo "\n📊 عدد صلاحيات الدور الآن: " . $role->permissions()->count() . "\n";
        echo "📊 إجمالي الصلاحيات: " . Permission::count() . "\n";
    }
}
