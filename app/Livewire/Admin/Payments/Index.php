<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;


#[Layout('components.layouts.admin')]

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = ''; // success|pending|failed or blank

    public string $mode = ''; // cash|cheque|online or blank

    public int $perPage = 15;

    public function updatingSearch()
    {$this->resetPage();}
    public function updatingStatus()
    {$this->resetPage();}
    public function updatingMode()
    {$this->resetPage();}

    private function getPaymentStats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        return [
            'monthlyRevenue' => [
                'amount' => Transaction::where('status', 'success')
                    ->whereMonth('date', $currentMonth->month)
                    ->whereYear('date', $currentMonth->year)
                    ->sum('amount'),
                'percentChange' => $this->calculateMonthlyGrowth(),
            ],
            'pendingPayments' => Transaction::where('status', 'pending')
                ->sum('amount'),
            'completedPayments' => Transaction::where('status', 'success')
                ->sum('amount'),
            'overduePayments' => DB::table('payment_schedules')
                ->where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->sum(DB::raw('amount - paid_amount')),
        ];
    }

    private function calculateMonthlyGrowth()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthRevenue = Transaction::where('status', 'success')
            ->whereMonth('date', $currentMonth->month)
            ->whereYear('date', $currentMonth->year)
            ->sum('amount');

        $lastMonthRevenue = Transaction::where('status', 'success')
            ->whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->sum('amount');

        if ($lastMonthRevenue == 0) return 0;
        
        return round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
    }

    public function render()
    {
        $transactions = Transaction::with(['admission.student', 'admission.batch'])
            ->latest('date')
            ->paginate(10);

        return view('livewire.admin.payments.index', [
            'transactions' => $transactions,
            'stats' => $this->getPaymentStats()
        ]);
    }
}
