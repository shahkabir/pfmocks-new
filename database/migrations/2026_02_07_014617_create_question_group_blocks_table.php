<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_group_blocks', function (Blueprint $table) {
            $table->id();

            // Parent group (per part / question)
            $table->foreignId('question_group_id')
                  ->constrained('question_groups')
                  ->cascadeOnDelete();

            // Instruction shown once before this block
            $table->text('instruction_text')
                  ->comment('Instruction shown before this block of questions');

            // Subset of question_option IDs belonging to this instruction
            // Example: [1,2,3]
            $table->json('question_option_ids');

            // Order of blocks within the same question_group
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('question_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_group_blocks');
    }
};
?>