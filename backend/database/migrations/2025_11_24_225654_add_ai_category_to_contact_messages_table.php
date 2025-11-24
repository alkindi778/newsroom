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
            // تصنيف الرسالة التلقائي من AI
            $table->enum('ai_category', ['complaint', 'inquiry', 'meeting_request', 'suggestion', 'praise', 'other'])
                  ->nullable()
                  ->after('ai_suggested_reply');
            
            // وقت أول رد (لحساب متوسط وقت الاستجابة)
            $table->timestamp('first_reply_at')->nullable()->after('read_at');
            
            // عدد الردود
            $table->integer('replies_count')->default(0)->after('first_reply_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['ai_category', 'first_reply_at', 'replies_count']);
        });
    }
};
