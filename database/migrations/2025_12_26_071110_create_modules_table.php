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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Reading, Writing
            $table->integer('duration_minutes');
            $table->timestamps();
            $table->string('module_type')->after('name');
            $table->enum('type', ['free', 'paid'])->default('paid')->after('module_type');
            $table->decimal('price_in_bdt', 10, 2)->default(0)->after('type');
            $table->decimal('price_in_usd', 10, 2)->default(0)->after('price_in_bdt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
