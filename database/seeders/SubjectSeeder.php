<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::first(); // take first course for demo
        if (!$course) return;

        $subjects = ['Mathematics', 'Physics', 'Chemistry', 'Biology', 'English'];

        foreach ($subjects as $subject) {
            Subject::create([
                'course_id' => $course->id,
                'name' => $subject,
                'code' => strtoupper(substr($subject, 0, 3)) . rand(100, 999),
            ]);
        }
    }
}
