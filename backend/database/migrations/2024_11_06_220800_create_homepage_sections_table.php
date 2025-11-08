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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القسم (آخر الأخبار، التقارير، إلخ)
            $table->string('slug')->unique(); // معرف فريد للقسم
            $table->string('type'); // نوع القسم: articles, opinions, trending, slider
            $table->string('title')->nullable(); // العنوان المعروض
            $table->string('subtitle')->nullable(); // عنوان فرعي
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); // القسم المرتبط
            $table->integer('order')->default(0); // ترتيب العرض
            $table->integer('items_count')->default(6); // عدد العناصر المعروضة
            $table->boolean('is_active')->default(true); // تفعيل/تعطيل القسم
            $table->json('settings')->nullable(); // إعدادات إضافية (layout, style, etc)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
