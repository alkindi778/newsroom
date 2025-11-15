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
        Schema::table('social_media_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('video_id')->nullable()->after('article_id');
            $table->unsignedBigInteger('opinion_id')->nullable()->after('video_id');
            
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('opinion_id')->references('id')->on('opinions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_media_posts', function (Blueprint $table) {
            $table->dropForeign(['video_id']);
            $table->dropForeign(['opinion_id']);
            $table->dropColumn(['video_id', 'opinion_id']);
        });
    }
};
