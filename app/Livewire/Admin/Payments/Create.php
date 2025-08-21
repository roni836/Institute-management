<?php
namespace App\Livewire\Admin\Payments;

use App\Models\Admission;
use App\Models\PaymentSchedule;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public ?int $admission_id        = null;
    public ?int $payment_schedule_id = null; // optional
    public string $date              = '';
    public ?string $mode             = 'cash'; // cash|cheque|online
    public ?string $reference_no     = null;
    public string $status            = 'success'; // success|pending|failed
    public $amount;

    // For UI helpers
    public $admissions        = [];
    public $schedules         = [];
    public $admission_fee_due = '0.00';

    public function mount()
    {
        $this->date = now()->format('Y-m-d');

        $this->admissions = Admission::with(['student', 'batch'])
            ->orderByDesc('id')
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'label' => $a->student->name . ' — ' . $a->batch->batch_name . ' (Due ₹' . number_format($a->fee_due, 2) . ')',
            ])->toArray();
    }

    public function updatedAdmissionId()
    {
        $this->payment_schedule_id = null;

        $admission = Admission::with('schedules')->find($this->admission_id);
        if (! $admission) {
            $this->schedules         = [];
            $this->admission_fee_due = '0.00';
            return;
        }

        $this->admission_fee_due = number_format($admission->fee_due, 2, '.', '');

        $this->schedules = $admission->schedules()
            ->orderBy('installment_no')
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'label' => "Inst #{$s->installment_no} — Due " .
                $s->due_date?->format('d-M-Y') .
                " — Amount ₹" . number_format($s->amount, 2) .
                " — Paid ₹" . number_format($s->paid_amount, 2) .
                " — Left ₹" . number_format(max(0, $s->amount - $s->paid_amount), 2),
            ])->toArray();
    }

    public function save()
    {
        $rules = [
            'admission_id'        => ['required', Rule::exists('admissions', 'id')],
            'payment_schedule_id' => ['nullable', Rule::exists('payment_schedules', 'id')],
            'date'                => ['required', 'date'],
            'mode'                => ['required', Rule::in(['cash', 'cheque', 'online'])],
            'reference_no'        => ['nullable', 'string', 'max:100'],
            'status'              => ['required', Rule::in(['success', 'pending', 'failed'])],
            'amount'              => ['required', 'numeric', 'min:0.01'],
        ];
        $data = $this->validate($rules);

        DB::transaction(function () use ($data) {
            /** @var Admission $admission */
            $admission = Admission::lockForUpdate()->findOrFail($data['admission_id']);

            // Guard: don't allow paying more than due (for success/pending only)
            if (in_array($data['status'], ['success', 'pending'])) {
                $maxPayable = (float) $admission->fee_due;
                if ((float) $data['amount'] > $maxPayable) {
                    abort(422, "Amount exceeds Admission Due (₹" . number_format($maxPayable, 2) . ").");
                }
            }

            $schedule = null;
            if (! empty($data['payment_schedule_id'])) {
                $schedule = PaymentSchedule::lockForUpdate()->where('admission_id', $admission->id)
                    ->findOrFail($data['payment_schedule_id']);

                // Guard: do not exceed schedule remaining
                $remaining = max(0, (float) $schedule->amount - (float) $schedule->paid_amount);
                if (in_array($data['status'], ['success', 'pending']) && (float) $data['amount'] > $remaining) {
                    abort(422, "Amount exceeds Installment Remaining (₹" . number_format($remaining, 2) . ").");
                }
            }

            // Create Transaction
            $tx = Transaction::create([
                'admission_id'        => $admission->id,
                'payment_schedule_id' => $schedule?->id,
                'amount'              => $data['amount'],
                'date'                => $data['date'],
                'mode'                => $data['mode'],
                'reference_no'        => $data['reference_no'] ?? null,
                'status'              => $data['status'],
            ]);

            // Only reduce due / update schedule if money is actually coming in (success or pending)
            if (in_array($tx->status, ['success', 'pending'])) {
                // Update admission due (never < 0)
                $admission->fee_due = max(0, (float) $admission->fee_due - (float) $tx->amount);
                // Optionally auto-complete admission if fully paid
                if ((float) $admission->fee_due <= 0.00001) {
                    $admission->status = 'completed';
                }
                $admission->save();

                // Update schedule paid/status
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
        return view('livewire.admin.payments.create');
    }
}
