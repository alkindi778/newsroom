<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة صلاحية إنشاء الأخبار للمستخدم الأول
        $user = User::find(1);
        if ($user) {
            $user->givePermissionTo('إنشاء الأخبار');
        }
    }

    public function down(): void
    {
        // إزالة صلاحية إنشاء الأخبار من المستخدم الأول
        $user = User::find(1);
        if ($user) {
            $user->revokePermissionTo('إنشاء الأخبار');
        }
    }
};
