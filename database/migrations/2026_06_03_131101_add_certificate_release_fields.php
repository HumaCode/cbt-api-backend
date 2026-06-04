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
        Schema::table('assessments', function (Blueprint $table) {
            $table->enum('certificate_release_mode', ['auto', 'manual'])->default('auto')->after('passing_grade');
        });

        Schema::table('assessment_sessions', function (Blueprint $table) {
            $table->boolean('is_certificate_released')->default(false)->after('total_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('certificate_release_mode');
        });

        Schema::table('assessment_sessions', function (Blueprint $table) {
            $table->dropColumn('is_certificate_released');
        });
    }
};
