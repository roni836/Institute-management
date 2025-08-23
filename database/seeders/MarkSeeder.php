<?php

namespace Database\Seeders;

use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examSubjects = ExamSubject::all();
        $students = Student::all();

        foreach ($examSubjects as $examSubject) {
            foreach ($students as $student) {
                Mark::create([
                    'student_id' => $student->id,
                    'exam_subject_id' => $examSubject->id,
                    'marks_obtained' => rand(35, 100), // random marks
                ]);
            }
        }
    }
}
