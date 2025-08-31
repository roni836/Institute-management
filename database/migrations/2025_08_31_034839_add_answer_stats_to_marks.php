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
        Schema::table('marks', function (Blueprint $table) {
            $table->unsignedInteger('correct')->default(0)->after('exam_subject_id');
            $table->unsignedInteger('wrong')->default(0)->after('correct');
            $table->unsignedInteger('blank')->default(0)->after('wrong');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->dropColumn(['correct', 'wrong', 'blank']);
        });
    }
};
