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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // نوع الإعلان
            $table->enum('type', ['banner', 'sidebar', 'popup', 'inline', 'floating'])->default('banner');
            
            // موقع الإعلان
            $table->enum('position', [
                'header', 
                'footer', 
                'sidebar_right', 
                'sidebar_left',
                'article_top',
                'article_bottom',
                'article_middle',
                'homepage_top',
                'homepage_bottom',
                'between_articles'
            ])->default('sidebar_right');
            
            // محتوى الإعلان
            $table->text('content')->nullable(); // HTML content
            $table->string('image')->nullable(); // صورة الإعلان
            $table->string('link')->nullable(); // رابط الإعلان
            $table->boolean('open_new_tab')->default(true);
            
            // الأبعاد
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // التحكم في العرض
            $table->boolean('is_active')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // الاستهداف
            $table->json('target_pages')->nullable(); // home, articles, categories, etc.
            $table->json('target_categories')->nullable(); // array of category IDs
            $table->json('target_devices')->nullable(); // desktop, mobile, tablet
            
            // الإحصائيات
            $table->bigInteger('views')->default(0);
            $table->bigInteger('clicks')->default(0);
            
            // الأولوية (للترتيب)
            $table->integer('priority')->default(0);
            
            // معلومات العميل
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            
            // ملاحظات إدارية
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('position');
            $table->index('is_active');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
