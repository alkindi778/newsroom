<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `position` ENUM(
            'header', 
            'footer', 
            'sidebar_right', 
            'sidebar_left',
            'article_top',
            'article_bottom',
            'article_middle',
            'homepage_top',
            'homepage_bottom',
            'between_articles',
            'between_sections'
        ) DEFAULT 'sidebar_right'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `advertisements` MODIFY COLUMN `position` ENUM(
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
        ) DEFAULT 'sidebar_right'");
    }
};
