<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Course;
use App\Models\PaymentSchedule;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Admission $admission;

                          // Stepper
    public int $step = 1; // 1: Student, 2: Education, 3: Admission, 4: Plan & Review

    // Student fields (matching new-form)
    public $name, $father_name, $mother_name, $email, $phone, $whatsapp_no, $address;
    public $gender, $category, $alt_phone, $dob, $session, $academic_session, $mother_occupation, $father_occupation;
    public $school_name, $school_address, $board, $class, $stream;
    public string $student_status = 'active';
    public ?string $module1       = null;
    public ?string $module2       = null;
    public ?string $module3       = null;
    public ?string $module4       = null;
    public ?string $module5       = null;
    public bool $id_card_required = false;

    // Address fields
    public $address_type                                                                                                   = 'permanent';
    public $address_line1, $address_line2, $city, $state, $district, $pincode, $country                                    = 'India';
    public $corr_address_line1, $corr_address_line2, $corr_city, $corr_state, $corr_district, $corr_pincode, $corr_country = 'India';
    public $same_as_permanent                                                                                              = false;

    // Course and Admission fields (matching new-form)
    public $course_id                        = null;
    public $selected_course                  = null;
    public $selected_batch                   = null;
    public $batch_id, $admission_date, $mode = 'full';
    public $fee_total                        = 0.00, $installments                        = 2, $plan                        = [];

                                      // Discount fields (matching new-form)
    public $discount_type  = 'fixed'; // fixed or percentage
    public $discount_value = 0.00;    // Amount or percentage value
    public $discount       = 0.00;    // Calculated discount amount

    public $subtotal   = 0.00; // Amount before GST
    public $lateFee    = 0.00; // Any late fees
    public $tuitionFee = 0.00; // Base tuition fee
    public $otherFee   = 0.00; // Other fees

    public $status               = 'active', $reason               = '';
    public $applyGst             = false;
    public $gstAmount            = 0.00;  // Amount of GST
    public $gstRate              = 18.00; // GST rate in percentage
    public $editableInstallments = false;

    public function mount(Admission $admission)
    {
        $this->admission = $admission->load(['student', 'batch.course', 'schedules']);

        // Load student data (matching new-form fields)
        $student                 = $this->admission->student;
        $this->name              = $student->name;
        $this->father_name       = $student->father_name;
        $this->mother_name       = $student->mother_name;
        $this->email             = $student->email;
        $this->phone             = $student->phone;
        $this->whatsapp_no       = $student->whatsapp_no;
        $this->address           = $student->address;
        $this->dob               = $student->dob;
        $this->session           = $student->session;
        $this->academic_session  = $student->academic_session;
        $this->gender            = $student->gender;
        $this->category          = $student->category;
        $this->alt_phone         = $student->alt_phone;
        $this->mother_occupation = $student->mother_occupation;
        $this->father_occupation = $student->father_occupation;
        $this->school_name       = $student->school_name;
        $this->school_address    = $student->school_address;
        $this->board             = $student->board;
        $this->class             = $student->class;
        $this->stream            = $student->stream;
        $this->student_status    = $student->status;

        // Load address data if available
        $permanentAddress = $student->addresses->where('type', 'permanent')->first();
        $corrAddress      = $student->addresses->where('type', 'correspondence')->first();

        if ($permanentAddress) {
            $this->address_line1 = $permanentAddress->address_line1;
            $this->address_line2 = $permanentAddress->address_line2;
            $this->city          = $permanentAddress->city;
            $this->state         = $permanentAddress->state;
            $this->district      = $permanentAddress->district;
            $this->pincode       = $permanentAddress->pincode;
            $this->country       = $permanentAddress->country;
        }

        if ($corrAddress) {
            $this->corr_address_line1 = $corrAddress->address_line1;
            $this->corr_address_line2 = $corrAddress->address_line2;
            $this->corr_city          = $corrAddress->city;
            $this->corr_state         = $corrAddress->state;
            $this->corr_district      = $corrAddress->district;
            $this->corr_pincode       = $corrAddress->pincode;
            $this->corr_country       = $corrAddress->country;
            $this->same_as_permanent  = false;
        } else {
            $this->same_as_permanent = true;
        }

        // Load admission data (matching new-form fields)
        $this->batch_id        = $this->admission->batch_id;
        $this->course_id       = $this->admission->batch->course_id ?? null;
        $this->selected_course = $this->admission->batch->course ?? null;
        $this->selected_batch  = $this->admission->batch ?? null;
        $this->admission_date  = $this->admission->admission_date->toDateString();
        $this->discount_type   = $this->admission->discount_type ?? 'fixed';
        $this->discount_value  = $this->admission->discount_value ?? 0;
        $this->mode            = $this->admission->mode;
        $this->fee_total       = $this->admission->fee_total;
        $this->status          = $this->admission->status;
        $this->reason          = $this->admission->reason ?? '';
        $this->applyGst        = $this->admission->is_gst ?? false;
        $this->gstAmount       = $this->admission->gst_amount ?? 0;
        $this->gstRate         = $this->admission->gst_rate ?? 18;

        // Calculate fee breakdown
        $this->tuitionFee = $this->admission->batch->course->fee ?? 0;
        $this->otherFee = $this->otherFee ?? 0;
        $this->lateFee = $this->lateFee ?? 0;
        $this->discount = $this->discount ?? 0;
        $this->subtotal = $this->tuitionFee + $this->otherFee + $this->lateFee;

        if ($this->applyGst) {
            $this->gstAmount = ($this->subtotal * $this->gstRate) / 100;
            $this->fee_total = $this->subtotal + $this->gstAmount - $this->discount;
        } else {
            $this->fee_total = $this->subtotal - $this->discount;
        }
        
        $this->fee_total = max(0, $this->fee_total);

        // Load payment schedule data
        $schedules = $this->admission->schedules;
        if ($schedules->count() > 1) {
            $this->installments = $schedules->count();
            $this->mode         = 'installment';

            // Load existing installment data for editing
            $this->plan = [];
            foreach ($schedules as $schedule) {
                $this->plan[] = [
                    'no'     => $schedule->installment_no,
                    'amount' => $schedule->amount,
                    'due_on' => $schedule->due_date->toDateString(),
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
        $batch     = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->fee ?? 0.00;
        $discount  = max(0.00, (float) $this->discount);

        $total = max(0.00, round(((float) $courseFee) - $discount, 2));

        // Apply GST if enabled
        if ($this->applyGst) {
            $gst = $total * 0.18; // 18% GST
            $total += $gst;
        }

        $this->fee_total = $total;

        $this->plan = [];
        $n          = ($this->mode === 'installment') ? max(2, (int) $this->installments) : 1;

        $anchor = $this->admission_date ? Carbon::parse($this->admission_date) : now();

        if ($n === 1) {
            $this->plan[] = ['no' => 1, 'amount' => $total, 'due_on' => $anchor->toDateString()];
            return;
        }

        $per = floor(($total / $n) * 100) / 100; // equalized
        $sum = round($per * $n, 2);
        $rem = round($total - $sum, 2); // first installment carries rounding

        for ($i = 1; $i <= $n; $i++) {
            $amt          = $per + ($i === 1 ? $rem : 0.00);
            $due          = $anchor->copy()->addMonths($i - 1)->toDateString();
            $this->plan[] = ['no' => $i, 'amount' => $amt, 'due_on' => $due];
        }
    }

    public function toggleEditableInstallments()
    {
        $this->editableInstallments = ! $this->editableInstallments;

        if (! $this->editableInstallments) {
            // When disabling edit mode, recalculate to ensure consistency
            $this->recalculate();
        }
    }

    public function validateInstallmentTotals()
    {
        if ($this->mode === 'installment' && ! empty($this->plan)) {
            $totalInstallments = array_sum(array_map('floatval', array_column($this->plan, 'amount')));
            $expectedTotal     = (float) $this->fee_total;

            if (abs($totalInstallments - $expectedTotal) > 0.01) {
                session()->flash('warning', 'Installment amounts total (₹' . number_format($totalInstallments, 2) . ') does not match the expected total (₹' . number_format($expectedTotal, 2) . ')');
            }
        }
    }

    public function addInstallment()
    {
        if ($this->mode === 'installment') {
            $nextNo       = count($this->plan) + 1;
            $this->plan[] = [
                'no'     => $nextNo,
                'amount' => 0.00,
                'due_on' => now()->addMonths($nextNo - 1)->toDateString(),
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
    public function rules()
    {
        $rules = [
            // Student fields
            'name'               => 'required|string|max:255',
            'father_name'        => 'required|string|max:255',
            'mother_name'        => 'required|string|max:255',
            'email'              => 'required|email|unique:students,email,' . $this->admission->student->id,
            'phone'              => 'required|string|max:20',
            'whatsapp_no'        => 'nullable|string|max:20',
            'dob'                => 'nullable|date',
            'session'            => 'nullable|string|max:255',
            'academic_session'   => 'nullable|string|max:255',
            'gender'             => 'required|in:male,female,other',
            'category'           => 'required|in:General,OBC,SC,ST,EWS',
            'alt_phone'          => 'nullable|string|max:20',
            'mother_occupation'  => 'nullable|string|max:255',
            'father_occupation'  => 'nullable|string|max:255',
            'school_name'        => 'nullable|string|max:255',
            'school_address'     => 'nullable|string',
            'board'              => 'nullable|string|max:255',
            'class'              => 'required|string|max:50',
            'stream'             => 'required|in:Foundation,Engineering,Medical,Other',

            // Address fields
            'address_line1'      => 'required|string|max:255',
            'address_line2'      => 'nullable|string|max:255',
            'city'               => 'required|string|max:100',
            'state'              => 'required|string|max:100',
            'district'           => 'required|string|max:100',
            'pincode'            => 'required|string|max:10',
            'country'            => 'required|string|max:100',

            // Correspondence address (conditional)
            'corr_address_line1' => $this->same_as_permanent ? 'nullable' : 'required|string|max:255',
            'corr_address_line2' => 'nullable|string|max:255',
            'corr_city'          => $this->same_as_permanent ? 'nullable' : 'required|string|max:100',
            'corr_state'         => $this->same_as_permanent ? 'nullable' : 'required|string|max:100',
            'corr_district'      => $this->same_as_permanent ? 'nullable' : 'required|string|max:100',
            'corr_pincode'       => $this->same_as_permanent ? 'nullable' : 'required|string|max:10',
            'corr_country'       => $this->same_as_permanent ? 'nullable' : 'required|string|max:100',

            // Admission fields
            'course_id'          => 'required|exists:courses,id',
            'batch_id'           => 'required|exists:batches,id',
            'admission_date'     => 'required|date',
            'discount_type'      => 'nullable|in:fixed,percentage',
            'discount_value'     => 'nullable|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'mode'               => 'required|in:full,installment',
            'fee_total'          => 'required|numeric|min:0',
            'status'             => 'required|in:active,inactive,suspended',
            'reason'             => 'nullable|string',
        ];

        // Add installment validation if mode is installment
        if ($this->mode === 'installment') {
            $rules['installments']    = 'required|integer|min:2|max:12';
            $rules['plan']            = 'required|array|min:2';
            $rules['plan.*.amount']   = 'required|numeric|min:0';
            $rules['plan.*.due_on'] = 'required|date';
        }

        return $rules;
    }

    /** Rules per step (for Next buttons) */
    protected function stepRules(int $step): array
    {
        return match ($step) {
            1       => [
                'name'          => ['required', 'string', 'max:255'],
                'father_name'   => ['required', 'string', 'max:255'],
                'mother_name'   => ['required', 'string', 'max:255'],
                'email'         => ['required', 'email', 'unique:students,email,' . $this->admission->student->id],
                'phone'         => ['required', 'string', 'max:20'],
                'whatsapp_no'   => ['nullable', 'string', 'max:20'],
                'gender'        => ['required', 'in:male,female,other'],
                'category'      => ['required', 'in:General,OBC,SC,ST,EWS'],
                'dob'           => ['nullable', 'date'],
                'address_line1' => ['required', 'string', 'max:255'],
                'city'          => ['required', 'string', 'max:100'],
                'state'         => ['required', 'string', 'max:100'],
                'district'      => ['required', 'string', 'max:100'],
                'pincode'       => ['required', 'string', 'max:10'],
                'country'       => ['required', 'string', 'max:100'],
            ],
            2       => [
                'school_name'      => ['nullable', 'string', 'max:255'],
                'school_address'   => ['nullable', 'string'],
                'board'            => ['nullable', 'string', 'max:255'],
                'class'            => ['required', 'string', 'max:50'],
                'stream'           => ['required', 'in:Foundation,Engineering,Medical,Other'],
                'session'          => ['nullable', 'string', 'max:255'],
                'academic_session' => ['nullable', 'string', 'max:255'],
            ],
            3       => [
                'course_id'      => ['required', 'exists:courses,id'],
                'batch_id'       => ['required', 'exists:batches,id'],
                'admission_date' => ['required', 'date'],
                'mode'           => ['required', 'in:full,installment'],
                'fee_total'      => ['required', 'numeric', 'min:0'],
            ],
            4       => [
                'status' => ['required', 'in:active,inactive,suspended'],
            ],
            default => []
        };
    }

    // Helper methods from new-form
    public function updatedCourseId()
    {
        if ($this->course_id) {
            $this->selected_course = Course::find($this->course_id);
            $this->tuitionFee      = $this->selected_course->fee ?? 0;
            $this->calculateTotal();
            $this->batch_id       = null;
            $this->selected_batch = null;
        }
    }

    public function updatedBatchId()
    {
        if ($this->batch_id) {
            $this->selected_batch = Batch::find($this->batch_id);
        }
    }

    public function updatedDiscountValue()
    {
        $this->calculateDiscount();
    }
    
    public function updatedOtherFee()
    {
        $this->calculateTotal();
    }
    

    public function updatedLateFee()
    {
        $this->calculateTotal();
    }

    public function updatedGstRate()
    {
        $this->calculateTotal();
    }

    public function updatedSameAsPermanent()
    {
        if ($this->same_as_permanent) {
            $this->corr_address_line1 = $this->address_line1;
            $this->corr_address_line2 = $this->address_line2;
            $this->corr_city          = $this->city;
            $this->corr_state         = $this->state;
            $this->corr_district      = $this->district;
            $this->corr_pincode       = $this->pincode;
            $this->corr_country       = $this->country;
        } else {
            $this->corr_address_line1 = '';
            $this->corr_address_line2 = '';
            $this->corr_city          = '';
            $this->corr_state         = '';
            $this->corr_district      = '';
            $this->corr_pincode       = '';
            $this->corr_country       = 'India';
        }
    }

    private function calculateDiscount()
    {
        if ($this->discount_type === 'percentage') {
            $this->discount = ($this->subtotal * $this->discount_value) / 100;
        } else {
            $this->discount = $this->discount_value ?? 0;
        }
        $this->discount = max(0, (float) $this->discount);
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $this->subtotal = $this->tuitionFee + $this->otherFee + $this->lateFee;

        if ($this->applyGst) {
            $this->gstAmount = ($this->subtotal * $this->gstRate) / 100;
            $this->fee_total = $this->subtotal + $this->gstAmount - $this->discount;
        } else {
            $this->gstAmount = 0;
            $this->fee_total = $this->subtotal - $this->discount;
        }

        // Ensure fee_total is not negative
        $this->fee_total = max(0, $this->fee_total);
    }

    private function getSessionYearStart($session): string
    {
        // Extract year start from session string (e.g., "2024-25" -> "24")
        if (preg_match('/^(\d{4})-\d{2}$/', $session, $matches)) {
            return substr($matches[1], -2);
        }

        // Fallback to current year if format doesn't match
        return date('y');
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
                // Update student with all new fields
                $student = $this->admission->student;
                $student->update([
                    'name'              => $this->name,
                    'father_name'       => $this->father_name,
                    'mother_name'       => $this->mother_name,
                    'email'             => $this->email,
                    'phone'             => $this->phone,
                    'whatsapp_no'       => $this->whatsapp_no,
                    'address'           => $this->address,
                    'dob'               => $this->dob,
                    'session'           => $this->session,
                    'academic_session'  => $this->academic_session,
                    'gender'            => $this->gender,
                    'category'          => $this->category,
                    'alt_phone'         => $this->alt_phone,
                    'mother_occupation' => $this->mother_occupation,
                    'father_occupation' => $this->father_occupation,
                    'school_name'       => $this->school_name,
                    'school_address'    => $this->school_address,
                    'board'             => $this->board,
                    'class'             => $this->class,
                    'stream'            => $this->stream,
                    'status'            => $this->student_status,
                ]);

                // Update or create addresses
                $this->updateStudentAddresses($student);

                // Update admission with new fields
                $this->admission->update([
                    'batch_id'       => $this->batch_id,
                    'admission_date' => $this->admission_date,
                    'mode'           => $this->mode,
                    'discount_type'  => $this->discount_type,
                    'discount_value' => $this->discount_value,
                    'fee_total'      => $this->fee_total,
                    'status'         => $this->status,
                    'reason'         => $this->reason,
                    'is_gst'         => $this->applyGst ?? false,
                    'gst_amount'     => $this->gstAmount ?? 0,
                    'gst_rate'       => $this->gstRate ?? 0,
                ]);

                // Smart payment schedule update - preserve existing schedules with transactions
                $this->updatePaymentSchedules();
            });

            session()->flash('success', 'Admission updated successfully!');
            return redirect()->route('admin.admissions.index');

        } catch (\Exception $e) {
            Log::error('Error updating admission: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Failed to update admission: ' . $e->getMessage());
            throw $e; // Re-throw for debugging
        }
    }

    private function updateStudentAddresses($student)
    {
        // Update permanent address
        $student->addresses()->updateOrCreate(
            ['type' => 'permanent'],
            [
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'city'          => $this->city,
                'state'         => $this->state,
                'district'      => $this->district,
                'pincode'       => $this->pincode,
                'country'       => $this->country,
            ]
        );

        // Update correspondence address
        if (! $this->same_as_permanent) {
            $student->addresses()->updateOrCreate(
                ['type' => 'correspondence'],
                [
                    'address_line1' => $this->corr_address_line1,
                    'address_line2' => $this->corr_address_line2,
                    'city'          => $this->corr_city,
                    'state'         => $this->corr_state,
                    'district'      => $this->corr_district,
                    'pincode'       => $this->corr_pincode,
                    'country'       => $this->corr_country,
                ]
            );
        } else {
            // Delete correspondence address if same as permanent
            $student->addresses()->where('type', 'correspondence')->delete();
        }
    }

    private function updatePaymentSchedules()
    {
        // Get existing schedules with their transactions
        $existingSchedules     = $this->admission->schedules()->with('transactions')->get();
        $existingSchedulesByNo = $existingSchedules->keyBy('installment_no');

        if ($this->mode === 'installment' && ! empty($this->plan)) {
            // Handle multiple installments
            $plannedInstallmentNos = collect($this->plan)->pluck('no')->toArray();

            // Update or create installments from the plan
            foreach ($this->plan as $p) {
                $existingSchedule = $existingSchedulesByNo->get($p['no']);

                if ($existingSchedule && $existingSchedule->transactions->count() > 0) {
                    // Preserve existing schedule with transactions, only update safe fields
                    $existingSchedule->update([
                        'due_date' => $p['due_on'],
                        'remarks'  => $p['remarks'] ?? null,
                        // Don't update amount if there are transactions to preserve data integrity
                    ]);
                } else {
                    // Safe to update or create new schedule
                    PaymentSchedule::updateOrCreate(
                        [
                            'admission_id'   => $this->admission->id,
                            'installment_no' => $p['no'],
                        ],
                        [
                            'due_date'    => $p['due_on'],
                            'amount'      => $p['amount'],
                            'paid_amount' => $existingSchedule->paid_amount ?? 0,
                            'status'      => $existingSchedule->status ?? 'pending',
                            'remarks'     => $p['remarks'] ?? null,
                        ]
                    );
                }
            }

            // Remove schedules that are no longer in the plan (only if they have no transactions)
            foreach ($existingSchedules as $schedule) {
                if (! in_array($schedule->installment_no, $plannedInstallmentNos) &&
                    $schedule->transactions->count() === 0) {
                    $schedule->delete();
                }
            }
        } else {
            // Full payment mode - remove all installment schedules without transactions
            foreach ($existingSchedules as $schedule) {
                if ($schedule->transactions->count() === 0) {
                    $schedule->delete();
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.admissions.edit', [
            'courses' => Course::all(),
            'batches' => Batch::where('course_id', $this->course_id)->get(),
        ]);
    }
}
