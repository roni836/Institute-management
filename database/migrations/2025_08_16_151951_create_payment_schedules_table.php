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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->unsignedInteger('installment_no');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->string('payment_mode')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->string('receipt_no')->nullable();
            $table->timestamps();
            $table->unique(['admission_id', 'installment_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
