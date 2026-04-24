<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('user_exam_id')->nullable()->constrained('user_exams')->nullOnDelete();

            $table->string('payment_method', 30)->default('bkash');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id', 80)->index();
            $table->string('sender_msisdn', 20)->nullable();

            // pending_verification → approved → [exam unlocked]
            //                    → rejected  → [user can retry]
            $table->enum('status', ['pending_verification', 'approved', 'rejected'])
                  ->default('pending_verification')
                  ->index();

            $table->text('admin_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->unique(['transaction_id', 'payment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
