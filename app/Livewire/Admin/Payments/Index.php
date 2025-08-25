<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Transaction;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

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

    protected $queryString = ['search', 'status', 'mode', 'perPage', 'page'];

    public function updating($name)
    {
        if (in_array($name, ['search', 'status', 'mode', 'perPage'])) {
            $this->resetPage();
        }
    }

    private function getTransactionsQuery()
    {
        return Transaction::query()
            ->with(['admission.student', 'admission.batch'])
            ->when($this->search, fn($q) => $q->where(function($qq) {
                $term = "%{$this->search}%";
                $qq->whereHas('admission.student', fn($s) => 
                    $s->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                )
                ->orWhere('transaction_id', 'like', $term);
            }))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->mode, fn($q) => $q->where('mode', $this->mode))
            ->latest('date');
    }

    private function getPaymentStats()
    {
        return Cache::remember('payment_stats', 300, function () {
            $currentMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();

            // Combine revenue queries into one
            $revenues = Transaction::selectRaw("
                SUM(CASE WHEN status = 'success' AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as current_month_revenue,
                SUM(CASE WHEN status = 'success' AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as last_month_revenue,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_payments,
                SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as completed_payments
            ", [
                $currentMonth->month, $currentMonth->year,
                $lastMonth->month, $lastMonth->year
            ])->first();

            $overduePayments = PaymentSchedule::where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->sum(DB::raw('amount - paid_amount'));

            $percentChange = $revenues->last_month_revenue > 0
                ? round((($revenues->current_month_revenue - $revenues->last_month_revenue) / $revenues->last_month_revenue) * 100, 1)
                : 0;

            return [
                'monthlyRevenue' => [
                    'amount' => $revenues->current_month_revenue,
                    'percentChange' => $percentChange,
                ],
                'pendingPayments' => $revenues->pending_payments,
                'completedPayments' => $revenues->completed_payments,
                'overduePayments' => $overduePayments,
            ];
        });
    }

    public function render()
    {
        return view('livewire.admin.payments.index', [
            'transactions' => $this->getTransactionsQuery()->paginate($this->perPage),
            'stats' => $this->getPaymentStats(),
        ]);
    }
}