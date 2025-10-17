<?php

namespace App\Excel;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;
    protected $status;
    protected $mode;
    protected $fromDate;
    protected $toDate;

    public function __construct($search = null, $status = null, $mode = null, $fromDate = null, $toDate = null)
    {
        $this->search = $search;
        $this->status = $status;
        $this->mode = $mode;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function query()
    {
        return Transaction::query()
            ->with(['admission.student', 'admission.batch'])
            ->leftJoin('admissions', 'transactions.admission_id', '=', 'admissions.id')
            ->leftJoin('courses', 'admissions.course_id', '=', 'courses.id')
            ->when($this->search, fn($q) => $q->where(function($qq) {
                $term = "%{$this->search}%";
                $qq->whereHas('admission.student', fn($s) => 
                    $s->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhere('enrollment_id', 'like', $term)
                )
                ->orWhere('transactions.receipt_number', 'like', $term)
                ->orWhere('transactions.reference_no', 'like', $term);
            }))
            ->when($this->status, fn($q) => $q->where('transactions.status', $this->status))
            ->when($this->mode, fn($q) => $q->where('transactions.mode', $this->mode))
            ->when($this->fromDate, fn($q) => $q->whereDate('transactions.date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('transactions.date', '<=', $this->toDate))
            ->select('transactions.*', 'courses.name as course_name')
            ->orderBy('transactions.date', 'desc')
            ->orderBy('transactions.id', 'desc');
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Student Name',
            'Enrollment ID',
            'Course',
            'Batch',
            'Mobile',
            'Transaction Date',
            'Transaction Amount (₹)',
            // 'GST Amount (₹)',
            'Total Amount (₹)',
            'Payment Mode',
            'Reference No',
            'Receipt Number',
            'Status',
            'Created At'
        ];
    }

    public function map($transaction): array
    {
        static $counter = 0;
        $counter++;

        $student = $transaction->admission->student ?? null;
        $batch = $transaction->admission->batch ?? null;

        $totalAmount = $transaction->amount + ($transaction->gst ?? 0);

        return [
            $counter,
            $student->name ?? 'N/A',
            $student->enrollment_id ?? 'N/A',
            $transaction->course_name ?? 'N/A',
            $batch->name ?? 'N/A',
            $student->phone ?? 'N/A',
            $transaction->date ? $transaction->date->format('d M Y') : 'N/A',
            number_format($transaction->amount, 2),
            // number_format($transaction->gst ?? 0, 2),
            number_format($totalAmount, 2),
            ucfirst($transaction->mode ?? 'N/A'),
            $transaction->reference_no ?? 'N/A',
            $transaction->receipt_number ?? 'N/A',
            ucfirst($transaction->status ?? 'N/A'),
            $transaction->created_at ? $transaction->created_at->format('d M Y H:i') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make first row bold
            1 => ['font' => ['bold' => true]],
            
            // Right align amount columns
            'H:J' => [
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
            'N:N' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }
}
