<?php

namespace App\Excel;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $date;

    public function __construct($date = null)
    {
        $this->date = $date;
    }

    public function collection()
    {
        $query = StudentAttendance::with(['student', 'admission.batch.course']);
        
        if ($this->date) {
            $query->whereDate('date', $this->date);
        }
        
        return $query->orderBy('date', 'desc')->orderBy('student_id')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Roll Number',
            'Phone',
            'Course',
            'Batch',
            'Status',
            'Remarks',
            'Recorded At'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date->format('Y-m-d'),
            $attendance->student->name ?? 'N/A',
            $attendance->student->roll_no ?? 'N/A',
            $attendance->student->phone ?? 'N/A',
            $attendance->admission->batch->course->name ?? 'N/A',
            $attendance->admission->batch->name ?? 'N/A',
            ucfirst($attendance->status),
            $attendance->remarks ?? 'No remarks',
            $attendance->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // Date
            'B' => 25, // Student Name
            'C' => 15, // Roll Number
            'D' => 15, // Phone
            'E' => 20, // Course
            'F' => 15, // Batch
            'G' => 12, // Status
            'H' => 30, // Remarks
            'I' => 20, // Recorded At
        ];
    }
}
