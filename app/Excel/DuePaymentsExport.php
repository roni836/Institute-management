<?php

namespace App\Excel;

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

        $base = PaymentSchedule::query()
            ->select([
                'payment_schedules.id as schedule_id',
                'payment_schedules.installment_no',
                'payment_schedules.due_date',
                'payment_schedules.amount',
                'payment_schedules.paid_amount',
                'payment_schedules.status as schedule_status',

                'admissions.id as admission_id',
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
            ->join('admissions', 'admissions.id', '=', 'payment_schedules.admission_id')
            ->join('students', 'students.id', '=', 'admissions.student_id')
            ->join('batches', 'batches.id', '=', 'admissions.batch_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->addSelect([
                // last transaction date and amount for this admission
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
            ->whereIn('payment_schedules.status', ['pending', 'partial'])
            ->where('payment_schedules.amount', '>', DB::raw('payment_schedules.paid_amount'));

        // Status filter - determine which installments to include
        if ($this->status === 'overdue') {
            // Only overdue installments (due date before today)
            $base->where('payment_schedules.due_date', '<', $today);
        } else {
            // All installments due up to today (including today)
            $base->where('payment_schedules.due_date', '<=', $today);
        }

        // Search by student name/phone
        if ($this->q !== null && $this->q !== '') {
            $q = trim($this->q);
            $base->where(function ($w) use ($q) {
                $w->where('students.name', 'like', "%{$q}%")
                  ->orWhere('students.phone', 'like', "%{$q}%")
                  ->orWhere('students.enrollment_id', 'like', "%{$q}%");
            });
        }

        if ($this->courseId) {
            $base->where('courses.id', $this->courseId);
        }

        if ($this->batchId) {
            $base->where('batches.id', $this->batchId);
        }

        // Order by due date (earliest first), then by student name
        $base->orderBy('payment_schedules.due_date')
             ->orderBy('students.name')
             ->orderBy('payment_schedules.installment_no');

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
            'Installment No',
            'Due Date',
            'Installment Amount',
            'Paid Amount',
            'Remaining Amount',
            'Status',
            'Days Overdue/Remaining',
        ];
    }

    public function map($row): array
    {
        $dueDate = $row->due_date ? Carbon::parse($row->due_date) : null;
        $isOverdue = $dueDate && $dueDate->isPast();
        
        // Calculate days overdue or remaining
        $daysCalculation = '';
        if ($dueDate) {
            $daysDiff = $dueDate->diffInDays(Carbon::today());
            if ($isOverdue) {
                $daysCalculation = $daysDiff . ' days overdue';
            } else {
                $daysCalculation = $daysDiff . ' days remaining';
            }
        }

        // Calculate remaining amount for this specific installment
        $remainingAmount = max(0, $row->amount - $row->paid_amount);

        // Determine status display
        $statusDisplay = ucfirst($row->schedule_status);
        if ($isOverdue && $remainingAmount > 0) {
            $statusDisplay = 'Overdue';
        }

        return [
            $row->student_name,
            $row->father_name ?? '',
            $row->enrollment_id ?? '',
            $row->student_phone ?? '',
            $row->alt_phone ?: ($row->whatsapp_no ?? ''),
            $row->course_name,
            $row->batch_name,
            $row->installment_no ?? '',
            $dueDate ? $dueDate->format('d/m/Y') : '',
            number_format($row->amount, 0),
            number_format($row->paid_amount, 0),
            number_format($remainingAmount, 0),
            $statusDisplay,
            $daysCalculation,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
            
            // Auto-fit columns
            'A:N' => ['alignment' => ['horizontal' => 'left']],
            
            // Right align numeric columns (Installment Amount, Paid Amount, Remaining Amount)
            'J:L' => ['alignment' => ['horizontal' => 'right']],
            
            // Center align installment number and status
            'H:H' => ['alignment' => ['horizontal' => 'center']],
            'M:M' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
