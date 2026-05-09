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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('actual_question');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->text('correct_answer_explanation')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->string('question_type', 50)
                ->after('actual_question');

            $table->text('correct_answer_fib')
                ->nullable()
                ->after('correct_answer_explanation');

            $table->boolean('is_active')
                ->default(true)
                ->after('sort_order');

            $table->string('question_image_path')
                ->nullable()
                ->after('is_active');

            $table->string('question_audio_path')
                ->nullable()
                ->after('question_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
