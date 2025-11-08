<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OpinionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء صلاحيات كُتاب الرأي
        $writerPermissions = [
            'عرض كُتاب الرأي',
            'إنشاء كُتاب الرأي', 
            'تعديل كُتاب الرأي',
            'حذف كُتاب الرأي',
            'إدارة كُتاب الرأي',
        ];

        // إنشاء صلاحيات مقالات الرأي
        $opinionPermissions = [
            'عرض مقالات الرأي',
            'إنشاء مقالات الرأي',
            'تعديل مقالات الرأي', 
            'حذف مقالات الرأي',
            'نشر مقالات الرأي',
            'إدارة مقالات الرأي',
        ];

        // دمج جميع الصلاحيات
        $allPermissions = array_merge($writerPermissions, $opinionPermissions);

        // إنشاء الصلاحيات
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // إضافة الصلاحيات لدور Super Admin إذا كان موجوداً
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }

        // إضافة الصلاحيات لدور Admin إذا كان موجوداً
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Admin يحصل على جميع الصلاحيات عدا حذف الكُتاب
            $adminPermissions = array_diff($allPermissions, ['حذف كُتاب الرأي']);
            $adminRole->givePermissionTo($adminPermissions);
        }

        // إنشاء دور "محرر مقالات الرأي" 
        $opinionEditorRole = Role::firstOrCreate([
            'name' => 'محرر مقالات الرأي',
            'guard_name' => 'web'
        ]);

        // صلاحيات محرر مقالات الرأي
        $editorPermissions = [
            'عرض كُتاب الرأي',
            'عرض مقالات الرأي', 
            'إنشاء مقالات الرأي',
            'تعديل مقالات الرأي',
            'حذف مقالات الرأي',
        ];

        $opinionEditorRole->givePermissionTo($editorPermissions);

        // إنشاء دور "كاتب رأي"
        $writerRole = Role::firstOrCreate([
            'name' => 'كاتب رأي',
            'guard_name' => 'web'
        ]);

        // صلاحيات كاتب الرأي - يمكنه فقط إنشاء وتعديل مقالاته
        $writerPermissions = [
            'عرض مقالات الرأي',
            'إنشاء مقالات الرأي', 
            'تعديل مقالات الرأي',
        ];

        $writerRole->givePermissionTo($writerPermissions);

        $this->command->info('تم إنشاء صلاحيات نظام كُتاب الرأي ومقالات الرأي بنجاح');
        $this->command->info('الصلاحيات المُنشأة:');
        
        foreach ($allPermissions as $permission) {
            $this->command->line("- {$permission}");
        }

        $this->command->info('الأدوار المُنشأة/المُحدثة:');
        $this->command->line("- محرر مقالات الرأي");
        $this->command->line("- كاتب رأي"); 
        $this->command->line("- Super Admin (إذا كان موجوداً)");
        $this->command->line("- Admin (إذا كان موجوداً)");
    }
}
