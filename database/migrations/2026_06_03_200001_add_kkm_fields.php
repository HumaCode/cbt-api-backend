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
        // Add optional passing_grade to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('passing_grade', 8, 2)->nullable()->after('name')
                ->comment('KKM per kategori (optional)');
        });

        // Add passing_grade_type to assessments
        // 'overall' = gunakan passing_grade dari assessment
        // 'per_category' = gunakan passing_grade dari masing-masing kategori soal
        Schema::table('assessments', function (Blueprint $table) {
            $table->enum('passing_grade_type', ['overall', 'per_category'])->default('overall')->after('passing_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('passing_grade');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('passing_grade_type');
        });
    }
};
