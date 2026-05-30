<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->text('exam_information')->nullable()->after('tag');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->text('module_information')->nullable()->after('module_type');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('exam_information');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('module_information');
        });
    }
};
