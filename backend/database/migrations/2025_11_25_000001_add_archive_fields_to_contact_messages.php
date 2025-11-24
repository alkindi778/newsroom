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
            $table->boolean('is_archived')->default(false)->after('approval_notes');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->foreignId('archived_by')->nullable()->after('archived_at')->constrained('users')->nullOnDelete();
            $table->string('archive_category')->nullable()->after('archived_by'); // تصنيف الأرشيف
            $table->text('archive_summary')->nullable()->after('archive_category'); // ملخص AI
            $table->json('archive_tags')->nullable()->after('archive_summary'); // وسوم AI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'is_archived',
                'archived_at',
                'archived_by',
                'archive_category',
                'archive_summary',
                'archive_tags',
            ]);
        });
    }
};
