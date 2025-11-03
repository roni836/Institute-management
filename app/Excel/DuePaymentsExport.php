<?php

namespace App\Excel;

use App\Models\Admission;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DuePaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        public ?string $q = null,
        public ?string $status = 'overdue',
        public ?int $courseId = null,
        public ?int $batchId = null,
    ) {}

    public function query()
    {
        $today = Carbon::today()->toDateString();

        $base = Admission::query()
            ->select([
                'admissions.id',
                'admissions.fee_total',
                'admissions.fee_due',
                'admissions.status as admission_status',

                'students.id as student_id',
                'students.name as student_name',
                'students.father_name',
                'students.phone as student_phone',
                'students.alt_phone',
                'students.whatsapp_no',
                'students.email as student_email',
                'students.enrollment_id',

                'batches.id as batch_id',
                'batches.batch_name',

                'courses.id as course_id',
                'courses.name as course_name',
            ])
            ->join('students', 'students.id', '=', 'admissions.student_id')
            ->join('batches', 'batches.id', '=', 'admissions.batch_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->addSelect([
                // next pending/partial due date
                'next_due_date' => PaymentSchedule::select('due_date')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount'))
                    ->orderBy('due_date')
                    ->limit(1),

                // amount remaining of the *next* installment (amount - paid_amount)
                'next_due_amount' => PaymentSchedule::selectRaw('GREATEST(0, amount - paid_amount)')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount'))
                    ->orderBy('due_date')
                    ->limit(1),

                // total amount of the next installment
                'next_installment_total' => PaymentSchedule::select('amount')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount'))
                    ->orderBy('due_date')
                    ->limit(1),

                // paid amount of the next installment
                'next_installment_paid' => PaymentSchedule::select('paid_amount')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount'))
                    ->orderBy('due_date')
                    ->limit(1),

                // how many installments still pending/partial
                'pending_installments' => PaymentSchedule::selectRaw('COUNT(*)')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount')),

                // total fee due up to today (sum of all unpaid amounts for installments due up to today)
                'total_fee_due_upto_today' => PaymentSchedule::selectRaw('SUM(GREATEST(0, amount - paid_amount))')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount'))
                    ->where('due_date', '<=', $today),

                // last transaction date and amount
                'last_transaction_date' => DB::table('transactions')
                    ->whereColumn('transactions.admission_id', 'admissions.id')
                    ->where('transactions.status', 'success')
                    ->orderBy('transactions.date', 'desc')
                    ->limit(1)
                    ->select('date'),

                'last_transaction_amount' => DB::table('transactions')
                    ->whereColumn('transactions.admission_id', 'admissions.id')
                    ->where('transactions.status', 'success')
                    ->orderBy('transactions.date', 'desc')
                    ->limit(1)
                    ->select('amount'),
            ])
            ->where('admissions.status', 'active')
            ->where('admissions.fee_due', '>', 0);

        // Search by student name/phone
        if ($this->q !== null && $this->q !== '') {
            $q = trim($this->q);
            $base->where(function ($w) use ($q) {
                $w->where('students.name', 'like', "%{$q}%")
                  ->orWhere('students.phone', 'like', "%{$q}%");
            });
        }

        if ($this->courseId) {
            $base->where('courses.id', $this->courseId);
        }

        if ($this->batchId) {
            $base->where('batches.id', $this->batchId);
        }

        // Status filter
        if ($this->status === 'overdue') {
            $base->whereExists(function ($q2) {
                $q2->select(DB::raw(1))
                   ->from('payment_schedules as ps')
                   ->whereColumn('ps.admission_id', 'admissions.id')
                   ->whereIn('ps.status', ['pending','partial'])
                   ->where('ps.amount', '>', DB::raw('ps.paid_amount'))
                   ->where('ps.due_date', '<', Carbon::today()->toDateString());
            });
        } else {
            // For 'all' status, show all dues up to today (including today)
            $base->whereExists(function ($q2) {
                $q2->select(DB::raw(1))
                   ->from('payment_schedules as ps')
                   ->whereColumn('ps.admission_id', 'admissions.id')
                   ->whereIn('ps.status', ['pending','partial'])
                   ->where('ps.amount', '>', DB::raw('ps.paid_amount'))
                   ->where('ps.due_date', '<=', Carbon::today()->toDateString());
            });
        }

        // Order: earliest due first; then highest overall fee_due
        $base->orderByRaw('CASE WHEN next_due_date IS NULL THEN 1 ELSE 0 END, next_due_date ASC')
             ->orderByDesc('admissions.fee_due');

        return $base;
    }

    public function headings(): array
    {
        return [
            'Name',
            'F Name',
            'Enrollment',
            'Primary Contact',
            'Secondary Contact',
            'Course',
            'Batch',
            'Total Amount',
            'Fee Due Upto Today',
            'Installment Date',
            'Installment Amt',
            'Paid Amt',
            'Remaining Amt',
        ];
    }

    public function map($row): array
    {
        static $counter = 0;
        $counter++;

        $nextDueDate = $row->next_due_date ? Carbon::parse($row->next_due_date) : null;
        $isOverdue = $nextDueDate && $nextDueDate->isPast();
        
        // Calculate days overdue or remaining
        $daysCalculation = '';
        if ($nextDueDate) {
            $daysDiff = $nextDueDate->diffInDays(Carbon::today());
            if ($isOverdue) {
                $daysCalculation = $daysDiff . ' days overdue';
            } else {
                $daysCalculation = $daysDiff . ' days remaining';
            }
        }

        // Calculate amounts correctly for the specific installment
        $totalPaidAmount = $row->fee_total - $row->fee_due; // Total amount paid across all installments
        $installmentTotalAmount = $row->next_installment_total ?? 0; // Total amount of this specific installment
        $installmentPaidAmount = $row->next_installment_paid ?? 0; // Amount already paid for this specific installment
        $remainingInstallmentAmount = $row->next_due_amount ?? 0; // Remaining amount for this specific installment
        $totalFeeDueUptoToday = $row->total_fee_due_upto_today ?? 0; // Total fee due up to today

        return [
            $row->student_name,
            $row->father_name ?? '',
            $row->enrollment_id ?? '',
            $row->student_phone ?? '',
            $row->alt_phone ?: ($row->whatsapp_no ?? ''),
            $row->course_name,
            $row->batch_name,
            number_format($row->fee_total, 0),
            number_format($totalFeeDueUptoToday, 0),
            $nextDueDate ? $nextDueDate->format('d/m/Y') : '',
            $installmentTotalAmount ? number_format($installmentTotalAmount, 0) : '',
            $installmentPaidAmount ? number_format($installmentPaidAmount, 0) : '0',
            $remainingInstallmentAmount ? number_format($remainingInstallmentAmount, 0) : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
            
            // Auto-fit columns
            'A:M' => ['alignment' => ['horizontal' => 'left']],
            
            // Right align numeric columns (Total Amount, Fee Due Upto Today, Installment Amt, Paid Amt, Remaining Amt)
            'H:M' => ['alignment' => ['horizontal' => 'right']],
        ];
    }
}
