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
        Schema::create('newspaper_issues', function (Blueprint $table) {
            $table->id();
            $table->string('newspaper_name'); // اسم الصحيفة
            $table->integer('issue_number'); // رقم العدد
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // وصف العدد
            $table->string('pdf_url'); // رابط الـ PDF
            $table->string('cover_image')->nullable(); // صورة الغلاف
            $table->date('publication_date'); // تاريخ النشر
            $table->integer('views')->default(0); // عدد المشاهدات
            $table->integer('downloads')->default(0); // عدد التحميلات
            $table->boolean('is_featured')->default(false); // مميز
            $table->boolean('is_published')->default(true); // منشور
            $table->unsignedBigInteger('user_id')->nullable(); // المستخدم الذي أنشأه
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('publication_date');
            $table->index('newspaper_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newspaper_issues');
    }
};
