<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_redemptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referral_invitation_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique(); // one-time use

            // This project doesn't have an `orders` table — the closest equivalent
            // is `payments` (which represents a paid order), so we link to that.
            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete()
                ->unique();

            $table->foreignId('referee_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('discount_amount', 10, 2);
            $table->decimal('reward_amount', 10, 2)->nullable();

            $table->timestamp('redeemed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_redemptions');
    }
};
