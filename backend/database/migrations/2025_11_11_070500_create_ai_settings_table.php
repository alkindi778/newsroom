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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        DB::table('ai_settings')->insert([
            [
                'key' => 'ai_agent_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'تفعيل/تعطيل وكيل الأخبار الآلي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_publish_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'السماح بالنشر التلقائي للأخبار ذات الثقة العالية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_publish_threshold',
                'value' => '90',
                'type' => 'integer',
                'description' => 'الحد الأدنى لدرجة الثقة للنشر التلقائي (0-100)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimum_trust_score',
                'value' => '70',
                'type' => 'integer',
                'description' => 'الحد الأدنى لدرجة الثقة لقبول المقال كمسودة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'n8n_schedule_interval',
                'value' => '10',
                'type' => 'integer',
                'description' => 'مدة التحديث بالدقائق (5, 10, 30, 60)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'notification_emails',
                'value' => '[]',
                'type' => 'json',
                'description' => 'قائمة الإيميلات التي تستقبل إشعارات المقالات الجديدة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'daily_article_limit',
                'value' => '50',
                'type' => 'integer',
                'description' => 'الحد الأقصى للمقالات المولدة يومياً',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
