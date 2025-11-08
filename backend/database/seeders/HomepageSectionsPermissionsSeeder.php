<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HomepageSectionsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات - استخدام الأسماء الإنجليزية مثل باقي المشروع
        $permissions = [
            'view_homepage_sections',
            'create_homepage_sections',
            'edit_homepage_sections',
            'delete_homepage_sections',
            'manage_homepage_sections',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // إعطاء جميع الصلاحيات للـ Super Admin
        $superAdminRole = Role::where('name', 'super_admin')
            ->orWhere('name', 'مدير النظام')
            ->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        // إعطاء صلاحيات محددة للـ Admin
        $adminRole = Role::where('name', 'admin')
            ->orWhere('name', 'مشرف')
            ->first();

        if ($adminRole) {
            $adminRole->givePermissionTo([
                'view_homepage_sections',
                'create_homepage_sections',
                'edit_homepage_sections',
                'manage_homepage_sections',
            ]);
        }

        // إعطاء صلاحية العرض فقط للـ Editor
        $editorRole = Role::where('name', 'editor')
            ->orWhere('name', 'محرر')
            ->first();

        if ($editorRole) {
            $editorRole->givePermissionTo('view_homepage_sections');
        }

        $this->command->info('✅ تم إنشاء صلاحيات قوالب الصفحة الرئيسية بنجاح');
    }
}
