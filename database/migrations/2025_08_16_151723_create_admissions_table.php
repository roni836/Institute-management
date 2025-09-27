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
            $table->boolean('is_gst')->default(false); // Whether GST was applied
            $table->decimal('gst_amount', 10, 2)->default(0); // Amount of GST applied
            $table->decimal('gst_rate', 5, 2)->default(0); // GST rate used
            $table->string('session')->nullable(); // Academic session
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
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
