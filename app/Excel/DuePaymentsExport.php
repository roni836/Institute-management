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
        public ?int $days = 7,
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
                'students.phone as student_phone',
                'students.email as student_email',
                'students.enrollment_id',

                'batches.id as batch_id',
                'batches.batch_name',

                'courses.id as course_id',
                'courses.name as course_name',
            ])
            ->join('students', 'students.id', '=', 'admissions.student_id')
            ->join('batches', 'batches.id', '=', 'admissions.batch_id')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->addSelect([
                // next pending/partial due date
                'next_due_date' => PaymentSchedule::select('due_date')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->orderBy('due_date')
                    ->limit(1),

                // amount remaining of the *next* installment (amount - paid_amount)
                'next_due_amount' => PaymentSchedule::selectRaw('(amount - paid_amount)')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->orderBy('due_date')
                    ->limit(1),

                // how many installments still pending/partial
                'pending_installments' => PaymentSchedule::selectRaw('COUNT(*)')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial']),
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
                   ->where('ps.due_date', '<', Carbon::today()->toDateString());
            });
        } elseif ($this->status === 'upcoming') {
            $to = Carbon::today()->addDays(max(0, (int)$this->days))->toDateString();
            $base->whereExists(function ($q2) use ($to) {
                $q2->select(DB::raw(1))
                   ->from('payment_schedules as ps')
                   ->whereColumn('ps.admission_id', 'admissions.id')
                   ->whereIn('ps.status', ['pending','partial'])
                   ->whereBetween('ps.due_date', [Carbon::today()->toDateString(), $to]);
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
            'S.No',
            'Student Name',
            'Phone',
            'Email',
            'Enrollment ID',
            'Course',
            'Batch',
            'Fee Total (₹)',
            'Fee Due (₹)',
            'Next Due Date',
            'Next Due Amount (₹)',
            'Pending Installments',
            'Status',
            'Days Overdue/Remaining',
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

        return [
            $counter,
            $row->student_name,
            $row->student_phone,
            $row->student_email ?? 'N/A',
            $row->enrollment_id ?? 'N/A',
            $row->course_name,
            $row->batch_name,
            number_format($row->fee_total, 2),
            number_format($row->fee_due, 2),
            $nextDueDate ? $nextDueDate->format('d M Y') : 'N/A',
            $row->next_due_amount ? number_format(max(0, $row->next_due_amount), 2) : 'N/A',
            $row->pending_installments ?? 0,
            $isOverdue ? 'Overdue' : 'Pending',
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
            
            // Right align numeric columns
            'H:K' => ['alignment' => ['horizontal' => 'right']],
            'L' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
