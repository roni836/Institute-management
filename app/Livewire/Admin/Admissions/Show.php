<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Show extends Component
{
    public Admission $admission;

    public function mount(Admission $admission)
    {
        // Eager-load everything we need once
        $this->admission = $admission->load([
            'student',
            'batch.course',
            'schedules'    => fn($q)    => $q->orderBy('installment_no'),
            'transactions' => fn($q) => $q->latest('date')->latest(), // date then created_at
        ]);
    }

    #[Computed]
    public function totalPaid(): float
    {
        // Count only successful payments as paid
        return (float) $this->admission->transactions
            ->where('status', 'success')
            ->sum('amount');
    }

    #[Computed]
    public function paidPercent(): int
    {
        $total = (float) $this->admission->fee_total;
        if ($total <= 0) {
            return 0;
        }

        return (int) round(($this->totalPaid / $total) * 100);
    }

    #[Computed]
    public function overdueCount(): int
    {
        $today = Carbon::today();
        return $this->admission->schedules
            ->filter(function ($s) use ($today) {
                $remaining = max(0, (float) $s->amount - (float) $s->paid_amount);
                return $remaining > 0 && Carbon::parse($s->due_date)->lt($today);
            })->count();
    }

    #[Computed]
    public function nextDue(): ?array
    {
        $next = null;
        foreach ($this->admission->schedules as $schedule) {
            if ((float) $schedule->amount > (float) $schedule->paid_amount) {
                $next = $schedule;
                break;
            }
        }

        if (! $next) {
            return null;
        }

        $remaining = max(0, (float) $next->amount - (float) $next->paid_amount);

        return [
            'installment_no' => $next->installment_no,
            'due_date'       => Carbon::parse($next->due_date)->format('d M Y'),
            'remaining'      => number_format($remaining, 2),
            'status'         => $next->status,
        ];
    }

    #[Computed]
    public function stats(): array 
    {
        return [
            'totalPaid' => $this->totalPaid,
            'paidPercent' => $this->paidPercent,
            'totalFee' => (float) $this->admission->fee_total,
            'dueFee' => (float) $this->admission->fee_due,
            'overdueCount' => $this->overdueCount,
            'nextDue' => $this->nextDue,
        ];
    }

    #[Computed]
    public function recentTransactions()
    {
        return $this->admission->transactions
            ->where('status', 'success')
            ->take(5);
    }

    #[Computed]
    public function photoUrl(): ?string
    {
        $path = $this->admission->student->photo ?? null;
        if (!$path) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    #[Computed]
    public function aadhaarUrl(): ?string
    {
        $path = $this->admission->student->aadhaar_document_path ?? null;
        if (!$path) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    #[Computed]
    public function aadhaarFilename(): ?string
    {
        $path = $this->admission->student->aadhaar_document_path ?? null;
        return $path ? basename($path) : null;
    }

    #[Computed]
    public function optionalModules(): array
    {
        return collect(range(1, 5))
            ->map(function (int $index) {
                $value = $this->admission->{"module{$index}"} ?? null;
                return $value !== null ? trim((string) $value) : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.admin.admissions.show', [
            'stats' => $this->stats,
            'recentTransactions' => $this->recentTransactions,
            'photoUrl' => $this->photoUrl,
            'aadhaarUrl' => $this->aadhaarUrl,
            'aadhaarFilename' => $this->aadhaarFilename,
            'optionalModules' => $this->optionalModules,
        ]);
    }
}
