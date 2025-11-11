<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ContactMessagePermissionsSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الصلاحيات الخاصة برسائل الاتصال
        $permissions = [
            'view_contact_messages' => 'عرض رسائل التواصل',
            'manage_contact_messages' => 'إدارة رسائل التواصل',
            'assign_contact_messages' => 'تكليف رسائل التواصل للموظفين',
            'delete_contact_messages' => 'حذف رسائل التواصل',
            'update_contact_message_status' => 'تحديث حالة رسائل التواصل',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // منح الصلاحيات للأدوار
        
        // Super Admin - جميع الصلاحيات
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_keys($permissions));
        }

        // Admin - جميع الصلاحيات
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(array_keys($permissions));
        }

        // Editor - عرض وتحديث الحالة فقط
        $editor = Role::where('name', 'Editor')->first();
        if ($editor) {
            $editor->givePermissionTo([
                'view_contact_messages',
                'update_contact_message_status'
            ]);
        }

        // Author - عرض فقط
        $author = Role::where('name', 'Author')->first();
        if ($author) {
            $author->givePermissionTo(['view_contact_messages']);
        }

        $this->command->info('تم إنشاء صلاحيات رسائل التواصل بنجاح!');
    }
}
