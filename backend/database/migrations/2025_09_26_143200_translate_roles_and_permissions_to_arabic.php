<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعريب أسماء الأدوار
        $roleTranslations = [
            'Super Admin' => 'المدير العام',
            'Admin' => 'مدير النظام',
            'Editor' => 'محرر',
            'Author' => 'كاتب',
            'Moderator' => 'مشرف',
        ];

        foreach ($roleTranslations as $english => $arabic) {
            DB::table('roles')->where('name', $english)->update(['name' => $arabic]);
        }

        // تعريب أسماء الصلاحيات
        $permissionTranslations = [
            // Articles - الأخبار
            'create_articles' => 'إنشاء الأخبار',
            'edit_articles' => 'تعديل الأخبار',
            'delete_articles' => 'حذف الأخبار',
            'publish_articles' => 'نشر الأخبار',
            'manage_articles' => 'إدارة الأخبار',
            'view_articles' => 'عرض الأخبار',
            
            // Categories - الأقسام
            'create_categories' => 'إنشاء الأقسام',
            'edit_categories' => 'تعديل الأقسام',
            'delete_categories' => 'حذف الأقسام',
            'manage_categories' => 'إدارة الأقسام',
            'view_categories' => 'عرض الأقسام',
            
            // Users - المستخدمين
            'create_users' => 'إنشاء المستخدمين',
            'edit_users' => 'تعديل المستخدمين',
            'delete_users' => 'حذف المستخدمين',
            'manage_users' => 'إدارة المستخدمين',
            'view_users' => 'عرض المستخدمين',
            
            // Roles - الأدوار
            'create_roles' => 'إنشاء الأدوار',
            'edit_roles' => 'تعديل الأدوار',
            'delete_roles' => 'حذف الأدوار',
            'manage_roles' => 'إدارة الأدوار',
            'view_roles' => 'عرض الأدوار',
            
            // Permissions - الصلاحيات
            'create_permissions' => 'إنشاء الصلاحيات',
            'edit_permissions' => 'تعديل الصلاحيات',
            'delete_permissions' => 'حذف الصلاحيات',
            'manage_permissions' => 'إدارة الصلاحيات',
            'view_permissions' => 'عرض الصلاحيات',
            
            // Own Articles - الأخبار الشخصية
            'edit_own_articles' => 'تعديل الأخبار الشخصية',
            'delete_own_articles' => 'حذف الأخبار الشخصية',
            
            // Media - الوسائط
            'manage_media' => 'إدارة الوسائط',
            'upload_media' => 'رفع الوسائط',
            'delete_media' => 'حذف الوسائط',
            
            // Settings - الإعدادات
            'manage_settings' => 'إدارة الإعدادات',
            'view_settings' => 'عرض الإعدادات',
            
            // Dashboard - لوحة التحكم
            'view_dashboard' => 'عرض لوحة التحكم',
            'manage_dashboard' => 'إدارة لوحة التحكم',
            
            // Reports - التقارير
            'view_reports' => 'عرض التقارير',
            'create_reports' => 'إنشاء التقارير',
            'export_reports' => 'تصدير التقارير',
            
            // Comments - التعليقات
            'manage_comments' => 'إدارة التعليقات',
            'moderate_comments' => 'مراجعة التعليقات',
            'delete_comments' => 'حذف التعليقات',
            
            // Tags - العلامات
            'manage_tags' => 'إدارة العلامات',
            'create_tags' => 'إنشاء العلامات',
            'delete_tags' => 'حذف العلامات',
        ];

        foreach ($permissionTranslations as $english => $arabic) {
            DB::table('permissions')->where('name', $english)->update(['name' => $arabic]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // استرجاع أسماء الأدوار للإنجليزية
        $roleTranslations = [
            'المدير العام' => 'Super Admin',
            'مدير النظام' => 'Admin',
            'محرر' => 'Editor',
            'كاتب' => 'Author',
            'مشرف' => 'Moderator',
        ];

        foreach ($roleTranslations as $arabic => $english) {
            DB::table('roles')->where('name', $arabic)->update(['name' => $english]);
        }

        // استرجاع أسماء الصلاحيات للإنجليزية
        $permissionTranslations = [
            'إنشاء الأخبار' => 'create_articles',
            'تعديل الأخبار' => 'edit_articles',
            'حذف الأخبار' => 'delete_articles',
            'نشر الأخبار' => 'publish_articles',
            'إدارة الأخبار' => 'manage_articles',
            'عرض الأخبار' => 'view_articles',
            
            'إنشاء الأقسام' => 'create_categories',
            'تعديل الأقسام' => 'edit_categories',
            'حذف الأقسام' => 'delete_categories',
            'إدارة الأقسام' => 'manage_categories',
            'عرض الأقسام' => 'view_categories',
            
            'إنشاء المستخدمين' => 'create_users',
            'تعديل المستخدمين' => 'edit_users',
            'حذف المستخدمين' => 'delete_users',
            'إدارة المستخدمين' => 'manage_users',
            'عرض المستخدمين' => 'view_users',
            
            'إنشاء الأدوار' => 'create_roles',
            'تعديل الأدوار' => 'edit_roles',
            'حذف الأدوار' => 'delete_roles',
            'إدارة الأدوار' => 'manage_roles',
            'عرض الأدوار' => 'view_roles',
            
            'إنشاء الصلاحيات' => 'create_permissions',
            'تعديل الصلاحيات' => 'edit_permissions',
            'حذف الصلاحيات' => 'delete_permissions',
            'إدارة الصلاحيات' => 'manage_permissions',
            'عرض الصلاحيات' => 'view_permissions',
            
            'تعديل الأخبار الشخصية' => 'edit_own_articles',
            'حذف الأخبار الشخصية' => 'delete_own_articles',
            
            'إدارة الوسائط' => 'manage_media',
            'رفع الوسائط' => 'upload_media',
            'حذف الوسائط' => 'delete_media',
            
            'إدارة الإعدادات' => 'manage_settings',
            'عرض الإعدادات' => 'view_settings',
            
            'عرض لوحة التحكم' => 'view_dashboard',
            'إدارة لوحة التحكم' => 'manage_dashboard',
            
            'عرض التقارير' => 'view_reports',
            'إنشاء التقارير' => 'create_reports',
            'تصدير التقارير' => 'export_reports',
            
            'إدارة التعليقات' => 'manage_comments',
            'مراجعة التعليقات' => 'moderate_comments',
            'حذف التعليقات' => 'delete_comments',
            
            'إدارة العلامات' => 'manage_tags',
            'إنشاء العلامات' => 'create_tags',
            'حذف العلامات' => 'delete_tags',
        ];

        foreach ($permissionTranslations as $arabic => $english) {
            DB::table('permissions')->where('name', $arabic)->update(['name' => $english]);
        }
    }
};
