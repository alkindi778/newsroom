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
        Schema::create('article_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->constrained('articles')
                ->onDelete('cascade');
            $table->json('embedding');
            $table->text('text_used')->nullable()->comment('النص المستخدم لتوليد الـ embedding');
            $table->string('task_type')->default('RETRIEVAL_DOCUMENT')->comment('نوع المهمة المستخدمة');
            $table->timestamps();
            
            // Index لتسريع البحث
            $table->index('article_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_embeddings');
    }
};
