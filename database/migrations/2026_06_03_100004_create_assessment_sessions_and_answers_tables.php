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
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('start_time')->index();
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_timer_started')->default(false)->index();
            $table->enum('status', ['in_progress', 'completed', 'force_submitted'])->default('in_progress')->index();
            $table->decimal('total_score', 8, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('session_answers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('session_id')->constrained('assessment_sessions')->cascadeOnDelete();
            $table->foreignUlid('question_id')->constrained()->cascadeOnDelete();
            $table->ulid('selected_option_id')->nullable()->index();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->default(false)->index();
            $table->decimal('score_earned', 8, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('selected_option_id')->references('id')->on('question_options')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_answers');
        Schema::dropIfExists('assessment_sessions');
    }
};
