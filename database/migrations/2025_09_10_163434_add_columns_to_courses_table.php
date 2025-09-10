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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('tution_fee', 10, 2)->nullable();
            $table->decimal('admission_fee', 10, 2)->nullable();
            $table->decimal('exam_fee', 10, 2)->nullable();
            $table->decimal('infra_fee', 10, 2)->nullable();
            $table->decimal('SM_fee', 10, 2)->nullable();
            $table->decimal('tech_fee', 10, 2)->nullable();
            $table->decimal('other_fee', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'tution_fee',
                'admission_fee', 
                'exam_fee',
                'infra_fee',
                'SM_fee',
                'tech_fee',
                'other_fee'
            ]);
        });
    }
};
