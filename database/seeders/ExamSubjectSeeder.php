<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exam = Exam::first();
        if (!$exam) return;

        $subjects = Subject::all()->take(3); // assign first 3 subjects to exam

        foreach ($subjects as $subject) {
            ExamSubject::create([
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'max_marks' => 100,
            ]);
        }
    }
}
