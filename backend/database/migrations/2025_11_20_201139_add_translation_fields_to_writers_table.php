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
        Schema::table('writers', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->text('bio_en')->nullable()->after('bio');
            $table->string('position_en')->nullable()->after('position');
            $table->string('specialization_en')->nullable()->after('specialization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writers', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'bio_en', 'position_en', 'specialization_en']);
        });
    }
};
