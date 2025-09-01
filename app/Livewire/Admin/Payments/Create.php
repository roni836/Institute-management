<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Admission;
use App\Models\PaymentSchedule;
use App\Models\Student;
use App\Models\Transaction;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
    public bool $applyGst = false;
    public $gstAmount = 0.00;
    public string $receipt_number = '';

    // Helpers
    public array $admissions         = [];
    public array $schedules          = [];
    public string $admission_fee_due = '0.00';

    public array $selectedScheduleIds = [];
    public ?Transaction $lastTransaction = null;

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
        if ($name === 'amount') {
            $this->updateGstAmount();
        }
        if ($name === 'mode') {
            $this->onModeChanged();
        }
    }

    private function onAdmissionChanged(): void
    {
        $this->payment_schedule_id = null;
        $this->selectedScheduleIds = [];
        $this->applyGst = false;
        $this->gstAmount = 0.00;
        $this->receipt_number = $this->generateReceiptNumber();

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
        // Filter out disabled installments (fully paid ones)
        $this->selectedScheduleIds = array_filter($this->selectedScheduleIds, function($id) {
            foreach ($this->schedules as $schedule) {
                if ($schedule['id'] == $id) {
                    return $schedule['left'] > 0;
                }
            }
            return false;
        });

        // Prefill amount with the sum of "left" of selected rows
        $this->amount = number_format($this->selected_total, 2, '.', '');
        // Keep original single-link behavior by picking the first checked schedule
        $this->payment_schedule_id = $this->selectedScheduleIds[0] ?? null;
        // Update GST amount when amount changes
        $this->updateGstAmount();
    }

    public function updatedApplyGst(): void
    {
        $this->updateGstAmount();
    }

    private function updateGstAmount(): void
    {
        if ($this->applyGst && $this->amount) {
            $this->gstAmount = round((float) $this->amount * 0.18, 2);
        } else {
            $this->gstAmount = 0.00;
        }
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $year = date('Y');
        $month = date('m');
        
        // Get the last receipt number for this month
        $lastReceipt = Transaction::where('receipt_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();
        
        if ($lastReceipt) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastReceipt->receipt_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    private function onModeChanged(): void
    {
        if ($this->mode === 'cash') {
            $this->reference_no = null;
        }
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

    // Check if any installments are available for selection
    public function getHasAvailableInstallmentsProperty(): bool
    {
        if (empty($this->schedules)) {
            return false;
        }
        
        foreach ($this->schedules as $schedule) {
            if ($schedule['left'] > 0) {
                return true;
            }
        }
        return false;
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->selectStudent($id);
        }
        $this->date   = now()->format('Y-m-d');
        $this->status = 'success'; // default value
        $this->applyGst = false;
        $this->gstAmount = 0.00;
        $this->receipt_number = $this->generateReceiptNumber();
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
        $this->applyGst = false;
        $this->gstAmount = 0.00;
        $this->receipt_number = $this->generateReceiptNumber();

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
            'admission_id'          => ['required', Rule::exists('admissions', 'id')],
            'payment_schedule_id'   => ['nullable', Rule::exists('payment_schedules', 'id')], // legacy single
            'selectedScheduleIds'   => ['array'],
            'selectedScheduleIds.*' => ['integer', Rule::exists('payment_schedules', 'id')],
            'date'                  => ['required', 'date'],
            'mode'                  => ['required', Rule::in(['cash', 'cheque', 'online'])],
            'reference_no'          => ['nullable', 'string', 'max:100'],
            'amount'                => ['required', 'numeric', 'min:0.01'],
            'applyGst'              => ['boolean'],
        ]);

        // Auto-set status to success and generate receipt number
        $this->status = 'success';
        $this->receipt_number = $this->generateReceiptNumber();

        // 1) Build the schedule set (prefer multi-select; fallback to single)
        $scheduleIds = collect($this->selectedScheduleIds)
            ->map(fn($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($scheduleIds->isEmpty() && ! empty($data['payment_schedule_id'])) {
            $scheduleIds = collect([(int) $data['payment_schedule_id']]);
        }

        // 2) Early UX checks (no exceptions, just inline errors)
        if ($scheduleIds->isEmpty()) {
            $this->addError('selectedScheduleIds', 'Please select at least one installment to pay.');
            return;
        }



        // Load selected schedules (no lock yet; we’ll lock inside the transaction)
        $schedules = PaymentSchedule::where('admission_id', $data['admission_id'])
            ->whereIn('id', $scheduleIds)
            ->orderBy('installment_no')
            ->get();

        if ($schedules->isEmpty()) {
            $this->addError('selectedScheduleIds', 'Selected installments were not found for this admission.');
            return;
        }

        // Check if selected installments have any amount left to pay
        $hasAmountLeft = false;
        foreach ($schedules as $schedule) {
            if (max(0.0, (float) $schedule->amount - (float) $schedule->paid_amount) > 0.00001) {
                $hasAmountLeft = true;
                break;
            }
        }

        if (!$hasAmountLeft) {
            $this->addError('selectedScheduleIds', 'All selected installments are already fully paid. Please select installments with pending amounts.');
            return;
        }

        $selectedLeft = (float) $schedules->sum(fn($s) => max(0.0, (float) $s->amount - (float) $s->paid_amount));

        if ($selectedLeft <= 0.00001) {
            $this->addError('amount', 'All selected installments are already fully paid.');
            return;
        }

        $incoming = (float) $data['amount'];
        if ($incoming - $selectedLeft > 0.00001) {
            // Auto-cap amount and stop; user can press Save again.
            $this->amount = number_format($selectedLeft, 2, '.', '');
            $this->addError('amount', 'Amount reduced to the maximum payable for the selected installments (₹' . number_format($selectedLeft, 2) . ').');
            return;
        }

        // 3) Do the real work atomically
        DB::transaction(function () use ($data, $scheduleIds) {
            $admission = Admission::lockForUpdate()->findOrFail($data['admission_id']);

            $schedules = PaymentSchedule::lockForUpdate()
                ->where('admission_id', $admission->id)
                ->whereIn('id', $scheduleIds)
                ->orderBy('installment_no')
                ->get();

            $incoming       = (float) $this->amount; // possibly auto-capped above
            $remaining      = $incoming;
            $allocatedTotal = 0.0;

            foreach ($schedules as $schedule) {
                if ($remaining <= 0) {
                    break;
                }

                $left = max(0.0, (float) $schedule->amount - (float) $schedule->paid_amount);
                if ($left <= 0) {
                    continue;
                }

                $portion = min($left, $remaining);

                $tx = Transaction::create([
                    'admission_id'        => $admission->id,
                    'payment_schedule_id' => $schedule->id,
                    'amount'              => $portion,
                    'gst'                 => $this->applyGst ? round($portion * 0.18, 2) : 0.00,
                    'date'                => $data['date'],
                    'mode'                => $data['mode'],
                    'reference_no'        => $data['reference_no'] ?? null,
                    'status'              => 'success',
                    'receipt_number'      => $this->receipt_number,
                ]);

                // Since status is always success, always process the payment
                $schedule->paid_amount = (float) $schedule->paid_amount + $portion;
                if ($schedule->paid_amount + 0.00001 >= (float) $schedule->amount) {
                    $schedule->status    = 'paid';
                    $schedule->paid_date = $schedule->paid_date ?? $tx->date;
                } elseif ($schedule->paid_amount > 0) {
                    $schedule->status = 'partial';
                } else {
                    $schedule->status = 'pending';
                }
                $schedule->save();

                $allocatedTotal += $portion;
                $remaining -= $portion;
            }

            // Recompute admission due from ALL schedules (authoritative) and sync
            $recomputedAllDue = (float) PaymentSchedule::where('admission_id', $admission->id)
                ->get()
                ->sum(fn($s) => max(0.0, (float) $s->amount - (float) $s->paid_amount));

            $admission->fee_due = round($recomputedAllDue, 2);
            if ($admission->fee_due <= 0.00001) {
                $admission->status = 'completed';
            }
            $admission->save();

            // Store the transaction for email sending
            $this->lastTransaction = $tx;
        });

        // Send payment confirmation email if student has email
        if ($this->lastTransaction && $this->lastTransaction->admission->student->email) {
            try {
                Mail::to($this->lastTransaction->student->email)->send(new PaymentConfirmationMail($this->lastTransaction));
            } catch (\Exception $e) {
                // Log error but don't fail the payment process
                Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
            }
        }

        session()->flash('success', "Payment recorded successfully! Receipt Number: {$this->receipt_number}");
        return redirect()->route('admin.payments.index');
    }

    public function render()
    {
        // Skip loading search results if student is already selected
        $searchResults = [];
        if (! $this->selectedStudentId && $this->search) {
            $searchResults = Student::where(function ($q) {
                $term = "%{$this->search}%";
                $q->where('name', 'like', $term)
                    ->orWhere('roll_no', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
                ->limit(5)
                ->get()
                ->map(fn($s) => [
                    'id'    => $s->id,
                    'label' => "{$s->name} ({$s->roll_no})",
                ]);
        }

        return view('livewire.admin.payments.create', [
            'students' => $searchResults,
        ]);
    }
}
