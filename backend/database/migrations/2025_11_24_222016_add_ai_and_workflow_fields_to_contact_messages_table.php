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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal')->after('status');
            $table->text('forwarding_reason')->nullable()->after('assigned_to'); // سبب التحويل
            $table->text('internal_notes')->nullable()->after('admin_notes'); // ملاحظات داخلية إضافية
            
            // حقول الذكاء الاصطناعي
            $table->text('ai_summary')->nullable()->after('message'); // ملخص
            $table->string('ai_sentiment')->nullable()->after('ai_summary'); // تحليل المشاعر (positive, negative, neutral)
            $table->text('ai_suggested_reply')->nullable()->after('ai_sentiment'); // الرد المقترح
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn([
                'priority', 
                'forwarding_reason', 
                'internal_notes',
                'ai_summary', 
                'ai_sentiment', 
                'ai_suggested_reply'
            ]);
        });
    }
};
