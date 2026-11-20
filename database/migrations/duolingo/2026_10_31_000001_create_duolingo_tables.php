<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Duolingo English Test (DET) — question bank & mock-test assembly.
 * Static catalogue lives in config/duolingo.php (see duolingo.md).
 */
return new class extends Migration {
    public function up(): void
    {
        // 1. Sections = the four individual skills (Reading, Listening, Writing, Speaking)
        Schema::create('duolingo_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('tag', 1)->unique();               // R / L / W / S
            $table->unsignedTinyInteger('display_order');
            $table->unsignedSmallInteger('time_allowed_minutes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Bridge: module -> duolingo_section
        Schema::create('duolingo_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('duolingo_section_id')->constrained('duolingo_sections')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['module_id', 'duolingo_section_id'], 'uq_det_module_section');
        });

        // 3. The DET question types (Read and Complete, Listen and Type, …)
        Schema::create('duolingo_question_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_section_id')->constrained('duolingo_sections')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('tag', 10)->unique();
            $table->enum('response_type', [
                'fill_in_blanks', 'word_classification', 'mcq_single', 'highlighting',
                'dictation', 'writing', 'interactive_writing', 'essay',
                'speaking', 'interactive_speaking',
            ]);
            $table->enum('stimulus_type', ['text', 'audio', 'image', 'word', 'none']);
            $table->enum('scoring_method', ['objective', 'rubric'])->default('objective');
            // Connected multi-step activity this type belongs to (interactive_reading / interactive_listening)
            $table->string('task_group', 50)->nullable();
            $table->unsignedSmallInteger('preparation_time_sec_default')->default(0);
            $table->unsignedSmallInteger('answer_time_sec_default')->nullable();
            $table->unsignedTinyInteger('max_replays_default')->nullable();
            $table->unsignedTinyInteger('turns_default')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedTinyInteger('display_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Question groups: the shared passage / conversation of an Interactive Reading
        //    or Interactive Listening activity. Subtask questions point to their group.
        Schema::create('duolingo_question_groups', function (Blueprint $table) {
            $table->id();
            $table->string('task_group', 50);               // interactive_reading / interactive_listening
            $table->string('title', 255)->nullable();
            $table->longText('passage_text')->nullable();   // Interactive Reading passage
            $table->string('audio_url', 500)->nullable();   // Interactive Listening scenario audio
            $table->text('audio_transcript')->nullable();
            $table->string('scenario', 500)->nullable();    // e.g. "You are talking to a professor about…"
            $table->unsignedSmallInteger('time_limit_sec')->nullable(); // overall task timer
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Question bank
        Schema::create('duolingo_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_question_type_id')->constrained('duolingo_question_types')->restrictOnDelete();
            $table->foreignId('duolingo_question_group_id')->nullable()
                ->constrained('duolingo_question_groups')->nullOnDelete();
            $table->unsignedTinyInteger('group_order')->nullable();
            $table->string('question_code', 20)->unique();

            $table->longText('question_text')->nullable();  // prompt / passage / sentence / word
            $table->text('audio_transcript')->nullable();
            $table->string('audio_url', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('image_alt_text', 255)->nullable();

            // Question configuration (duolingo.md §8.2)
            $table->unsignedSmallInteger('preparation_time_sec')->nullable();
            $table->unsignedSmallInteger('answer_time_sec')->nullable();
            $table->unsignedTinyInteger('max_replays')->nullable();
            $table->unsignedSmallInteger('min_word_count')->nullable();
            $table->unsignedSmallInteger('max_word_count')->nullable();
            $table->json('config')->nullable();             // any extra type-specific settings

            // Objective answer key (Read/Listen and Select: "yes"/"no"; Dictation: transcript)
            $table->text('correct_ans')->nullable();
            $table->json('accepted_variants')->nullable();
            $table->text('correct_ans_explanation')->nullable();
            $table->decimal('marks', 5, 2)->default(1);

            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('topic_tags')->nullable();
            $table->string('source_reference', 255)->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('difficulty', 'idx_det_q_difficulty');
            $table->index('is_active', 'idx_det_q_active');
        });

        // 6. Options satellite (mcq_single subtasks)
        Schema::create('duolingo_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('display_order');
        });

        // 7. Blanks satellite (Read and Complete C-Test, Vocabulary in Context)
        Schema::create('duolingo_question_blanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();
            $table->unsignedTinyInteger('blank_order');
            $table->string('visible_prefix', 100)->nullable();  // C-Test: "pract" shown, "ice" missing
            $table->string('correct_answer', 255);
            $table->json('accepted_variants')->nullable();
            $table->unique(['duolingo_question_id', 'blank_order'], 'uq_det_blank_order');
        });

        // 8. Highlight satellite (Interactive Reading — Highlight the Answer)
        Schema::create('duolingo_question_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();
            $table->unsignedInteger('start_offset');
            $table->unsignedInteger('end_offset');
            $table->text('answer_text');
        });

        // 9. Turns satellite (Interactive Speaking turns, Interactive Writing follow-up prompt)
        Schema::create('duolingo_question_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();
            $table->unsignedTinyInteger('turn_order');
            $table->string('speaker', 100)->nullable();
            $table->text('prompt_text')->nullable();
            $table->string('audio_url', 500)->nullable();
            $table->unsignedSmallInteger('response_time_sec')->nullable();
            $table->unique(['duolingo_question_id', 'turn_order'], 'uq_det_turn_order');
        });

        // 10. Mock-test assembly: duolingo_module -> question (with display order)
        Schema::create('duolingo_module_wise_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_module_id')->constrained('duolingo_module')->cascadeOnDelete();
            $table->foreignId('duolingo_question_id')->constrained('duolingo_questions')->cascadeOnDelete();
            $table->unsignedSmallInteger('display_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['duolingo_module_id', 'display_order'], 'uq_det_module_question_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duolingo_module_wise_question');
        Schema::dropIfExists('duolingo_question_turns');
        Schema::dropIfExists('duolingo_question_highlights');
        Schema::dropIfExists('duolingo_question_blanks');
        Schema::dropIfExists('duolingo_question_options');
        Schema::dropIfExists('duolingo_questions');
        Schema::dropIfExists('duolingo_question_groups');
        Schema::dropIfExists('duolingo_question_types');
        Schema::dropIfExists('duolingo_module');
        Schema::dropIfExists('duolingo_sections');
    }
};
