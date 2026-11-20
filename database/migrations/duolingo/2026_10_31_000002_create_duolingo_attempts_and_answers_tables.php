<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Duolingo attempts & answers.
 * Raw performance and estimated proficiency are stored separately (duolingo.md §6, §8.5).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('duolingo_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            // Raw performance: { "reading": {"earned": 12.5, "max": 20, "percent": 62.5}, … }
            $table->json('raw_scores')->nullable();

            // Estimated individual subscores (10–160, step 5)
            $table->unsignedTinyInteger('reading_score')->nullable();
            $table->unsignedTinyInteger('writing_score')->nullable();
            $table->unsignedTinyInteger('listening_score')->nullable();
            $table->unsignedTinyInteger('speaking_score')->nullable();

            // Integrated subscores (averages, may be x.5)
            $table->decimal('literacy_score', 5, 1)->nullable();
            $table->decimal('comprehension_score', 5, 1)->nullable();
            $table->decimal('conversation_score', 5, 1)->nullable();
            $table->decimal('production_score', 5, 1)->nullable();

            // Overall = avg of four individual subscores, rounded to nearest 5
            $table->unsignedTinyInteger('overall_score')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'module_id', 'status'], 'idx_det_attempt_lookup');
        });

        Schema::create('duolingo_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_attempt_id')->constrained('duolingo_attempts')->cascadeOnDelete();
            $table->foreignId('duolingo_module_wise_question_id')
                ->constrained('duolingo_module_wise_question')->cascadeOnDelete();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();

            // Text response (dictation, writing, essay, word_classification "yes"/"no")
            $table->longText('response_text')->nullable();
            // Connected text responses (interactive_writing: [initial, follow_up])
            $table->json('response_texts')->nullable();
            // Single recording (speaking)
            $table->string('response_audio_url', 500)->nullable();
            // Per-turn recordings (interactive_speaking: { "1": "path", "2": "path" })
            $table->json('response_audio_urls')->nullable();
            // mcq_single
            $table->json('selected_option_ids')->nullable();
            // fill_in_blanks: { "1": "ice", "2": "…" } keyed by blank_order
            $table->json('response_blanks')->nullable();
            // highlighting: { "start": 10, "end": 42, "text": "…" }
            $table->json('response_highlight')->nullable();

            // Evaluation
            $table->decimal('raw_score', 6, 2)->nullable();      // earned marks
            $table->decimal('max_score', 6, 2)->nullable();      // available marks
            $table->json('rubric_scores')->nullable();           // { "grammar": 4, "vocabulary": 3, … }
            $table->text('feedback')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['duolingo_attempt_id', 'duolingo_module_wise_question_id'], 'uq_det_attempt_mwq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duolingo_answers');
        Schema::dropIfExists('duolingo_attempts');
    }
};
