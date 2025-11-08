<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CleanupDuplicatePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== تنظيف الصلاحيات المكررة ===\n\n";
        
        // الصلاحيات العربية القديمة التي يجب حذفها (لديها بدائل إنجليزية)
        $arabicPermissionsToDelete = [
            'إدارة سلة المهملات',
            'استعادة الأخبار',
            'حذف نهائي للأخبار',
        ];
        
        $deletedCount = 0;
        
        foreach ($arabicPermissionsToDelete as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            
            if ($permission) {
                echo "🗑️  حذف: {$permissionName}\n";
                $permission->delete();
                $deletedCount++;
            }
        }
        
        echo "\n✅ تم حذف {$deletedCount} صلاحية مكررة\n";
        
        // عرض العدد النهائي
        $totalPermissions = Permission::count();
        echo "📊 إجمالي الصلاحيات المتبقية: {$totalPermissions}\n\n";
        
        // عرض بعض الصلاحيات
        echo "الصلاحيات الإنجليزية المتعلقة بالأخبار:\n";
        Permission::where('name', 'like', '%articles%')
            ->orWhere('name', 'like', '%trash%')
            ->get(['name'])
            ->each(function($p) {
                echo "  - {$p->name}\n";
            });
    }
}
