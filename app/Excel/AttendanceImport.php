<?php

namespace App\Excel;

use App\Models\Student;
use App\Models\Admission;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class AttendanceImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use Importable, SkipsErrors;

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // Find student by roll number or phone
            $student = Student::where('roll_no', $row['roll_no'])
                ->orWhere('phone', $row['student_phone'])
                ->first();

            if (!$student) {
                throw new \Exception("Student not found with roll number: {$row['roll_no']} or phone: {$row['student_phone']}");
            }

            // Find admission for the student
            $admission = Admission::where('student_id', $student->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if (!$admission) {
                throw new \Exception("No active admission found for student: {$student->name}");
            }

            // Check if attendance already exists for this date
            $existingAttendance = StudentAttendance::where('student_id', $student->id)
                ->where('admission_id', $admission->id)
                ->whereDate('date', $row['date'])
                ->first();

            if ($existingAttendance) {
                // Update existing attendance
                $existingAttendance->update([
                    'status' => strtolower($row['status']),
                    'remarks' => $row['remarks'] ?? null,
                ]);
                return null; // Skip creating new record
            }

            // Create new attendance record
            return new StudentAttendance([
                'student_id' => $student->id,
                'admission_id' => $admission->id,
                'date' => $row['date'],
                'status' => strtolower($row['status']),
                'remarks' => $row['remarks'] ?? null,
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'roll_no' => 'required|string',
            'student_phone' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused,Present,Absent,Late,Excused',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'roll_no.required' => 'Roll number is required.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date format (YYYY-MM-DD).',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: present, absent, late, excused.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
