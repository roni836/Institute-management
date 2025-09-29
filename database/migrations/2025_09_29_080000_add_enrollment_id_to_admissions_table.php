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
        if (! Schema::hasColumn('admissions', 'enrollment_id')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->string('enrollment_id')->nullable()->after('student_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('admissions', 'enrollment_id')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->dropColumn('enrollment_id');
            });
        }
    }
};
