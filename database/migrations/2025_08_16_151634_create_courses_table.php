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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // e.g., PURNEA 1 MGZ
            $table->string('batch_code')->nullable(); // e.g., MGZPU-7201
            $table->integer('duration_months')->nullable();
            $table->decimal('gross_fee', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_fee', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
