<?php

namespace App\Excel;
use App\Models\Admission;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;

class AdmissionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function rules(): array
    {
        return [
            '*.student_phone' => ['required', 'string', 'max:20'],
            '*.student_name'  => ['required', 'string', 'max:255'],
            '*.batch_id'      => ['required', 'integer', 'exists:batches,id'],
            '*.admission_date'=> ['required', 'date'],
            '*.mode'          => ['required', Rule::in(['full', 'installment'])],
            '*.discount'      => ['nullable', 'numeric', 'min:0'],
            '*.fee_total'     => ['required', 'numeric', 'min:0'],
            '*.fee_due'       => ['nullable', 'numeric', 'min:0'],
            '*.status'        => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.student_phone.required' => 'Phone is required for student identification.',
        ];
    }

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // 1) Upsert Student by phone
            $student = Student::firstOrCreate(
                ['phone' => $row['student_phone']],
                [
                    'name'         => $row['student_name'],
                    'email'        => $row['student_email'] ?? null,
                    'roll_no'      => $row['roll_no'] ?? null,
                    'student_uid'  => $row['student_uid'] ?? null,
                    'father_name'  => $row['father_name'] ?? null,
                    'mother_name'  => $row['mother_name'] ?? null,
                    'address'      => $row['address'] ?? null,
                    'status'       => $row['student_status'] ?? 'active',
                ]
            );

            // 2) Create Admission (skip duplicates by (student_id, batch_id, admission_date))
            $exists = Admission::where('student_id', $student->id)
                ->where('batch_id', $row['batch_id'])
                ->whereDate('admission_date', $row['admission_date'])
                ->exists();

            if ($exists) {
                return null; // skip duplicate row
            }

            return new Admission([
                'student_id'    => $student->id,
                'batch_id'      => (int) $row['batch_id'],
                'admission_date'=> $row['admission_date'],
                'mode'          => $row['mode'],
                'discount'      => (float) ($row['discount'] ?? 0),
                'fee_total'     => (float) $row['fee_total'],
                'fee_due'       => (float) ($row['fee_due'] ?? $row['fee_total']),
                'status'        => $row['status'] ?? 'active',
            ]);
        });
    }
}
