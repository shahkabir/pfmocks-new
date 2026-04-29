<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_attempt_id')
                ->constrained('exam_attempts')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('module_type', 30)->index(); // writing, speaking, ...

            $table->enum('status', [
                'assigned',
                'in_progress',
                'completed',
            ])->default('assigned')->index();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // IELTS criteria scores stored as a JSON map, e.g.
            //   {"task_achievement":6,"coherence_cohesion":7,"lexical_resource":6.5,
            //    "grammatical_range":7,"overall":6.5}
            $table->json('band_scores')->nullable();

            $table->longText('feedback_text')->nullable();
            $table->string('feedback_audio_path')->nullable();

            $table->timestamps();

            $table->unique('exam_attempt_id'); // one evaluation per attempt
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
