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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->date('admission_date');
            $table->enum('mode', ['full', 'installment'])->default('full');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fee_total', 10, 2); // final payable after discount
            $table->decimal('fee_due', 10, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->unique(['student_id', 'batch_id']); // same student can’t be admitted twice to same batch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_batches');
    }
};
