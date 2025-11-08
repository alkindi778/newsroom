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
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_published')->default(true)->after('content');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->text('excerpt')->nullable()->after('published_at');
            $table->string('meta_description')->nullable()->after('excerpt');
            $table->string('keywords')->nullable()->after('meta_description');
            $table->integer('views')->default(0)->after('keywords');
            $table->string('featured_image')->nullable()->after('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'is_published',
                'published_at',
                'excerpt', 
                'meta_description',
                'keywords',
                'views',
                'featured_image'
            ]);
        });
    }
};
