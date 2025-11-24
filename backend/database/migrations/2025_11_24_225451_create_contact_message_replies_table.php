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
        Schema::create('contact_message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained('contact_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // من أرسل الرد
            $table->enum('type', ['email', 'internal_note', 'system'])->default('email'); // نوع الرد
            $table->text('content'); // محتوى الرد
            $table->string('subject')->nullable(); // عنوان البريد
            $table->boolean('sent_successfully')->default(false); // هل تم الإرسال بنجاح
            $table->timestamp('sent_at')->nullable(); // وقت الإرسال
            $table->string('email_message_id')->nullable(); // معرف رسالة البريد
            $table->timestamps();
            
            $table->index('contact_message_id');
            $table->index('user_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');
    }
};
