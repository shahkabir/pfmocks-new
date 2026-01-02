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
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();

            // 1:1 mapping with questions table
            $table->foreignId('question_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // UI grouping of options (MCQ, matching, etc.)
            // Example: [12,13,14,15]
            $table->json('question_options_group_ids')->nullable();

            // IELTS: 1,2,3,4 | PTE: null
            $table->tinyInteger('part_number')->nullable();

            $table->timestamps();

            // Ensure strict 1:1 mapping
            $table->unique('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_groups');
    }
};
