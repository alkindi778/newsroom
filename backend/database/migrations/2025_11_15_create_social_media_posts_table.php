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
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->enum('platform', ['facebook', 'twitter', 'telegram']); // المنصة
            $table->string('external_id')->nullable(); // معرّف المنشور على المنصة
            $table->text('message'); // نص المنشور
            $table->enum('status', ['pending', 'published', 'failed', 'scheduled'])->default('pending');
            $table->text('error_message')->nullable(); // رسالة الخطأ إن وجدت
            $table->timestamp('published_at')->nullable(); // وقت النشر الفعلي
            $table->timestamp('scheduled_for')->nullable(); // وقت الجدولة
            $table->integer('likes')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('comments')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('article_id');
            $table->index('platform');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_posts');
    }
};
