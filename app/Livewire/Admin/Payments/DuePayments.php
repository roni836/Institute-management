<?php

namespace App\Livewire\Admin\Payments;

use App\Excel\DuePaymentsExport;
use App\Models\Admission;
use App\Models\Course;
use App\Models\PaymentSchedule;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
class DuePayments extends Component
{
     use WithPagination;

    // Query string-powered filters
    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(except: 'overdue')]
    public string $status = 'overdue'; // overdue|all

    #[Url(as: 'course_id', except: null)]
    public ?int $courseId = null;

    #[Url(as: 'batch_id', except: null)]
    public ?int $batchId = null;

    public int $perPage = 20;

    // Reset to page 1 whenever a filter changes
    public function updating($name, $value)
    {
        $this->resetPage();
    }

    protected function baseQuery()
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

                // how many installments still pending/partial
                'pending_installments' => PaymentSchedule::selectRaw('COUNT(*)')
                    ->whereColumn('admissions.id', 'payment_schedules.admission_id')
                    ->whereIn('status', ['pending','partial'])
                    ->where('amount', '>', DB::raw('paid_amount')),
            ])
            ->where('admissions.status', 'active')
            ->where('admissions.fee_due', '>', 0);

        // Search by student name/phone
        if ($this->q !== '') {
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

        // Status filter - only show dues up to today
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

    public function exportToExcel()
    {
        $fileName = 'due_payments_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(
            new DuePaymentsExport(
                q: $this->q,
                status: $this->status,
                courseId: $this->courseId,
                batchId: $this->batchId
            ),
            $fileName
        );
    }

    public function render()
    {
        $dues = $this->baseQuery()->paginate($this->perPage);

        // Simple dropdown sources (optional)
        $courses = Course::select('id','name')->orderBy('name')->get();
        $batches = Batch::select('id','batch_name')
            ->when($this->courseId, fn($q) => $q->where('course_id', $this->courseId))
            ->orderBy('batch_name')->get();

        return view('livewire.admin.payments.due-payments', [
            'dues'    => $dues,
            'courses' => $courses,
            'batches' => $batches,
        ]);
    }

}
