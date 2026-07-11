<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pte_attempts', function (Blueprint $table) {
            $table->string('intro_audio_url', 500)->nullable()->after('total_marks');
        });
    }

    public function down(): void
    {
        Schema::table('pte_attempts', function (Blueprint $table) {
            $table->dropColumn('intro_audio_url');
        });
    }
};
