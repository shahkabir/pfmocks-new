<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Section time per test. Tests have different numbers of questions per section,
 * so the time can no longer live on the shared duolingo_sections row.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('duolingo_module', function (Blueprint $table) {
            $table->unsignedSmallInteger('time_allowed_minutes')->nullable()->after('duolingo_section_id');
        });
    }

    public function down(): void
    {
        Schema::table('duolingo_module', function (Blueprint $table) {
            $table->dropColumn('time_allowed_minutes');
        });
    }
};
