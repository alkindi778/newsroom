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
        // تعديل ENUM لإضافة 'facebook'
        DB::statement("ALTER TABLE `videos` MODIFY COLUMN `video_type` ENUM('youtube', 'vimeo', 'local', 'facebook') DEFAULT 'youtube'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع ENUM للقيم الأصلية
        DB::statement("ALTER TABLE `videos` MODIFY COLUMN `video_type` ENUM('youtube', 'vimeo', 'local') DEFAULT 'youtube'");
    }
};
