<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Duolingo autosave, resumable exam state, objective marking and evaluator review.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('duolingo_attempts', function (Blueprint $table) {
            // Last exam state: current question, step, remaining time, group timers, audio plays used.
            $table->json('state')->nullable()->after('ended_at');
            $table->timestamp('last_activity_at')->nullable()->after('state');
            // null while in progress; pending_evaluation after submit; published once scored.
            $table->string('result_status', 30)->nullable()->after('status')->index();
            $table->timestamp('published_at')->nullable()->after('overall_score');
        });

        Schema::table('duolingo_answers', function (Blueprint $table) {
            // Objective items: true / false. Partially correct items are false with a partial raw_score.
            $table->boolean('is_correct')->nullable()->after('max_score');
            // Per-part marking, e.g. which blanks were right, or dictation word accuracy.
            $table->json('score_detail')->nullable()->after('is_correct');
            $table->foreignId('evaluated_by')->nullable()->after('feedback')
                ->constrained('users')->nullOnDelete();
        });

        Schema::create('duolingo_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duolingo_attempt_id')->unique()->constrained('duolingo_attempts')->cascadeOnDelete();
            // Null until an evaluator is assigned or claims it.
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->longText('feedback_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duolingo_evaluations');

        Schema::table('duolingo_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('evaluated_by');
            $table->dropColumn(['is_correct', 'score_detail']);
        });

        Schema::table('duolingo_attempts', function (Blueprint $table) {
            $table->dropColumn(['state', 'last_activity_at', 'result_status', 'published_at']);
        });
    }
};
