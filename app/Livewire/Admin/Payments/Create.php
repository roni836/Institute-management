<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Admission;
use App\Models\PaymentSchedule;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public string $search          = '';
    public array $students         = [];
    public ?int $selectedStudentId = null;

    public ?int $admission_id        = null;
    public ?int $payment_schedule_id = null;
    public string $date;
    public string $mode          = 'cash';
    public ?string $reference_no = null;
    public string $status        = 'success';
    public $amount;

    // Helpers
    public array $admissions         = [];
    public array $schedules          = [];
    public string $admission_fee_due = '0.00';

    public array $selectedScheduleIds = [];


    public function updated($name, $value): void
    {
        if ($name === 'search') {
            $this->updatedSearch();
            return;
        }
        if ($name === 'admission_id') {
            $this->onAdmissionChanged();
        }
        if ($name === 'selectedScheduleIds') {
            $this->updatedSelectedScheduleIds();
        }
    }

    private function onAdmissionChanged(): void
    {
        $this->payment_schedule_id = null;
        $this->selectedScheduleIds = [];

        $admission = Admission::with('schedules')->find($this->admission_id);

        if (! $admission) {
            $this->schedules         = [];
            $this->admission_fee_due = '0.00';
            return;
        }

        $this->admission_fee_due = number_format($admission->fee_due, 2, '.', '');

        // Keep numeric fields to compute totals in Blade/Component
        $this->schedules = $admission->schedules()
            ->orderBy('installment_no')
            ->get()
            ->map(fn($s) => [
                'id'             => $s->id,
                'installment_no' => $s->installment_no,
                'due_date'       => optional($s->due_date)?->format('d-M-Y'),
                'amount'         => (float) $s->amount,
                'paid'           => (float) $s->paid_amount,
                'left'           => max(0.0, (float) $s->amount - (float) $s->paid_amount),
                // label kept if you need elsewhere
                'label'          => "Inst #{$s->installment_no} — Due {$s->due_date?->format('d-M-Y')} — Amount ₹"
                . number_format($s->amount, 2)
                . " — Paid ₹" . number_format($s->paid_amount, 2)
                . " — Left ₹" . number_format(max(0, $s->amount - $s->paid_amount), 2),
            ])
            ->toArray();
    }

    public function updatedSelectedScheduleIds(): void
    {
        // Prefill amount with the sum of "left" of selected rows
        $this->amount = number_format($this->selected_total, 2, '.', '');
        // Keep original single-link behavior by picking the first checked schedule
        $this->payment_schedule_id = $this->selectedScheduleIds[0] ?? null;
    }

// Livewire accessor: $this->selected_total
    public function getSelectedTotalProperty(): float
    {
        if (empty($this->selectedScheduleIds) || empty($this->schedules)) {
            return 0.0;
        }
        $map = collect($this->schedules)->keyBy('id');
        $sum = 0.0;
        foreach ($this->selectedScheduleIds as $id) {
            if ($map->has($id)) {
                $sum += (float) $map[$id]['left'];
            }
        }
        return round($sum, 2);
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->selectStudent($id);
        }
        $this->date = now()->format('Y-m-d');
        $this->status = 'success'; // default value
    }

    public function updatedSearch(): void
    {
        $q = trim($this->search);
        if ($q === '') {
            $this->students = [];
            return;
        }

        $this->students = Student::query()
            ->where(fn($q2) =>
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('roll_no', 'like', "%{$q}%")
            )
            ->limit(10)
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'label' => "{$s->name} ({$s->roll_no})",
            ])
            ->toArray();
    }

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
        $this->search            = '';
        $this->students          = [];

        // reset dependent state when changing student
        $this->reset(['admission_id', 'payment_schedule_id', 'schedules']);
        $this->admission_fee_due = '0.00';

        $this->admissions = Admission::with('batch')
            ->where('student_id', $id)
            ->latest('id')
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'label' => "{$a->batch->batch_name} — Due ₹" . number_format($a->fee_due, 2),
                'due'   => number_format($a->fee_due, 2, '.', ''),
            ])
            ->toArray();
    }

    public function save()
    {
        $data = $this->validate([
            'admission_id'        => ['required', Rule::exists('admissions', 'id')],
            'payment_schedule_id' => ['nullable', Rule::exists('payment_schedules', 'id')],
            'date'                => ['required', 'date'],
            'mode'                => ['required', Rule::in(['cash', 'cheque', 'online'])],
            'reference_no'        => ['nullable', 'string', 'max:100'],
            'status'              => ['required', Rule::in(['success', 'pending', 'failed'])],
            'amount'              => ['required', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($data) {
            $admission = Admission::lockForUpdate()->findOrFail($data['admission_id']);

            // Guard: do not exceed admission due
            if (in_array($data['status'], ['success', 'pending'])) {
                $maxPayable = (float) $admission->fee_due;
                if ((float) $data['amount'] > $maxPayable) {
                    abort(422, "Amount exceeds Admission Due (₹" . number_format($maxPayable, 2) . ").");
                }
            }

            $schedule = null;
            if ($data['payment_schedule_id']) {
                $schedule = PaymentSchedule::lockForUpdate()
                    ->where('admission_id', $admission->id)
                    ->findOrFail($data['payment_schedule_id']);

                $remaining = max(0, (float) $schedule->amount - (float) $schedule->paid_amount);
                if (in_array($data['status'], ['success', 'pending']) && (float) $data['amount'] > $remaining) {
                    abort(422, "Amount exceeds Installment Remaining (₹" . number_format($remaining, 2) . ").");
                }
            }

            // Create transaction
            $tx = Transaction::create([
                'admission_id'        => $admission->id,
                'payment_schedule_id' => $schedule?->id,
                'amount'              => $data['amount'],
                'date'                => $data['date'],
                'mode'                => $data['mode'],
                'reference_no'        => $data['reference_no'] ?? null,
                'status'              => $data['status'],
            ]);

            if (in_array($tx->status, ['success', 'pending'])) {
                // Update admission
                $admission->fee_due = max(0, (float) $admission->fee_due - (float) $tx->amount);
                if ($admission->fee_due <= 0.00001) {
                    $admission->status = 'completed';
                }
                $admission->save();

                // Update schedule
                if ($schedule) {
                    $schedule->paid_amount = (float) $schedule->paid_amount + (float) $tx->amount;
                    if ($schedule->paid_amount >= $schedule->amount) {
                        $schedule->status    = 'paid';
                        $schedule->paid_date = $schedule->paid_date ?? $tx->date;
                    } elseif ($schedule->paid_amount > 0) {
                        $schedule->status = 'partial';
                    } else {
                        $schedule->status = 'pending';
                    }
                    $schedule->save();
                }
            }
        });

        session()->flash('success', 'Payment recorded successfully.');
        return redirect()->route('admin.payments.index');
    }

    public function render()
    {
        // Skip loading search results if student is already selected
        $searchResults = [];
        if (!$this->selectedStudentId && $this->search) {
            $searchResults = Student::where(function ($q) {
                $term = "%{$this->search}%";
                $q->where('name', 'like', $term)
                    ->orWhere('roll_no', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'label' => "{$s->name} ({$s->roll_no})"
            ]);
        }

        return view('livewire.admin.payments.create', [
            'students' => $searchResults
        ]);
    }
}
