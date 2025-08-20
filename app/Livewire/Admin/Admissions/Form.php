<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Form extends Component
{
    public ?Admission $admission = null;

    public $student_id, $batch_id, $admission_date, $discount = 0.00, $mode = 'full';
    public $fee_total                                         = 0.00; // computed from course gross_fee - discount
    public $installments                                      = 2;    // min 2 when mode=installment
    public array $plan                                        = [];   // [['no'=>1,'amount'=>..., 'due_on'=>...], ...]

    public function mount(?int $admissionId = null)
    {
        $this->admission_date = now()->toDateString();

        if ($admissionId) {
            $this->admission  = Admission::with('batch.course', 'paymentSchedules')->findOrFail($admissionId);
            $this->student_id = $this->admission->student_id;
            $this->batch_id   = $this->admission->batch_id;
            $this->discount   = (float) $this->admission->discount;
            $this->mode       = $this->admission->mode;
            $this->fee_total  = (float) $this->admission->fee_total;
            // rebuild plan preview from existing (optional)
            $this->plan = $this->admission->paymentSchedules()
                ->orderBy('installment_no')
                ->get()
                ->map(fn($s) => ['no' => $s->installment_no, 'amount' => (float) $s->amount, 'due_on' => $s->due_date->toDateString()])
                ->toArray();
            if ($this->mode === 'installment') {
                $this->installments = max(2, count($this->plan) ?: 2);
            }
        }

        $this->recalculate();
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['batch_id', 'discount', 'mode', 'installments'], true)) {
            $this->recalculate();
        }
    }

    public function recalculate(): void
    {
        $batch     = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->gross_fee ?? 0.00;
        $discount  = max(0.00, (float) $this->discount);

        $total           = max(0.00, round(((float) $courseFee) - $discount, 2));
        $this->fee_total = $total;

        // Build equal parts with cents handled correctly
        $this->plan = [];
        $n          = ($this->mode === 'installment') ? max(2, (int) $this->installments) : 1;

        if ($n === 1) {
            $this->plan[] = ['no' => 1, 'amount' => $total, 'due_on' => $this->admission_date];
            return;
        }

        // split to 2 decimals fairly: base = floor((total/n)*100)/100, remainder goes to first
        $per = floor(($total / $n) * 100) / 100;
        $sum = round($per * $n, 2);
        $rem = round($total - $sum, 2); // could be 0.01..0.99

        for ($i = 1; $i <= $n; $i++) {
            $amt          = $per + ($i === 1 ? $rem : 0.00);
            $due          = now()->addMonths($i - 1)->toDateString();
            $this->plan[] = ['no' => $i, 'amount' => $amt, 'due_on' => $due];
        }
    }

    protected function rules(): array
    {
        return [
            'student_id'     => 'required|exists:students,id',
            'batch_id'       => 'required|exists:batches,id',
            'admission_date' => 'required|date',
            'discount'       => 'nullable|numeric|min:0',
            'mode'           => 'required|in:full,installment',
            'fee_total'      => 'required|numeric|min:0',
            'installments'   => 'nullable|integer|min:2',
        ];
    }

    public function save()
    {
        $data = $this->validate();
        DB::transaction(function () use ($data) {
            // upsert admission
            $payload = [
                'student_id'     => $data['student_id'],
                'batch_id'       => $data['batch_id'],
                'admission_date' => $data['admission_date'],
                'discount'       => (float) $this->discount,
                'mode'           => $this->mode,
                'fee_total'      => (float) $this->fee_total,
            ];

            if ($this->admission) {
                $this->admission->update($payload);
                $admission = $this->admission->fresh();
            } else {
                $payload['fee_due'] = (float) $this->fee_total;
                $admission          = Admission::create($payload);
                $this->admission    = $admission;
            }

            // Rebuild schedules for installment mode: keep already PAID schedules
            if ($this->mode === 'installment') {
                // delete only unpaid or future schedules
                $admission->paymentSchedules()
                    ->whereIn('status', ['pending', 'partial'])
                    ->delete();

                foreach ($this->plan as $p) {
                    $admission->paymentSchedules()->create([
                        'installment_no' => $p['no'],
                        'due_date'       => $p['due_on'],
                        'amount'         => $p['amount'],
                        'paid_amount'    => 0,
                        'status'         => 'pending',
                    ]);
                }
            } else {
                // full payment mode: remove any unpaid schedules
                $admission->paymentSchedules()
                    ->whereIn('status', ['pending', 'partial'])
                    ->delete();
            }

            // Adjust due from transactions
            $admission->refreshDue();
        });

        session()->flash('ok', 'Admission saved');
        return redirect()->route('admin.admissions.index');
    }

    public function render()
    {
        return view('livewire.admin.admissions.form', [
            'students' => Student::orderBy('name')->get(),
            'batches'  => Batch::with('course')->latest()->get(),
        ]);
    }
}
