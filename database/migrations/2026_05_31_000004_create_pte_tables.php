<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 3. Sections: the 3 PTE sections (Speaking & Writing, Reading, Listening)
        Schema::create('pte_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('tag', 5)->unique();
            $table->unsignedTinyInteger('display_order');
            $table->unsignedSmallInteger('time_allowed_minutes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Bridge: module -> pte_section
        Schema::create('pte_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('pte_section_id')->constrained('pte_sections')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['module_id', 'pte_section_id'], 'uq_module_section');
            $table->index('pte_section_id', 'idx_ptemodule_section');
        });

        // 5. The 20 PTE question sub-types (Read Aloud, Repeat Sentence...)
        Schema::create('pte_question_sub_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pte_section_id')->constrained('pte_sections')->restrictOnDelete();
            $table->string('name', 100);
            $table->char('tag', 5)->unique();
            $table->enum('response_type', [
                'audio_record', 'text_write', 'single_choice', 'multi_choice',
                'fill_blank', 'reorder', 'highlight_words', 'select_option',
            ]);
            $table->enum('stimulus_type', ['text', 'audio', 'image', 'audio_image', 'none']);
            $table->unsignedSmallInteger('preparation_time_sec_default')->default(0);
            $table->unsignedSmallInteger('answer_time_sec_default')->nullable();
            $table->unsignedTinyInteger('marks_default')->nullable();
            $table->unsignedTinyInteger('typical_count_in_exam')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedTinyInteger('display_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('pte_section_id', 'idx_subtype_section');
        });

        // 6. Question Bank
        Schema::create('pte_question_granular', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('pte_sub_type_id')->constrained('pte_question_sub_types')->restrictOnDelete();
            $table->string('question_granular_id', 20)->unique();

            $table->text('question_text')->nullable();
            $table->text('audio_transcript')->nullable();
            $table->string('audio_url', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('image_alt_text', 255)->nullable();

            $table->unsignedSmallInteger('preparation_time_sec')->nullable();
            $table->unsignedSmallInteger('answer_time_sec')->nullable();

            $table->unsignedTinyInteger('marks')->default(1);
            $table->text('correct_ans')->nullable();
            $table->text('correct_ans_explanation')->nullable();

            $table->unsignedSmallInteger('min_word_count')->nullable();
            $table->unsignedSmallInteger('max_word_count')->nullable();

            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('topic_tags')->nullable();
            $table->string('source_reference', 255)->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('pte_sub_type_id', 'idx_granular_subtype');
            $table->index('difficulty', 'idx_granular_difficulty');
            $table->index('is_active', 'idx_granular_active');
        });

        // 7. Mock-test assembly: pte_module -> question (with display order)
        Schema::create('pte_module_wise_question', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('pte_module_id')->constrained('pte_module')->cascadeOnDelete();
            $table->foreignId('pte_question_granular_id')
                ->constrained('pte_question_granular')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('display_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['pte_module_id', 'display_order'], 'uq_module_question_order');
            $table->index('pte_question_granular_id', 'idx_mwq_question');
        });

        // 8. Options satellite (MCQ Single/Multiple, Select Missing Word, Highlight Correct Summary, ASQ)
        Schema::create('pte_question_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('question_granular_id')
                ->constrained('pte_question_granular')
                ->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('display_order');
            $table->index('question_granular_id', 'idx_options_question');
        });

        // 9. Blanks satellite (R/W FIB, R-FIB, L-FIB, Write from Dictation)
        Schema::create('pte_question_blanks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('question_granular_id')
                ->constrained('pte_question_granular')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('blank_order');
            $table->string('correct_answer', 255);
            $table->json('accepted_variants')->nullable();
            $table->json('dropdown_options')->nullable();
            $table->unique(['question_granular_id', 'blank_order'], 'uq_blank_order');
        });

        // 10. Segments satellite (Re-order Paragraphs)
        Schema::create('pte_question_segments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('question_granular_id')
                ->constrained('pte_question_granular')
                ->cascadeOnDelete();
            $table->text('segment_text');
            $table->unsignedTinyInteger('correct_order');
            $table->unique(['question_granular_id', 'correct_order'], 'uq_segment_order');
        });

        // 11. Highlight Incorrect Words satellite
        Schema::create('pte_question_highlight_words', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('question_granular_id')
                ->constrained('pte_question_granular')
                ->cascadeOnDelete();
            $table->string('word_text', 100);
            $table->unsignedSmallInteger('word_order');
            $table->boolean('is_incorrect')->default(false);
            $table->unique(['question_granular_id', 'word_order'], 'uq_word_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pte_question_highlight_words');
        Schema::dropIfExists('pte_question_segments');
        Schema::dropIfExists('pte_question_blanks');
        Schema::dropIfExists('pte_question_options');
        Schema::dropIfExists('pte_module_wise_question');
        Schema::dropIfExists('pte_question_granular');
        Schema::dropIfExists('pte_question_sub_types');
        Schema::dropIfExists('pte_module');
        Schema::dropIfExists('pte_sections');
    }
};
