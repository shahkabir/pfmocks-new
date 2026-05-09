<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sop_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_exam_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();

            // 'review'  → student already has an SOP and wants it reviewed
            // 'new'     → student wants the evaluator to write one from scratch
            $table->enum('service_type', ['review', 'new']);

            $table->string('intended_university');
            $table->string('country', 100);

            $table->string('resume_path');             // student-uploaded résumé (PDF)
            $table->string('original_sop_path')->nullable(); // only for 'review'
            $table->string('final_sop_path')->nullable();    // uploaded by evaluator

            $table->timestamps();

            $table->index(['user_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_submissions');
    }
};
