<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Exam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batch = Batch::first(); // take first batch for demo
        if (!$batch) return;

        $exams = [
            ['name' => 'Mid Term Exam', 'exam_date' => now()->subDays(10)],
            ['name' => 'Final Exam', 'exam_date' => now()->addDays(30)],
        ];

        foreach ($exams as $exam) {
            Exam::create([
                'batch_id' => $batch->id,
                'name' => $exam['name'],
                'exam_date' => $exam['exam_date'],
            ]);
        }
    }
}
