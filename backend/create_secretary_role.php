<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    $roleName = 'سكرتير';
    $guardName = 'web';

    $permissionsToAssign = [
        'view_dashboard',
        'view_contact_messages',
        'manage_contact_messages',
        'assign_contact_messages',
    ];

    // إنشاء / جلب الدور
    $role = Role::firstOrCreate(
        ['name' => $roleName, 'guard_name' => $guardName],
        ['name' => $roleName, 'guard_name' => $guardName]
    );

    // التأكد من وجود الصلاحيات
    foreach ($permissionsToAssign as $permissionName) {
        Permission::firstOrCreate(
            ['name' => $permissionName, 'guard_name' => $guardName],
            ['name' => $permissionName, 'guard_name' => $guardName]
        );
    }

    // ربط الصلاحيات بالدور
    $role->syncPermissions($permissionsToAssign);

    echo "✅ تم إنشاء/تحديث دور سكرتير بنجاح.\n";
    echo "- اسم الدور: {$roleName}\n";
    echo "- الصلاحيات:\n";
    foreach ($permissionsToAssign as $p) {
        echo "  • {$p}\n";
    }

    echo "\nℹ️ لإسناد الدور لمستخدم معيّن، يمكنك استخدام Tinker مثلاً:\n";
    echo "   php artisan tinker\n";
    echo "   >>> $" . "user = \\App\\Models\\User::where('email', 'USER_EMAIL_HERE')->first();\n";
    echo "   >>> $" . "user?->assignRole('{$roleName}');\n";

} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
