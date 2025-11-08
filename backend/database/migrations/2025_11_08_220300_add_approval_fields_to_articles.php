<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // حالة الموافقة: draft, pending_approval, approved, rejected
            $table->enum('approval_status', ['draft', 'pending_approval', 'approved', 'rejected'])
                  ->default('draft')
                  ->after('is_published');
            
            // سبب الرفض (إذا تم رفض المقال)
            $table->text('rejection_reason')->nullable()->after('approval_status');
            
            // تاريخ الموافقة
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            
            // المستخدم الذي وافق على المقال
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            
            // تاريخ الرفض
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            
            // المستخدم الذي رفض المقال
            $table->foreignId('rejected_by')->nullable()->constrained('users')->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'approval_status',
                'rejection_reason',
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_by'
            ]);
        });
    }
};
