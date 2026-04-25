<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_programs', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Discount for the referred (new) user
            $table->enum('referee_discount_type', ['fixed', 'percent']);
            $table->decimal('referee_discount_value', 8, 2);

            // Optional reward for the referrer
            $table->enum('referrer_reward_type', ['fixed', 'percent'])->nullable();
            $table->decimal('referrer_reward_value', 8, 2)->nullable();

            $table->decimal('min_first_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_programs');
    }
};
