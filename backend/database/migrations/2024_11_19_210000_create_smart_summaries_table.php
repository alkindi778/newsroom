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
        Schema::create('smart_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('content_hash', 64)->unique()->index(); // SHA256 hash للمحتوى
            $table->text('original_content_sample'); // عينة من المحتوى الأصلي (أول 500 حرف)
            $table->longText('summary'); // الملخص المولد
            $table->enum('type', ['news', 'opinion', 'analysis'])->default('news');
            $table->enum('length', ['short', 'medium', 'long'])->default('medium');
            $table->integer('word_count')->nullable();
            $table->integer('compression_ratio')->nullable();
            $table->integer('original_length')->nullable();
            $table->integer('usage_count')->default(1); // عدد مرات الاستخدام
            $table->timestamp('last_used_at')->useCurrent(); // آخر استخدام
            $table->timestamps();
            
            // Indexes للأداء
            $table->index(['type', 'length']);
            $table->index('created_at');
            $table->index('last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_summaries');
    }
};
