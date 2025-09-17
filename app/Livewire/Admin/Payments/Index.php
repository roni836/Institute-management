<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Transaction;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;
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
        $transactions = $this->getTransactionsQuery()->get();

        // Company details (customize as needed)
        $company = [
            ['Institute Name', 'My Institute'],
            ['Address', '123 Main Street, City, State'],
            ['Phone', '+91-1234567890'],
            ['Email', 'info@myinstitute.com'],
            [],
        ];

        $header = [
            'Date', 'Student', 'Batch', 'Amount', 'GST', 'Mode', 'Ref', 'Receipt No', 'Status'
        ];

        $rows = $transactions->map(function($t) {
            return [
                optional($t->date)->format('d-M-Y'),
                optional($t->admission?->student)->name,
                optional($t->admission?->batch)->batch_name,
                $t->amount,
                $t->gst,
                $t->mode,
                $t->reference_no,
                $t->receipt_number,
                $t->status,
            ];
        })->toArray();

        $data = array_merge($company, [$header], $rows);

        // Use PhpSpreadsheet to generate Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($data as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($colLetter . ($rowIdx + 1), $value);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'payments.xlsx';

        // Output to browser
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
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
            ->when($this->fromDate, fn($q) => $q->whereDate('date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('date', '<=', $this->toDate))
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
                ->sum(FacadesDB::raw('amount - paid_amount'));

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