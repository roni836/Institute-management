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
        if (!Schema::hasColumn('students', 'academic_session')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('academic_session')->nullable()->after('stream');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'academic_session')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('academic_session');
            });
        }
    }
};
