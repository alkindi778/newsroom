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
        // إضافة indexes بسيطة للأداء (تجنب التضارب مع indexes موجودة)
        
        // Articles - إضافة created_at فقط
        if (!$this->indexExists('articles', 'articles_created_at_index')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->index('created_at');
            });
        }

        // Videos - إضافة indexes أساسية
        if (!$this->indexExists('videos', 'videos_created_at_index')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->index('created_at');
                $table->index('video_type');
            });
        }

        // Opinions - إضافة created_at
        if (!$this->indexExists('opinions', 'opinions_created_at_index')) {
            Schema::table('opinions', function (Blueprint $table) {
                $table->index('created_at');
            });
        }

        // Push Subscriptions
        if (!$this->indexExists('push_subscriptions', 'push_subscriptions_created_at_index')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['video_type']);
        });

        Schema::table('opinions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }

    /**
     * تحقق من وجود index
     */
    private function indexExists($table, $index)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }
        return false;
    }
};
