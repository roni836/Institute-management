<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Admission;
use App\Models\StudentAttendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::with('admissions')->get();
        
        if ($students->isEmpty()) {
            return;
        }

        // Generate attendance for the last 30 days
        $dates = collect();
        for ($i = 0; $i < 30; $i++) {
            $dates->push(Carbon::now()->subDays($i));
        }

        $statuses = ['present', 'absent', 'late', 'excused'];
        $remarks = [
            'present' => ['On time', 'Good attendance', ''],
            'absent' => ['Sick leave', 'Personal reason', 'No show'],
            'late' => ['Traffic delay', 'Late arrival', ''],
            'excused' => ['Medical appointment', 'Family emergency', 'Authorized absence']
        ];

        foreach ($dates as $date) {
            foreach ($students as $student) {
                // Get the student's latest admission
                $admission = $student->admissions()->latest()->first();
                
                if (!$admission) {
                    continue;
                }

                // Randomly decide if student has attendance record for this date (80% chance)
                if (rand(1, 100) <= 80) {
                    $status = $statuses[array_rand($statuses)];
                    $statusRemarks = $remarks[$status];
                    $remark = $statusRemarks[array_rand($statusRemarks)];

                    // Check if attendance record already exists for this student and date
                    $exists = StudentAttendance::where('student_id', $student->id)
                        ->where('admission_id', $admission->id)
                        ->whereDate('date', $date)
                        ->exists();

                    if (!$exists) {
                        StudentAttendance::create([
                            'student_id' => $student->id,
                            'admission_id' => $admission->id,
                            'date' => $date,
                            'status' => $status,
                            'remarks' => $remark,
                        ]);
                    }
                }
            }
        }
    }
}
