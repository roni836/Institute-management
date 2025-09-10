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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('dob')->nullable(); // Date of Birth
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('roll_no')->unique();
            $table->string('student_uid')->unique(); // e.g., 2501458
            $table->date('admission_date');
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->integer('total_courses_enrolled')->default(0);
            $table->integer('courses_completed')->default(0);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
