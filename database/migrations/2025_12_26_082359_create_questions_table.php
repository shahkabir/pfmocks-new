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
            Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('module_id')
                ->constrained()
                ->cascadeOnDelete();

            // Question classification
            $table->enum('type', [
                'mcq_single',     // single correct
                'mcq_multiple',   // multiple correct
                'text',           // short answer
                'essay',          // long answer (manual evaluation)
                'audio',          // listening-based
                'speaking'        // audio recording
            ]);

            $table->text('passage_instruction')->nullable()->after('passage');

            // Main question text
            $table->text('question_text');

            // Optional shared content (reading passage / prompt)
            $table->longText('passage')->nullable();

            // Media support
            $table->string('audio_url')->nullable();
            $table->string('image_url')->nullable();

            // Scoring
            $table->integer('marks')->default(1);

            // Ordering inside module
            $table->integer('sort_order')->default(0);

            // Flexible metadata
            // e.g. time_limit, word_limit, band_mapping, difficulty
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
