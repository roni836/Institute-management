<?php
namespace App\Livewire\Admin\Payments;

use App\Excel\PaymentsExport;
use App\Excel\TransactionsExport;
use App\Models\Transaction;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    #[Validate('string')]
    public string $search = '';

    #[Validate('string|in:success,pending,failed,')]
    public string $status = '';

    #[Validate('string|in:cash,cheque,online,')]
    public string $mode = '';

    #[Validate('integer|min:5|max:50')]
    public int $perPage = 15;

    // Date range and quick range
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?string $quickRange = null;

    protected $queryString = ['search', 'status', 'mode', 'perPage', 'page', 'fromDate', 'toDate', 'quickRange'];

    public function updating($name)
    {
        if (in_array($name, ['search', 'status', 'mode', 'perPage', 'fromDate', 'toDate', 'quickRange'])) {
            $this->resetPage();
        }
    }

    public function updatedQuickRange($value)
    {
        if ($value) {
            $today = Carbon::today();
            switch ($value) {
                case 'this_week':
                    $this->fromDate = $today->copy()->startOfWeek()->format('Y-m-d');
                    $this->toDate = $today->copy()->endOfWeek()->format('Y-m-d');
                    break;
                case 'this_month':
                    $this->fromDate = $today->copy()->startOfMonth()->format('Y-m-d');
                    $this->toDate = $today->copy()->endOfMonth()->format('Y-m-d');
                    break;
                case 'this_year':
                    $this->fromDate = $today->copy()->startOfYear()->format('Y-m-d');
                    $this->toDate = $today->copy()->endOfYear()->format('Y-m-d');
                    break;
                default:
                    $this->fromDate = null;
                    $this->toDate = null;
            }
        }
    }

    public function exportExcel()
    {
        $fileName = 'payments_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(
            new PaymentsExport(
                search: $this->search,
                status: $this->status,
                mode: $this->mode,
                fromDate: $this->fromDate,
                toDate: $this->toDate
            ),
            $fileName
        );
    }

    public function exportTransactions()
    {
        $fileName = 'transactions_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(
            new TransactionsExport(
                search: $this->search,
                status: $this->status,
                mode: $this->mode,
                fromDate: $this->fromDate,
                toDate: $this->toDate
            ),
            $fileName
        );
    }

    private function getTransactionsQuery()
    {
        // Get one representative transaction per student (admission_id) - showing student's payment summary
        $subQuery = Transaction::select(
                'admission_id',
                FacadesDB::raw('MIN(id) as representative_id'),
                FacadesDB::raw('SUM(amount) as total_amount'),
                FacadesDB::raw('SUM(gst) as total_gst'),
                FacadesDB::raw('COUNT(*) as transaction_count'),
                FacadesDB::raw('MIN(date) as earliest_date'),
                FacadesDB::raw('MAX(date) as latest_date'),
                FacadesDB::raw('GROUP_CONCAT(DISTINCT mode) as modes'),
                FacadesDB::raw('GROUP_CONCAT(DISTINCT status) as statuses'),
                FacadesDB::raw('GROUP_CONCAT(DISTINCT receipt_number) as receipt_numbers')
            )
            ->groupBy('admission_id');

        return Transaction::query()
            ->with(['admission.student', 'admission.batch'])
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

    private function getPaymentStats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Combine revenue queries into one
        $revenues = Transaction::selectRaw("
            SUM(CASE WHEN status = 'success' AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as current_month_revenue,
            SUM(CASE WHEN status = 'success' AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as last_month_revenue,
            SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as completed_payments
        ", [
            $currentMonth->month, $currentMonth->year,
            $lastMonth->month, $lastMonth->year
        ])->first();

        // Get pending payments from admissions fee_due
        $pendingPayments = \App\Models\Admission::where('is_draft', false)
            ->sum('fee_due');


        $overduePayments = PaymentSchedule::where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->sum(FacadesDB::raw('amount - paid_amount'));

        $percentChange = $revenues->last_month_revenue > 0
            ? round((($revenues->current_month_revenue - $revenues->last_month_revenue) / $revenues->last_month_revenue) * 100, 1)
            : 0;

        return [
            'monthlyRevenue' => [
                'amount' => $revenues->current_month_revenue,
                'percentChange' => $percentChange,
            ],
            'pendingPayments' => $pendingPayments,
            'completedPayments' => $revenues->completed_payments,
            'overduePayments' => $overduePayments,
        ];
    }

    public function render()
    {
        return view('livewire.admin.payments.index', [
            'transactions' => $this->getTransactionsQuery()->paginate($this->perPage),
            'stats' => $this->getPaymentStats(),
        ]);
    }
}