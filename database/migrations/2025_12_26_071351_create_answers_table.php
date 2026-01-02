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
       
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            // Core ownership
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('exam_attempt_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('question_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // For MCQ / TFNG / Matching (nullable for text/essay)
            $table->foreignId('question_option_id')
                  ->nullable()
                  ->constrained('question_options')
                  ->nullOnDelete();

            // Text / essay / listening answers
            $table->text('answer')->nullable();

            // Evaluation snapshot
            $table->boolean('is_correct')->nullable();

            $table->timestamps();

            // Prevent duplicate answers per attempt per question
            $table->unique([
                'exam_attempt_id',
                'question_id',
                'question_option_id'
            ], 'unique_answer_per_attempt');
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
