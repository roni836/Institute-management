<?php

namespace App\Excel;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $mode = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
    ) {}

    public function query()
    {
        // Get one representative transaction per student (admission_id) - showing student's payment summary
        $subQuery = Transaction::select(
                'admission_id',
                DB::raw('MIN(id) as representative_id'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(COALESCE(gst, 0)) as total_gst'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('MIN(date) as earliest_date'),
                DB::raw('MAX(date) as latest_date'),
                DB::raw('GROUP_CONCAT(DISTINCT mode) as modes'),
                DB::raw('GROUP_CONCAT(DISTINCT status) as statuses'),
                DB::raw('GROUP_CONCAT(DISTINCT receipt_number) as receipt_numbers')
            )
            ->groupBy('admission_id');

        return Transaction::query()
            ->with(['admission.student', 'admission.batch.course'])
            ->leftJoinSub($subQuery, 'student_summary', function($join) {
                $join->on('transactions.id', '=', 'student_summary.representative_id');
            })
            ->whereIn('transactions.id', $subQuery->pluck('representative_id'))
            ->when($this->search, fn($q) => $q->where(function($qq) {
                $term = "%{$this->search}%";
                $qq->whereHas('admission.student', fn($s) => 
                    $s->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhere('enrollment_id', 'like', $term)
                )
                ->orWhere('transactions.receipt_number', 'like', $term);
            }))
            ->when($this->status, fn($q) => $q->where('transactions.status', $this->status))
            ->when($this->mode, fn($q) => $q->where('transactions.mode', $this->mode))
            ->when($this->fromDate, fn($q) => $q->whereDate('transactions.date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('transactions.date', '<=', $this->toDate))
            ->select('transactions.*', 
                'student_summary.total_amount', 
                'student_summary.total_gst', 
                'student_summary.transaction_count',
                'student_summary.earliest_date',
                'student_summary.latest_date',
                'student_summary.modes',
                'student_summary.statuses',
                'student_summary.receipt_numbers'
            )
            ->latest('student_summary.latest_date');
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Name',
            'Father Name',
            'Enrollment',
            'Mobile',
            'Alt Mobile',
            'Batch',
            'Inst Date',
            'Inst Amount (₹)',
            'Paid Amount (₹)',
            'Due Amount (₹)',
            'Course',
            'Payment Mode',
            'Transaction Date',
            'Receipt No',
            'Status',
            'GST (₹)',
            'Total Transactions',
        ];
    }

    public function map($transaction): array
    {
        static $counter = 0;
        $counter++;

        $student = $transaction->admission?->student;
        $admission = $transaction->admission;
        $batch = $admission?->batch;
        $course = $batch?->course;

        // Calculate paid and due amounts
        $feeTotal = $admission?->fee_total ?? 0;
        $feeDue = $admission?->fee_due ?? 0;
        $paidAmount = $feeTotal - $feeDue;

        return [
            $counter,
            $student?->name ?? 'N/A',
            $student?->father_name ?? 'N/A',
            $student?->enrollment_id ?? 'N/A',
            $student?->phone ?? 'N/A',
            $student?->alt_phone ?: ($student?->whatsapp_no ?? 'N/A'),
            $batch?->batch_name ?? 'N/A',
            $transaction->date ? $transaction->date->format('d M Y') : 'N/A',
            number_format($transaction->total_amount ?? $transaction->amount, 2),
            number_format($paidAmount, 2),
            number_format($feeDue, 2),
            $course?->name ?? 'N/A',
            $transaction->modes ?? $transaction->mode ?? 'N/A',
            $transaction->date ? $transaction->date->format('d M Y') : 'N/A',
            $transaction->receipt_numbers ?? $transaction->receipt_number ?? 'N/A',
            $transaction->statuses ?? $transaction->status ?? 'N/A',
            number_format($transaction->total_gst ?? $transaction->gst ?? 0, 2),
            $transaction->transaction_count ?? 1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
            
            // Auto-fit columns
            'A:R' => ['alignment' => ['horizontal' => 'left']],
            
            // Right align numeric columns
            'I:K' => ['alignment' => ['horizontal' => 'right']],
            'Q:R' => ['alignment' => ['horizontal' => 'right']],
        ];
    }
}
