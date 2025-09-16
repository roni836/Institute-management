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
    public bool $flexiblePayment = false; // New flag for flexible payment mode
    public $flexibleAmount = 0.00; // Amount for flexible payment

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
        if ($name === 'flexibleAmount') {
            $this->updateGstAmount();
        }
        if ($name === 'mode') {
            $this->onModeChanged();
        }
        if ($name === 'flexiblePayment') {
            $this->onFlexiblePaymentChanged();
        }
        if ($name === 'flexibleAmount') {
            $this->onFlexibleAmountChanged();
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

        $this->admission_fee_due = number_format((float)$admission->fee_due, 2, '.', '');

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
                'label'          => "Inst #{$s->installment_no} — Due " . 
                    (is_object($s->due_date) && method_exists($s->due_date, 'format') ? $s->due_date->format('d-M-Y') : 'N/A') . 
                    " — Amount ₹" . number_format((float)$s->amount, 2)
                . " — Paid ₹" . number_format((float)$s->paid_amount, 2)
                . " — Left ₹" . number_format(max(0, (float)$s->amount - (float)$s->paid_amount), 2),
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
        $baseAmount = $this->flexiblePayment ? $this->flexibleAmount : $this->amount;
        if ($this->applyGst && $baseAmount && (float)$baseAmount > 0) {
            $this->gstAmount = round((float) $baseAmount * 0.18, 2);
        } else {
            $this->gstAmount = 0.00;
        }
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $year = date('Y');
        
        // Get the last receipt number for this month
        $lastReceipt = Transaction::where('receipt_number', 'like', $prefix . $year . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();
        
        if ($lastReceipt) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastReceipt->receipt_number, -3);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . $year . str_pad($newSequence, 3, '0', STR_PAD_LEFT);
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
                'label' => "{$a->batch->batch_name} — Due ₹" . number_format((float)$a->fee_due, 2),
                'due'   => number_format((float)$a->fee_due, 2, '.', ''),
            ])
            ->toArray();
    }

    private function onFlexiblePaymentChanged(): void
    {
        if ($this->flexiblePayment) {
            // Clear selected installments when switching to flexible mode
            $this->selectedScheduleIds = [];
            
            // Initialize with a minimum valid amount or copy from existing amount if available
            if (!empty($this->amount) && (float)$this->amount > 0) {
                $this->flexibleAmount = (float)$this->amount;
            } else {
                $this->flexibleAmount = 0.01;
            }
            
            // Sync the main amount field
            $this->amount = $this->flexibleAmount;
        }
    }

    private function onFlexibleAmountChanged(): void
    {
        // Auto-update the main amount field when flexible amount changes
        $this->amount = $this->flexibleAmount ? (float)$this->flexibleAmount : 0;
    }

    /**
     * Get smart payment allocation for flexible payments
     */
    public function getSmartAllocation($amount): array
    {
        if (!$this->admission_id) {
            return [];
        }

        $schedules = PaymentSchedule::where('admission_id', $this->admission_id)
            ->orderBy('installment_no')
            ->get();

        $allocation = [];
        $remaining = (float) $amount;
        $overpayment = 0.00;

        foreach ($schedules as $schedule) {
            if ($remaining <= 0) {
                break;
            }

            $left = max(0.0, (float) $schedule->amount - (float) $schedule->paid_amount);
            
            if ($left > 0) {
                $allocated = min($left, $remaining);
                $allocation[] = [
                    'schedule_id' => $schedule->id,
                    'installment_no' => $schedule->installment_no,
                    'due_date' => $schedule->due_date,
                    'amount' => $allocated,
                    'left_before' => $left,
                    'left_after' => $left - $allocated,
                ];
                $remaining -= $allocated;
            }
        }

        // If there's remaining amount, it's overpayment
        if ($remaining > 0.01) {
            $overpayment = $remaining;
        }

        return [
            'allocation' => $allocation,
            'overpayment' => $overpayment,
            'total_allocated' => $amount - $overpayment,
        ];
    }

    /**
     * Get preview of smart allocation
     */
    public function getSmartAllocationPreview(): array
    {
        if (!$this->flexiblePayment || !$this->flexibleAmount || (float)$this->flexibleAmount < 0.01) {
            return [];
        }

        return $this->getSmartAllocation((float)$this->flexibleAmount);
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
            'flexiblePayment'       => ['boolean'],
            'flexibleAmount'        => $this->flexiblePayment ? ['required', 'numeric', 'min:0.01'] : ['nullable', 'numeric'],
        ]);

        // Auto-set status to success and generate receipt number
        $this->status = 'success';
        $this->receipt_number = $this->generateReceiptNumber();

        // 1) Build the schedule set (prefer multi-select; fallback to single; or flexible)
        $scheduleIds = collect($this->selectedScheduleIds)
            ->map(fn($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($scheduleIds->isEmpty() && ! empty($data['payment_schedule_id'])) {
            $scheduleIds = collect([(int) $data['payment_schedule_id']]);
        }

        // Handle flexible payment mode
        if ($this->flexiblePayment) {
            // Ensure we're using flexibleAmount in flexible payment mode
            $flexAmount = (float)$this->flexibleAmount;
            if ($flexAmount < 0.01) {
                $this->addError('flexibleAmount', 'Payment amount must be at least ₹0.01.');
                return;
            }
            
            $smartAllocation = $this->getSmartAllocation($flexAmount);
            if (empty($smartAllocation['allocation'])) {
                $this->addError('flexibleAmount', 'No pending installments found to allocate payment to.');
                return;
            }
            $scheduleIds = collect($smartAllocation['allocation'])->pluck('schedule_id');
        } else {
            // 2) Early UX checks for traditional mode
            if ($scheduleIds->isEmpty()) {
                $this->addError('selectedScheduleIds', 'Please select at least one installment to pay or enable flexible payment mode.');
                return;
            }
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

            // Use the appropriate amount based on payment mode
            $incoming = $this->flexiblePayment ? (float) $this->flexibleAmount : (float) $this->amount;
            $allocatedTotal = 0.0;
            $lastTransaction = null;

            if ($this->flexiblePayment) {
                // Handle flexible payment with smart allocation
                $smartAllocation = $this->getSmartAllocation($incoming);
                
                foreach ($smartAllocation['allocation'] as $allocation) {
                    $schedule = PaymentSchedule::lockForUpdate()
                        ->where('id', $allocation['schedule_id'])
                        ->first();
                    
                    if (!$schedule) continue;

                    $portion = $allocation['amount'];

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

                    // Update schedule
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
                    $lastTransaction = $tx;
                }

                // Handle overpayment - create a special transaction for future installments
                if ($smartAllocation['overpayment'] > 0.01) {
                    $overpaymentTx = Transaction::create([
                        'admission_id'        => $admission->id,
                        'payment_schedule_id' => null, // No specific schedule for overpayment
                        'amount'              => $smartAllocation['overpayment'],
                        'gst'                 => $this->applyGst ? round($smartAllocation['overpayment'] * 0.18, 2) : 0.00,
                        'date'                => $data['date'],
                        'mode'                => $data['mode'],
                        'reference_no'        => $data['reference_no'] ?? null,
                        'status'              => 'success',
                        'receipt_number'      => $this->receipt_number,
                    ]);
                    $lastTransaction = $overpaymentTx;
                }
            } else {
                // Handle traditional payment mode
                $schedules = PaymentSchedule::lockForUpdate()
                    ->where('admission_id', $admission->id)
                    ->whereIn('id', $scheduleIds)
                    ->orderBy('installment_no')
                    ->get();

                $remaining = $incoming;

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

                    // Update schedule
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
                    $lastTransaction = $tx;
                }
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
            $this->lastTransaction = $lastTransaction;
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
