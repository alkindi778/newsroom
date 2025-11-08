<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ReporterRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات الجديدة المتعلقة بالموافقة
        $approvalPermissions = [
            'تقديم المقالات للموافقة',
            'الموافقة على المقالات',
            'رفض المقالات',
            'عرض المقالات المعلقة',
            'تعديل المقالات الخاصة المعلقة',
        ];

        foreach ($approvalPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
        }

        // إنشاء دور "مراسل صحفي"
        $reporterRole = Role::firstOrCreate(
            ['name' => 'مراسل صحفي'],
            ['guard_name' => 'web']
        );

        // صلاحيات المراسل الصحفي
        $reporterPermissions = [
            // عرض الأقسام
            'view_dashboard',
            'عرض الأخبار',
            'view_categories',
            
            // إدارة المقالات (مع قيود)
            'إنشاء الأخبار',
            'edit_own_articles',
            'تقديم المقالات للموافقة',
            'عرض المقالات المعلقة',
            'تعديل المقالات الخاصة المعلقة',
            
            // صلاحيات الميديا
            'manage_media',
        ];

        // إضافة الصلاحيات للدور
        foreach ($reporterPermissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $reporterRole->givePermissionTo($perm);
            }
        }

        // تحديث صلاحيات المدير (Super Admin) لتشمل الموافقة
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'الموافقة على المقالات',
                'رفض المقالات',
                'عرض المقالات المعلقة',
            ]);
        }

        // تحديث صلاحيات المحرر لتشمل الموافقة
        $editor = Role::where('name', 'محرر')->first();
        if ($editor) {
            $editor->givePermissionTo([
                'الموافقة على المقالات',
                'رفض المقالات',
                'عرض المقالات المعلقة',
            ]);
        }

        $this->command->info('✅ تم إنشاء دور المراسل الصحفي وصلاحيات الموافقة بنجاح!');
    }
}
