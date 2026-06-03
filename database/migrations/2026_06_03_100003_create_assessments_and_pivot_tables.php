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
        Schema::create('assessments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('duration_minutes');
            $table->integer('max_attempts')->default(1);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->decimal('passing_grade', 8, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('assessment_group', function (Blueprint $table) {
            $table->foreignUlid('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('group_id')->constrained()->cascadeOnDelete();
            $table->primary(['assessment_id', 'group_id']);
        });

        Schema::create('assessment_question', function (Blueprint $table) {
            $table->foreignUlid('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('question_id')->constrained()->cascadeOnDelete();
            $table->integer('order_no')->default(0)->index();
            $table->primary(['assessment_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_question');
        Schema::dropIfExists('assessment_group');
        Schema::dropIfExists('assessments');
    }
};
