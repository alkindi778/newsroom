<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تنظيف حقل image من الروابط المضاعفة أو الكاملة
     * عند استخدام Media Library، يجب أن يكون حقل image فارغ أو يحتوي على مسار نسبي فقط
     */
    public function up(): void
    {
        // تنظيف الروابط المضاعفة في حقل image
        DB::statement("
            UPDATE articles 
            SET image = NULL 
            WHERE image LIKE 'http://%' 
            OR image LIKE 'https://%'
            OR image LIKE '%/storage/http%'
        ");
        
        \Log::info('Migration: تم تنظيف حقل image من الروابط الكاملة والمضاعفة');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا يمكن استعادة البيانات القديمة
        \Log::info('Migration rollback: لا يمكن استعادة الروابط القديمة');
    }
};
