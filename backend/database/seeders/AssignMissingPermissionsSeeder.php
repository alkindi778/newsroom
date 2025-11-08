<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AssignMissingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== إصلاح الصلاحيات المفقودة ===\n\n";
        
        // Get Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if ($superAdminRole) {
            // Give ALL permissions to Super Admin role
            $superAdminRole->syncPermissions(Permission::all());
            echo "✅ تم منح دور Super Admin جميع الصلاحيات (" . Permission::count() . " صلاحية)\n\n";
        }
        
        // Get admin user
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if ($admin) {
            // Give ALL permissions to admin user directly
            $admin->syncPermissions(Permission::all());
            echo "✅ تم منح المستخدم admin@newsroom.com جميع الصلاحيات مباشرة\n\n";
        }
        
        // Show system permissions
        echo "صلاحيات النظام:\n";
        Permission::whereIn('name', ['view_dashboard', 'manage_settings', 'view_reports', 'manage_media'])
            ->get(['name'])
            ->each(function($p) {
                echo "  - {$p->name}\n";
            });
        
        echo "\n📊 إجمالي الصلاحيات: " . Permission::count() . "\n";
    }
}
