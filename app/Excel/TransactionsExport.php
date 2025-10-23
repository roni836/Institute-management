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
        // Get all transactions with filters applied
        $baseQuery = Transaction::query()
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

        // Group by receipt number and get representative transaction with merged data
        return Transaction::query()
            ->with(['admission.student', 'admission.batch'])
            ->leftJoin('admissions', 'transactions.admission_id', '=', 'admissions.id')
            ->leftJoin('courses', 'admissions.course_id', '=', 'courses.id')
            ->whereIn('transactions.id', function($subQuery) use ($baseQuery) {
                // Get one representative transaction per receipt number (earliest transaction)
                $subQuery->selectRaw('MIN(t.id)')
                    ->from('transactions as t')
                    ->whereIn('t.id', $baseQuery->pluck('transactions.id'))
                    ->groupBy(\DB::raw('COALESCE(t.receipt_number, CONCAT("TX-", t.id))'));
            })
            ->selectRaw('
                transactions.*,
                courses.name as course_name,
                (SELECT SUM(t2.amount) 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                ) as merged_amount,
                (SELECT SUM(t2.gst) 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                ) as merged_gst,
                (SELECT COUNT(*) 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                ) as transaction_count,
                (SELECT GROUP_CONCAT(DISTINCT t2.mode SEPARATOR ", ") 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                ) as merged_modes,
                (SELECT GROUP_CONCAT(DISTINCT t2.status SEPARATOR ", ") 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                ) as merged_statuses,
                (SELECT GROUP_CONCAT(DISTINCT t2.reference_no SEPARATOR ", ") 
                 FROM transactions t2 
                 WHERE COALESCE(t2.receipt_number, CONCAT("TX-", t2.id)) = COALESCE(transactions.receipt_number, CONCAT("TX-", transactions.id))
                 AND t2.reference_no IS NOT NULL
                ) as merged_references
            ')
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
            'GST Amount (₹)',
            'Total Amount (₹)',
            'Payment Mode(s)',
            'Reference No(s)',
            'Receipt Number',
            'Status',
            'Transaction Count',
            'Created At'
        ];
    }

    public function map($transaction): array
    {
        static $counter = 0;
        $counter++;

        $student = $transaction->admission->student ?? null;
        $batch = $transaction->admission->batch ?? null;

        // Use merged amounts from the query
        $mergedAmount = $transaction->merged_amount ?? $transaction->amount;
        $mergedGst = $transaction->merged_gst ?? ($transaction->gst ?? 0);
        $totalAmount = $mergedAmount + $mergedGst;

        // Use merged data or fallback to individual transaction data
        $modes = $transaction->merged_modes ?? ucfirst($transaction->mode ?? 'N/A');
        $references = $transaction->merged_references ?? ($transaction->reference_no ?? 'N/A');
        $statuses = $transaction->merged_statuses ?? ucfirst($transaction->status ?? 'N/A');
        $transactionCount = $transaction->transaction_count ?? 1;

        return [
            $counter,
            $student->name ?? 'N/A',
            $student->enrollment_id ?? 'N/A',
            $transaction->course_name ?? 'N/A',
            $batch->name ?? 'N/A',
            $student->phone ?? 'N/A',
            $transaction->date ? $transaction->date->format('d M Y') : 'N/A',
            number_format($mergedAmount, 2),
            number_format($mergedGst, 2),
            number_format($totalAmount, 2),
            $modes,
            $references,
            $transaction->receipt_number ?? 'N/A',
            $statuses,
            $transactionCount,
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
            
            // Center align S.No, Status, Transaction Count columns
            'A:A' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            'N:O' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }
}
