<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AssignAllPermissionsToAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user
        $admin = User::where('email', 'admin@newsroom.com')->first();
        
        if ($admin) {
            // Give all permissions
            $admin->syncPermissions(Permission::all());
            
            echo "\n✅ تم منح المدير العام جميع الصلاحيات\n";
            echo "المستخدم: {$admin->name}\n";
            echo "عدد الصلاحيات: " . Permission::count() . "\n";
        } else {
            echo "\n❌ لم يتم العثور على المستخدم admin@newsroom.com\n";
        }
    }
}
