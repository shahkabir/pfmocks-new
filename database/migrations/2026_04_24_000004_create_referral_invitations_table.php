<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referral_program_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('referrer_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('referred_user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique(); // one referral per new user

            $table->string('referral_code_used', 20);

            $table->enum('status', [
                'pending',
                'qualified',
                'redeemed',
                'expired',
                'rejected',
            ])->default('pending');

            $table->string('source_channel', 60)->nullable(); // whatsapp, fb, email, copy

            $table->timestamps();

            $table->index(['referrer_user_id', 'status']);
            $table->index('referral_code_used');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_invitations');
    }
};
