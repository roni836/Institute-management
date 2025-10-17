<?php

namespace App\Excel;

use App\Models\Admission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AdmissionsDetailsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;
    protected $status;
    protected $batchId;
    protected $fromDate;
    protected $toDate;

    public function __construct($search = null, $status = null, $batchId = null, $fromDate = null, $toDate = null)
    {
        $this->search = $search;
        $this->status = $status;
        $this->batchId = $batchId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function query()
    {
        return Admission::query()
            ->with(['student', 'batch.course'])
            ->leftJoin('courses', 'admissions.course_id', '=', 'courses.id')
            ->when($this->search, fn($q) => $q->where(function($qq) {
                $term = "%{$this->search}%";
                $qq->whereHas('student', fn($s) => 
                    $s->where('name', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('enrollment_id', 'like', $term)
                );
            }))
            ->when($this->status, fn($q) => $q->where('admissions.status', $this->status))
            ->when($this->batchId, fn($q) => $q->where('admissions.batch_id', $this->batchId))
            ->when($this->fromDate, fn($q) => $q->whereDate('admissions.admission_date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('admissions.admission_date', '<=', $this->toDate))
            ->select('admissions.*', 'courses.name as course_name')
            ->orderBy('admissions.admission_date', 'desc')
            ->orderBy('admissions.id', 'desc');
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Student Name',
            'Enrollment ID',
            'Email',
            'Mobile',
            'Alt Mobile',
            'WhatsApp No',
            'Father Name',
            'Mother Name',
            'Date of Birth',
            'Gender',
            'Address',
            'City',
            'State',
            'Pincode',
            'Course',
            'Batch',
            'Admission Date',
            'Payment Mode',
            'Fee Total (₹)',
            'Fee Due (₹)',
            'Fee Paid (₹)',
            'Discount Type',
            'Discount Value (₹)',
            'GST Applied',
            'GST Rate (%)',
            'GST Amount (₹)',
            'Session',
            'Stream',
            'Status',
            'Review Status',
            'Review Notes',
            'Reviewed At',
            'Created At'
        ];
    }

    public function map($admission): array
    {
        static $counter = 0;
        $counter++;

        $student = $admission->student ?? null;
        $batch = $admission->batch ?? null;
        $feePaid = $admission->fee_total - $admission->fee_due;

        return [
            $counter,
            $student->name ?? 'N/A',
            $student->enrollment_id ?? 'N/A',
            $student->email ?? 'N/A',
            $student->phone ?? 'N/A',
            $student->alt_phone ?? 'N/A',
            $student->whatsapp_no ?? 'N/A',
            $student->father_name ?? 'N/A',
            $student->mother_name ?? 'N/A',
            $student->dob ? (is_string($student->dob) ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : $student->dob->format('d M Y')) : 'N/A',
            ucfirst($student->gender ?? 'N/A'),
            $student->address ?? 'N/A',
            $student->city ?? 'N/A',
            $student->state ?? 'N/A',
            $student->pincode ?? 'N/A',
            $admission->course_name ?? 'N/A',
            $batch->batch_name ?? 'N/A',
            $admission->admission_date ? (is_string($admission->admission_date) ? \Carbon\Carbon::parse($admission->admission_date)->format('d M Y') : $admission->admission_date->format('d M Y')) : 'N/A',
            ucfirst($admission->mode ?? 'N/A'),
            number_format($admission->fee_total, 2),
            number_format($admission->fee_due, 2),
            number_format($feePaid, 2),
            ucfirst($admission->discount_type ?? 'N/A'),
            number_format($admission->discount_value ?? 0, 2),
            $admission->is_gst ? 'Yes' : 'No',
            number_format($admission->gst_rate ?? 0, 2),
            number_format($admission->gst_amount ?? 0, 2),
            $admission->session ?? 'N/A',
            $admission->stream ?? 'N/A',
            ucfirst($admission->status ?? 'N/A'),
            ucfirst($admission->review_status ?? 'N/A'),
            $admission->review_notes ?? 'N/A',
            $admission->reviewed_at ? (is_string($admission->reviewed_at) ? \Carbon\Carbon::parse($admission->reviewed_at)->format('d M Y H:i') : $admission->reviewed_at->format('d M Y H:i')) : 'N/A',
            $admission->created_at ? (is_string($admission->created_at) ? \Carbon\Carbon::parse($admission->created_at)->format('d M Y H:i') : $admission->created_at->format('d M Y H:i')) : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make first row bold
            1 => ['font' => ['bold' => true]],
            
            // Right align amount columns
            'T:W' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ]
            ],
            
            // Center align S.No, Status columns
            'A:A' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            'AD:AE' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }
}
