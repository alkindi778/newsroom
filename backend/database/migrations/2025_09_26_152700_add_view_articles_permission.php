<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة صلاحية عرض الأخبار للمستخدم الأول (Admin)
        $user = User::find(1);
        if ($user) {
            $user->givePermissionTo('عرض الأخبار');
        }
    }

    public function down(): void
    {
        // إزالة صلاحية عرض الأخبار من المستخدم الأول
        $user = User::find(1);
        if ($user) {
            $user->revokePermissionTo('عرض الأخبار');
        }
    }
};
