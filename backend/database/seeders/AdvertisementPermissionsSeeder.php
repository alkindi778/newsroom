<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdvertisementPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            'view_advertisements',
            'create_advertisements',
            'edit_advertisements',
            'delete_advertisements',
            'manage_advertisements',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // منح الصلاحيات للأدوار
        
        // Admin: جميع الصلاحيات
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'view_advertisements',
                'create_advertisements',
                'edit_advertisements',
                'delete_advertisements',
                'manage_advertisements',
            ]);
        }

        // Editor: عرض، إنشاء، تعديل
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole) {
            $editorRole->givePermissionTo([
                'view_advertisements',
                'create_advertisements',
                'edit_advertisements',
            ]);
        }

        // إنشاء دور جديد: مدير الإعلانات
        $adsManagerRole = Role::firstOrCreate(
            ['name' => 'ads_manager', 'guard_name' => 'web']
        );

        // منح جميع صلاحيات الإعلانات لمدير الإعلانات
        $adsManagerRole->givePermissionTo([
            'view_advertisements',
            'create_advertisements',
            'edit_advertisements',
            'delete_advertisements',
            'manage_advertisements',
        ]);

        $this->command->info('✅ Advertisement permissions and roles created successfully!');
    }
}
