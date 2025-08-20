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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('payment_schedule_id')->nullable()->constrained('payment_schedules')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('mode');                     // e.g., cash, cheque, online
            $table->string('reference_no')->nullable(); // CHQ/UTR
            $table->enum('status', ['success', 'failed', 'pending'])->default('success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
