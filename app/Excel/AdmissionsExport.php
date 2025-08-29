<?php

namespace App\Excel;
use App\Models\Admission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdmissionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Admission::query()
            ->with(['student', 'batch.course'])
            ->latest('id');
    }

    public function headings(): array
    {
        return [
            'Admission ID',
            'Student Name',
            'Student Phone',
            'Student Email',
            'Roll No',
            'Student UID',
            'Status',
            'Batch',
            'Course',
            'Course Fee',
            'Admission Date',
            'Mode',
            'Discount',
            'Fee Total',
            'Fee Due',
            'Admission Status',
            'Created At',
        ];
    }

    public function map($a): array
    {
        $student = $a->student;
        $batch   = $a->batch;
        $course  = $batch?->course;

        return [
            $a->id,
            $student?->name,
            $student?->phone,
            $student?->email,
            $student?->roll_no,
            $student?->student_uid,
            $student?->status,
            $batch?->batch_name,
            $course?->name,
            $course?->gross_fee,
            $a->admission_date?->format('Y-m-d'),
            $a->mode,
            $a->discount,
            $a->fee_total,
            $a->fee_due,
            $a->status,
            $a->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
