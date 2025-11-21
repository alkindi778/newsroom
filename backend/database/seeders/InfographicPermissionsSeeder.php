<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InfographicPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // إنشاء الصلاحيات للإنفوجرافيك لكل من web و api guards
        $permissions = [
            'view_infographics',
            'create_infographics',
            'edit_infographics',
            'delete_infographics',
            'manage_infographics',
        ];

        foreach (['web', 'api'] as $guard) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard]
                );
            }
        }

        // إعطاء جميع الصلاحيات للـ Super Admin (جميع guards)
        $superAdminRoles = Role::where('name', 'super_admin')
            ->orWhere('name', 'مدير النظام')
            ->get();

        foreach ($superAdminRoles as $role) {
            try {
                foreach ($permissions as $permission) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء وإكمال العمل
            }
        }

        // إعطاء صلاحيات محددة للـ Admin (جميع guards)
        $adminRoles = Role::where('name', 'admin')
            ->orWhere('name', 'مشرف')
            ->get();

        foreach ($adminRoles as $role) {
            try {
                $adminPermissions = [
                    'view_infographics',
                    'create_infographics',
                    'edit_infographics',
                    'manage_infographics',
                ];
                foreach ($adminPermissions as $permission) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء وإكمال العمل
            }
        }

        // إعطاء صلاحيات للـ Editor (جميع guards)
        $editorRoles = Role::where('name', 'editor')
            ->orWhere('name', 'محرر')
            ->get();

        foreach ($editorRoles as $role) {
            try {
                $editorPermissions = [
                    'view_infographics',
                    'create_infographics',
                    'edit_infographics',
                ];
                foreach ($editorPermissions as $permission) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء وإكمال العمل
            }
        }

        // إعطاء صلاحية العرض فقط للـ Reporter (جميع guards)
        $reporterRoles = Role::where('name', 'reporter')
            ->orWhere('name', 'مراسل')
            ->get();

        foreach ($reporterRoles as $role) {
            try {
                if (!$role->hasPermissionTo('view_infographics')) {
                    $role->givePermissionTo('view_infographics');
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء وإكمال العمل
            }
        }

        $this->command->info('✅ تم إنشاء صلاحيات الإنفوجرافيك بنجاح');
    }
}
