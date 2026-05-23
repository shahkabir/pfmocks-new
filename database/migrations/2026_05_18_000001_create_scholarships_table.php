<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();

            $table->string('title', 220);
            $table->string('slug', 240)->unique();

            // One of ScholarshipConstants::TYPES
            $table->string('type', 50)->index();

            // ISO alpha-2 country code + display name (kept together for quick rendering)
            $table->string('country_code', 2)->nullable()->index();
            $table->string('country_name', 80)->nullable();

            $table->enum('funding_type', ['fully_funded', 'partially_funded'])->index();
            $table->enum('program_level', ['undergraduate', 'masters', 'doctoral'])->nullable()->index();

            $table->date('deadline')->nullable()->index();

            $table->longText('short_description')->nullable();
            $table->longText('eligibility_criteria')->nullable();
            $table->longText('benefits')->nullable();
            $table->longText('required_documents')->nullable();

            $table->string('official_link_1', 500)->nullable();
            $table->string('official_link_2', 500)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['is_active', 'deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
