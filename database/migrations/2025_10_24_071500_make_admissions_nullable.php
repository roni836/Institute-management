<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make several admissions columns nullable so drafts can be created without full step-2 data
        Schema::table('admissions', function (Blueprint $table) {
            // Drop foreign keys first (if present) so we can change column nullability
            try {
                $table->dropForeign(['batch_id']);
            } catch (\Throwable $e) {
                // ignore if foreign doesn't exist
            }

            try {
                $table->dropForeign(['course_id']);
            } catch (\Throwable $e) {
                // ignore if foreign doesn't exist
            }

            // Make columns nullable
            $table->date('admission_date')->nullable()->change();
            $table->unsignedBigInteger('batch_id')->nullable()->change();
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->decimal('fee_total', 10, 2)->nullable()->change();
            $table->enum('mode', ['full','installment'])->nullable()->change();
            $table->string('discount_type')->nullable()->change();
            $table->decimal('discount_value', 10, 2)->nullable()->change();
        });

        // Re-create foreign keys (nullable FK allowed)
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attempt to revert nullable changes (may require data cleanup before running)
        Schema::table('admissions', function (Blueprint $table) {
            // Drop foreign keys to change column definitions back
            try {
                $table->dropForeign(['batch_id']);
            } catch (\Throwable $e) {
            }

            try {
                $table->dropForeign(['course_id']);
            } catch (\Throwable $e) {
            }

            $table->date('admission_date')->nullable(false)->change();
            $table->unsignedBigInteger('batch_id')->nullable(false)->change();
            $table->unsignedBigInteger('course_id')->nullable(false)->change();
            $table->decimal('fee_total', 10, 2)->nullable(false)->change();
            $table->enum('mode', ['full','installment'])->nullable(false)->change();
            $table->string('discount_type')->nullable(false)->change();
            $table->decimal('discount_value', 10, 2)->nullable(false)->change();

            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }
};
