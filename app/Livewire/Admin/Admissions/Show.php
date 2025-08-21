<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use Carbon\Carbon;
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
        $next = $this->admission->schedules->first(function ($s) {
            return (float) $s->amount > (float) $s->paid_amount;
        });

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

    public function render()
    {
        return view('livewire.admin.admissions.show');
    }
}
