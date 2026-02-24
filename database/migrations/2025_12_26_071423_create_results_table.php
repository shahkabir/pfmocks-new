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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete()->unique(); //One result per attempt
            //$table->foreignId('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();   // If admin deleted → keep result


            $table->string('status')->comment('pending, completed, evaluated, manual_review');

            $table->string('exam_name');
            $table->string('module_name');

            $table->integer('achieved_score');
            $table->integer('total_score');

            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->decimal('band_score', 3, 1)->nullable(); // IELTS band

            $table->unsignedInteger('time_taken_seconds');

            $table->text('evaluator_feedback')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->json('breakdown')->nullable();
            // Example:
            // {
            //   "part1": 8,
            //   "part2": 7,
            //   "part3": 9
            // }
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
