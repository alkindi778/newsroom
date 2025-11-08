<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== فحص صلاحيات دور Super Admin ===\n\n";
        
        $role = Role::where('name', 'Super Admin')->first();
        
        if (!$role) {
            echo "❌ الدور غير موجود\n";
            return;
        }
        
        $rolePermissions = $role->permissions;
        
        echo "📊 عدد الصلاحيات في قاعدة البيانات: " . Permission::count() . "\n";
        echo "📊 عدد صلاحيات دور Super Admin: " . $rolePermissions->count() . "\n\n";
        
        if ($rolePermissions->count() < Permission::count()) {
            echo "⚠️  الدور لا يملك جميع الصلاحيات!\n\n";
            
            $allPermissions = Permission::pluck('name');
            $rolePermissionNames = $rolePermissions->pluck('name');
            $missing = $allPermissions->diff($rolePermissionNames);
            
            echo "الصلاحيات المفقودة:\n";
            foreach ($missing as $perm) {
                echo "  ❌ {$perm}\n";
            }
        } else {
            echo "✅ الدور يملك جميع الصلاحيات!\n";
        }
    }
}
