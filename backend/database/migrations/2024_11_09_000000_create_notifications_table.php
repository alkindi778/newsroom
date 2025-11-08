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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع الإشعار
            $table->unsignedBigInteger('user_id'); // المستخدم المستلم
            $table->unsignedBigInteger('sender_id')->nullable(); // المرسل
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // محتوى الإشعار
            $table->string('icon')->nullable(); // أيقونة الإشعار
            $table->string('link')->nullable(); // رابط الإشعار
            $table->json('data')->nullable(); // بيانات إضافية
            $table->boolean('is_read')->default(false); // هل تم القراءة
            $table->timestamp('read_at')->nullable(); // وقت القراءة
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('sender_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_at');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
