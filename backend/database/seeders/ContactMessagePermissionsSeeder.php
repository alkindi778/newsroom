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
            'view_contact_dashboard' => 'عرض لوحة إحصائيات الرسائل',
            'manage_reply_templates' => 'إدارة قوالب الردود',
            'reply_contact_messages' => 'الرد على رسائل التواصل',
        ];

        // إنشاء الصلاحيات لكل الـ guards
        $guards = ['web', 'api'];
        foreach ($guards as $guard) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => $guard]
                );
            }
        }

        // منح الصلاحيات للأدوار (حسب guard كل دور)
        $permissionNames = array_keys($permissions);
        
        // Super Admin - جميع الصلاحيات
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            foreach ($permissionNames as $perm) {
                $permission = Permission::where('name', $perm)->where('guard_name', $superAdmin->guard_name)->first();
                if ($permission && !$superAdmin->hasPermissionTo($permission)) {
                    $superAdmin->givePermissionTo($permission);
                }
            }
        }

        // Admin - جميع الصلاحيات
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            foreach ($permissionNames as $perm) {
                $permission = Permission::where('name', $perm)->where('guard_name', $admin->guard_name)->first();
                if ($permission && !$admin->hasPermissionTo($permission)) {
                    $admin->givePermissionTo($permission);
                }
            }
        }

        // Editor - عرض وتحديث الحالة فقط
        $editorPerms = ['view_contact_messages', 'update_contact_message_status'];
        $editor = Role::where('name', 'Editor')->first();
        if ($editor) {
            foreach ($editorPerms as $perm) {
                $permission = Permission::where('name', $perm)->where('guard_name', $editor->guard_name)->first();
                if ($permission && !$editor->hasPermissionTo($permission)) {
                    $editor->givePermissionTo($permission);
                }
            }
        }

        // Author - عرض فقط
        $author = Role::where('name', 'Author')->first();
        if ($author) {
            $permission = Permission::where('name', 'view_contact_messages')->where('guard_name', $author->guard_name)->first();
            if ($permission && !$author->hasPermissionTo($permission)) {
                $author->givePermissionTo($permission);
            }
        }

        // السكرتير - إدارة كاملة لرسائل التواصل
        $secretaryPerms = [
            'view_contact_messages',
            'manage_contact_messages',
            'update_contact_message_status',
            'view_contact_dashboard',
            'manage_reply_templates',
            'reply_contact_messages',
        ];
        $secretary = Role::where('name', 'سكرتير')->first();
        if ($secretary) {
            foreach ($secretaryPerms as $perm) {
                $permission = Permission::where('name', $perm)->where('guard_name', $secretary->guard_name)->first();
                if ($permission && !$secretary->hasPermissionTo($permission)) {
                    $secretary->givePermissionTo($permission);
                }
            }
        }

        $this->command->info('تم إنشاء صلاحيات رسائل التواصل بنجاح!');
    }
}
