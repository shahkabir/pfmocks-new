<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pte_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'module_id', 'status']);
        });

        Schema::create('pte_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pte_attempt_id')->constrained('pte_attempts')->cascadeOnDelete();
            $table->foreignId('pte_module_wise_question_id')
                ->constrained('pte_module_wise_question')->cascadeOnDelete();
            $table->foreignId('pte_question_granular_id')
                ->constrained('pte_question_granular')->cascadeOnDelete();

            // Free-form text response (text_write, write-from-dictation, ASQ if typed)
            $table->text('response_text')->nullable();
            // Recorded audio path for audio_record types
            $table->string('response_audio_url', 500)->nullable();
            // Selected option IDs for MCQ Single/Multi, Select Missing Word, Highlight Correct Summary
            $table->json('selected_option_ids')->nullable();
            // Per-blank answers for fill_blank types ({ "1": "foo", "2": "bar" })
            $table->json('response_blanks')->nullable();
            // Ordered list of segment IDs for re-order types
            $table->json('response_segments_order')->nullable();
            // IDs of words user clicked for Highlight Incorrect Words
            $table->json('response_highlight_word_ids')->nullable();

            $table->decimal('score', 6, 2)->nullable();
            $table->timestamps();

            $table->unique(['pte_attempt_id', 'pte_module_wise_question_id'], 'uq_attempt_mwq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pte_answers');
        Schema::dropIfExists('pte_attempts');
    }
};
