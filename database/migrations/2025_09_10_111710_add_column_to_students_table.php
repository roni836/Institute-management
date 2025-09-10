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
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender')->nullable();
            $table->string('category')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->string('board')->nullable();
            $table->string('class')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
