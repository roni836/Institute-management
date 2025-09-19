<?php

namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Admission $admission;
    
    // Stepper
    public int $step = 1; // 1: Student, 2: Education, 3: Admission, 4: Plan & Review

    // Student fields
    public $name, $father_name, $mother_name, $email, $phone, $address;
    public $gender, $category, $alt_phone, $mother_occupation, $father_occupation;
    public $school_name, $school_address, $board, $class;
    public string $student_status = 'active';

    // Admission fields
    public $batch_id, $admission_date, $discount = 0.00, $mode = 'full';
    public $fee_total = 0.00, $installments = 2, $plan = [];
    public $status = 'active', $reason = '';
    public $applyGst = false;
    public $editableInstallments = false;

    public function mount(Admission $admission)
    {
        $this->admission = $admission->load(['student', 'batch.course', 'schedules']);
        
        // Load student data
        $student = $this->admission->student;
        $this->name = $student->name;
        $this->father_name = $student->father_name;
        $this->mother_name = $student->mother_name;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->address = $student->address;
        $this->gender = $student->gender;
        $this->category = $student->category;
        $this->alt_phone = $student->alt_phone;
        $this->mother_occupation = $student->mother_occupation;
        $this->father_occupation = $student->father_occupation;
        $this->school_name = $student->school_name;
        $this->school_address = $student->school_address;
        $this->board = $student->board;
        $this->class = $student->class;
        $this->student_status = $student->status;

        // Load admission data
        $this->batch_id = $this->admission->batch_id;
        $this->admission_date = $this->admission->admission_date->toDateString();
        $this->discount = $this->admission->discount;
        $this->mode = $this->admission->mode;
        $this->fee_total = $this->admission->fee_total;
        $this->status = $this->admission->status;
        $this->reason = $this->admission->reason ?? '';
        $this->applyGst = $this->admission->apply_gst ?? false;
        
        // Load payment schedule data
        $schedules = $this->admission->schedules;
        if ($schedules->count() > 1) {
            $this->installments = $schedules->count();
            $this->mode = 'installment';
            
            // Load existing installment data for editing
            $this->plan = [];
            foreach ($schedules as $schedule) {
                $this->plan[] = [
                    'no' => $schedule->installment_no,
                    'amount' => $schedule->amount,
                    'due_on' => $schedule->due_date->toDateString()
                ];
            }
        } else {
            $this->recalculate();
        }
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['batch_id', 'discount', 'mode', 'installments', 'admission_date', 'applyGst'], true)) {
            $this->recalculate();
        }
        
        // Handle individual installment updates
        if (str_starts_with($name, 'plan.')) {
            $this->validateInstallmentTotals();
        }
    }

    public function recalculate(): void
    {
        $batch = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->gross_fee ?? 0.00;
        $discount = max(0.00, (float) $this->discount);

        $total = max(0.00, round(((float) $courseFee) - $discount, 2));
        
        // Apply GST if enabled
        if ($this->applyGst) {
            $gst = $total * 0.18; // 18% GST
            $total += $gst;
        }
        
        $this->fee_total = $total;

        $this->plan = [];
        $n = ($this->mode === 'installment') ? max(2, (int) $this->installments) : 1;

        $anchor = $this->admission_date ? Carbon::parse($this->admission_date) : now();

        if ($n === 1) {
            $this->plan[] = ['no' => 1, 'amount' => $total, 'due_on' => $anchor->toDateString()];
            return;
        }

        $per = floor(($total / $n) * 100) / 100; // equalized
        $sum = round($per * $n, 2);
        $rem = round($total - $sum, 2); // first installment carries rounding

        for ($i = 1; $i <= $n; $i++) {
            $amt = $per + ($i === 1 ? $rem : 0.00);
            $due = $anchor->copy()->addMonths($i - 1)->toDateString();
            $this->plan[] = ['no' => $i, 'amount' => $amt, 'due_on' => $due];
        }
    }

    public function toggleEditableInstallments()
    {
        $this->editableInstallments = !$this->editableInstallments;
        
        if (!$this->editableInstallments) {
            // When disabling edit mode, recalculate to ensure consistency
            $this->recalculate();
        }
    }

    public function validateInstallmentTotals()
    {
        if ($this->mode === 'installment' && !empty($this->plan)) {
            $totalInstallments = array_sum(array_map('floatval', array_column($this->plan, 'amount')));
            $expectedTotal = (float) $this->fee_total;
            
            if (abs($totalInstallments - $expectedTotal) > 0.01) {
                session()->flash('warning', 'Installment amounts total (₹' . number_format($totalInstallments, 2) . ') does not match the expected total (₹' . number_format($expectedTotal, 2) . ')');
            }
        }
    }

    public function addInstallment()
    {
        if ($this->mode === 'installment') {
            $nextNo = count($this->plan) + 1;
            $this->plan[] = [
                'no' => $nextNo,
                'amount' => 0.00,
                'due_on' => now()->addMonths($nextNo - 1)->toDateString()
            ];
            $this->installments = count($this->plan);
        }
    }

    public function removeInstallment($index)
    {
        if ($this->mode === 'installment' && count($this->plan) > 2) {
            unset($this->plan[$index]);
            $this->plan = array_values($this->plan); // Re-index array
            
            // Update installment numbers
            foreach ($this->plan as $i => $installment) {
                $this->plan[$i]['no'] = $i + 1;
            }
            
            $this->installments = count($this->plan);
            $this->validateInstallmentTotals();
        }
    }

    /** Full rules (used on final save) */
    public function rules(): array
    {
        return [
            // step 1 - Student details
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:male,female,others'],
            'category' => ['nullable', 'string', 'in:sc,st,obc,general,other'],
            'alt_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'student_status' => ['nullable', 'in:active,inactive,alumni'],

            // step 2 - Education details
            'school_name' => ['nullable', 'string', 'max:255'],
            'school_address' => ['nullable', 'string'],
            'board' => ['nullable', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:255'],

            // step 3 - Admission details
            'batch_id' => ['required', 'exists:batches,id'],
            'admission_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'mode' => ['required', 'in:full,installment'],
            'fee_total' => ['required', 'numeric', 'min:0'],
            'installments' => [
                Rule::requiredIf(fn() => $this->mode === 'installment'),
                'integer', 'min:2',
            ],
            'status' => ['required', 'in:active,completed,cancelled'],
            'reason' => [
                Rule::requiredIf(fn() => in_array($this->status, ['cancelled'])),
                'nullable', 'string', 'max:1000',
            ],
        ];
    }

    /** Rules per step (for Next buttons) */
    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
            ],
            2 => [
                'school_name' => ['nullable', 'string', 'max:255'],
                'school_address' => ['nullable', 'string'],
                'board' => ['nullable', 'string', 'max:255'],
                'class' => ['nullable', 'string', 'max:255'],
            ],
            3 => [
                'batch_id' => ['required', 'exists:batches,id'],
                'admission_date' => ['required', 'date'],
                'discount' => ['nullable', 'numeric', 'min:0'],
                'mode' => ['required', 'in:full,installment'],
                'installments' => [
                    Rule::requiredIf(fn() => $this->mode === 'installment'),
                    'integer', 'min:2',
                ],
                'fee_total' => ['required', 'numeric', 'min:0'],
                'status' => ['required', 'in:active,completed,cancelled'],
                'reason' => [
                    Rule::requiredIf(fn() => in_array($this->status, ['cancelled'])),
                    'nullable', 'string', 'max:1000',
                ],
            ],
            default => [],
        };
    }

    public function next()
    {
        $this->validate($this->stepRules($this->step));
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prev()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $to)
    {
        // Prevent jumping ahead without validating current step
        if ($to > $this->step) {
            $this->validate($this->stepRules($this->step));
        }
        $this->step = max(1, min(4, $to));
    }

    public function save()
    {
        $data = $this->validate();

        try {
            DB::transaction(function () use ($data) {
                // Update student
                $student = $this->admission->student;
                $student->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'father_name' => $this->father_name,
                    'mother_name' => $this->mother_name,
                    'address' => $this->address,
                    'gender' => $this->gender,
                    'category' => $this->category,
                    'alt_phone' => $this->alt_phone,
                    'mother_occupation' => $this->mother_occupation,
                    'father_occupation' => $this->father_occupation,
                    'school_name' => $this->school_name,
                    'school_address' => $this->school_address,
                    'board' => $this->board,
                    'class' => $this->class,
                    'status' => $this->student_status,
                ]);

                // Update admission
                $this->admission->update([
                    'batch_id' => $this->batch_id,
                    'admission_date' => $this->admission_date,
                    'mode' => $this->mode,
                    'discount' => $this->discount,
                    'fee_total' => $this->fee_total,
                    'fee_due' => $this->fee_total, // Recalculate due amount
                    'status' => $this->status,
                    'reason' => $this->reason,
                    'is_gst' => $this->applyGst ?? false,
                ]);

                // Update payment schedules
                $this->admission->schedules()->delete(); // Remove old schedules
                
                if ($this->mode === 'installment') {
                    // Create multiple installments
                    foreach ($this->plan as $p) {
                        $this->admission->schedules()->create([
                            'installment_no' => $p['no'],
                            'due_date' => $p['due_on'],
                            'amount' => $p['amount'],
                            'status' => 'pending',
                        ]);
                    }
                } else {
                    // Create single payment schedule for full payment
                    $this->admission->schedules()->create([
                        'installment_no' => 1,
                        'due_date' => $this->admission_date,
                        'amount' => $this->fee_total,
                        'status' => 'pending',
                    ]);
                }
            });

        } catch (\Exception $e) {
            Log::error('Failed to update admission: ' . $e->getMessage());
            session()->flash('error', 'Failed to update admission. Please try again.');
            return;
        }

        session()->flash('ok', 'Admission updated successfully.');
        return redirect()->route('admin.admissions.index');
    }

    public function render()
    {
        return view('livewire.admin.admissions.edit', [
            'batches' => Batch::with('course')->latest()->get(),
            'progress' => match ($this->step) {
                1 => 25, 2 => 50, 3 => 75, default => 100,
            },
        ]);
    }
}
