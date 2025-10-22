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
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    use WithFileUploads;
    public Admission $admission;

                          // Stepper
    public int $step = 1; // 1: Student, 2: Admission & Payment

    // Student fields (matching new-form)
    public $name, $father_name, $mother_name, $email, $phone, $whatsapp_no, $address;
    public $gender, $category, $alt_phone, $dob, $session, $academic_session, $mother_occupation, $father_occupation;
    public $school_name, $school_address, $board, $class, $stream;
    public $enrollment_id;
    public $generated_enrollment_id = null;
    public string $student_status = 'active';
    public ?string $module1       = null;
    public ?string $module2       = null;
    public ?string $module3       = null;
    public ?string $module4       = null;
    public ?string $module5       = null;
    public bool $id_card_required = false;

    // File upload fields
    public $photo_upload;
    public $aadhaar_upload;
    public ?string $existing_photo = null;
    public ?string $existing_aadhaar = null;

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
    public $discount_type = 'fixed';  // fixed or percentage
    public $discount_value = 0.00;    // Amount or percentage value
    public $discount = 0.00;          // Calculated discount amount
    
    public $subtotal = 0.00;          // Amount before GST
    public $lateFee = 0.00;           // Any late fees
    public $tuitionFee = 0.00;        // Base tuition fee
    public $otherFee = 0.00;          // Other fees
    
    public $custom_installments = false; // Flag to track if user has customized installments

    public $status = 'active', $reason = '';
    public $applyGst = false;         // Flag to track if GST should be applied
    public $gstAmount = 0.00;         // Amount of GST
    public $gstRate = 18.00;          // GST rate in percentage
    public $editableInstallments = false;

    // Validation error tracking
    public array $validationErrors = [];
    public bool $showValidationErrors = false;
    public string $validationMessage = '';

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
        $this->class             = $student->class ?? '';
        $this->enrollment_id     = $student->enrollment_id;
        $this->stream            = $student->stream;
        
        // Debug: Log the class value being loaded
        Log::info('Edit form - Loading class value: ' . ($this->class ?? 'NULL'));
        
        // Load existing file paths
        $this->existing_photo = $student->photo;
        $this->existing_aadhaar = $student->aadhaar_document_path;
        
        // Generate enrollment ID based on current values
        $this->updateGeneratedEnrollmentId();
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

        // Calculate fee breakdown - use gross_fee as primary source
        $courseFee = $this->admission->batch->course->gross_fee ?? $this->admission->batch->course->fee ?? 0;
        $this->tuitionFee = $this->admission->batch->course->tution_fee ?? $courseFee;
        $this->otherFee = $this->admission->batch->course->other_fee ?? 0;
        $this->lateFee = $this->lateFee ?? 0;
        
        // Calculate discount based on existing admission data
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            $this->discount = round(($courseFee * $this->discount_value) / 100, 2);
        } else {
            $this->discount = $this->discount_value ?? 0;
        }
        
        $this->subtotal = max(0, $courseFee - $this->discount);

        if ($this->applyGst) {
            $this->gstAmount = round(($this->subtotal * $this->gstRate) / 100, 2);
            $this->fee_total = $this->subtotal + $this->gstAmount;
        } else {
            $this->gstAmount = 0;
            $this->fee_total = $this->subtotal;
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
        // Properties that trigger fee recalculation
        if (in_array($name, [
            'course_id', 'batch_id', 'discount_type', 'discount_value', 
            'mode', 'installments', 'admission_date', 'applyGst', 'gstRate'
        ], true)) {
            $this->recalculate();
        }

        // Load course data when course is selected
        if ($name === 'course_id' && $value) {
            $this->loadCourseData($value);
            $this->resetBatch(); // Reset batch when course changes
        }

        if ($name === 'batch_id') {
            $this->loadBatchData($value);
        }

        // Reset custom installments flag when mode changes
        if ($name === 'mode') {
            $this->custom_installments = false;
        }

        // Reset custom installments flag when number of installments changes
        if ($name === 'installments') {
            $this->custom_installments = false;
        }

        // Handle same_as_permanent address copying
        if ($name === 'same_as_permanent') {
            if ($value) {
                $this->copyPermanentToCorrespondence();
            } else {
                $this->clearCorrespondenceAddress();
            }
        }

        // Handle individual installment updates
        if (str_starts_with($name, 'plan.')) {
            $this->validateInstallmentTotal();
        }

        // Notify frontend (Alpine) about property changes so Alpine can sync
        try {
            $this->dispatch('propertyChanged', property: $name, value: $value);
        } catch (\Throwable $e) {
            // Ignore dispatch failures to avoid breaking server flow
            Log::debug('Failed to dispatch propertyChanged: ' . $e->getMessage());
        }
        
        // Dispatch notification events like new-form
        try {
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Field updated successfully'
            ]);
        } catch (\Throwable $e) {
            Log::debug('Failed to dispatch notify: ' . $e->getMessage());
        }
    }

    public function recalculate(): void
    {
        // Get course information from selected course or batch->course
        if ($this->selected_course) {
            $courseFee = $this->selected_course->gross_fee ?? 0.00;
            $this->tuitionFee = $this->selected_course->tution_fee ?? 0.00;
            $this->otherFee = $this->selected_course->other_fee ?? 0.00;
        } else {
            $batch = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
            $courseFee = $batch?->course?->gross_fee ?? 0.00;
            $this->tuitionFee = $batch?->course?->tution_fee ?? 0.00;
            $this->otherFee = $batch?->course?->other_fee ?? 0.00;
            
            // Update selected_course if batch is selected but course is not
            if ($batch && $batch->course && !$this->selected_course) {
                $this->selected_course = $batch->course;
                $this->course_id = $batch->course->id;
            }
        }
        
        // Fallback: If no course fee is available, use the existing admission fee_total
        if ($courseFee <= 0 && $this->admission && $this->admission->fee_total > 0) {
            $courseFee = $this->admission->fee_total;
            $this->tuitionFee = $courseFee; // Set tuition fee as the total fee for display
        }
        
        // Calculate discount based on discount type and value
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            $discount = min(100, max(0, (float)$this->discount_value)); // Ensure percentage is between 0-100
            $this->discount = round(($courseFee * $discount) / 100, 2);
        } else {
            // Fixed amount discount
            $this->discount = min($courseFee, max(0, (float)$this->discount_value)); // Can't discount more than course fee
        }

        $this->subtotal = max(0.00, round(((float)$courseFee) - $this->discount, 2));

        // Calculate GST if applicable
        if ($this->applyGst) {
            $this->gstAmount = round(($this->subtotal * $this->gstRate) / 100, 2);
            $total = $this->subtotal + $this->gstAmount;
        } else {
            $this->gstAmount = 0.00;
            $total = $this->subtotal;
        }

        $this->fee_total = $total;
        
        // Dispatch events for UI updates
        $this->dispatch('feeRecalculated', 
            subtotal: $this->subtotal,
            discount: $this->discount,
            gstAmount: $this->gstAmount,
            total: $this->fee_total
        );
        
        // Dispatch GST toggled event if GST status changed
        if ($this->applyGst) {
            $this->dispatch('gstToggled', 
                applied: true,
                rate: $this->gstRate,
                amount: $this->gstAmount
            );
        }

        // Don't recalculate if user has customized installments
        if ($this->custom_installments && $this->mode === 'installment') {
            return;
        }

        $this->plan = [];
        $n = ($this->mode === 'installment') ? max(2, (int)$this->installments) : 1;

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
            'phone'              => 'required|string|min:10|max:15|regex:/^[0-9+\-\s()]+$/',
            'whatsapp_no'        => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]+$/',
            'dob'                => 'nullable|date',
            'session'            => 'nullable|string|max:255',
            'academic_session'   => 'nullable|string|max:255',
            'gender'             => 'required|in:male,female,others',
            'category'           => 'required|in:general,obc,sc,st,other',
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
            'photo_upload'       => 'nullable|image|max:2048',
            'aadhaar_upload'     => 'nullable|mimes:jpeg,jpg,png,pdf|max:4096',
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
            1 => [
                'name' => ['required', 'string', 'max:255'],
                'father_name' => ['required', 'string', 'max:255'],
                'mother_name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]+$/'],
                'address_line1' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'district' => ['required', 'string', 'max:100'],
                'pincode' => ['required', 'digits:6'],
                'country' => ['required', 'string', 'max:100'],
                'corr_address_line1' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:255'],
                'corr_city' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:100'],
                'corr_state' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:100'],
                'corr_district' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:100'],
                'corr_pincode' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'digits:6'],
                'corr_country' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:100'],
                'photo_upload' => ['nullable', 'image'],
                'aadhaar_upload' => ['nullable'],
            ],
            2 => [
                'stream' => ['required', 'string', 'in:Engineering,Foundation,Medical,Other'],
                'course_id' => ['required', 'exists:courses,id'],
                'batch_id' => ['required', 'exists:batches,id'],
                'admission_date' => ['required', 'date'],
                'discount_type' => ['nullable', 'in:fixed,percentage'],
                'discount_value' => ['nullable', 'numeric', 'min:0'],
                'mode' => ['required', 'in:full,installment'],
                'fee_total' => ['required', 'numeric', 'min:0'],
            ],
            default => [],
        };
    }

    /**
     * Load course data when a course is selected
     */
    public function loadCourseData($courseId)
    {
        $this->selected_course = Course::find($courseId);
        if ($this->selected_course) {
            $this->tuitionFee = $this->selected_course->tution_fee ?? 0;
            $this->otherFee = $this->selected_course->other_fee ?? 0;
            
            // Count how many batches are available for this course
            $batchCount = Batch::where('course_id', $this->selected_course->id)->count();
            
            // Dispatch event to notify UI that course data is loaded
            $this->dispatch('courseDataLoaded', 
                course_id: $this->selected_course->id,
                name: $this->selected_course->name,
                gross_fee: $this->selected_course->gross_fee,
                net_fee: $this->selected_course->net_fee,
                batch_count: $batchCount
            );
        }
    }
    
    /**
     * Handle course change event
     */
    public function onCourseChange()
    {
        if ($this->course_id) {
            $this->loadCourseData($this->course_id);
        } else {
            $this->selected_course = null;
            $this->resetBatch();
        }
        $this->recalculate();
    }
    
    /**
     * Reset batch selection when course changes
     */
    public function resetBatch()
    {
        $this->batch_id = null;
        $this->selected_batch = null;
    }

    /**
     * Load batch details for summary display
     */
    public function loadBatchData($batchId): void
    {
        $this->selected_batch = $batchId ? Batch::find($batchId) : null;
    }

    /**
     * Copy permanent address to correspondence address
     */
    public function copyPermanentToCorrespondence()
    {
        $this->corr_address_line1 = $this->address_line1;
        $this->corr_address_line2 = $this->address_line2;
        $this->corr_city = $this->city;
        $this->corr_state = $this->state;
        $this->corr_district = $this->district;
        $this->corr_pincode = $this->pincode;
        $this->corr_country = $this->country;
    }

    /**
     * Clear correspondence address fields
     */
    public function clearCorrespondenceAddress()
    {
        $this->corr_address_line1 = '';
        $this->corr_address_line2 = '';
        $this->corr_city = '';
        $this->corr_state = '';
        $this->corr_district = '';
        $this->corr_pincode = '';
        $this->corr_country = 'India';
    }

    /**
     * Toggle same as permanent address
     */
    public function toggleSameAsPermanent()
    {
        $this->same_as_permanent = !$this->same_as_permanent;
        
        if ($this->same_as_permanent) {
            $this->copyPermanentToCorrespondence();
        } else {
            $this->clearCorrespondenceAddress();
        }
    }

    /**
     * Clear validation errors
     */
    public function clearValidationErrors()
    {
        $this->validationErrors = [];
        $this->showValidationErrors = false;
        $this->validationMessage = '';
        $this->resetErrorBag();
    }

    /**
     * Display validation errors in a user-friendly format
     */
    public function displayValidationErrors(array $errors)
    {
        $this->validationErrors = $errors;
        $this->showValidationErrors = true;
        
        // Create a summary message
        $errorCount = count($errors);
        $fieldCount = array_sum(array_map('count', $errors));
        
        $this->validationMessage = "Please fix {$fieldCount} validation error(s) in {$errorCount} field(s) before submitting.";
        
        // Dispatch to frontend for additional UI feedback
        $this->dispatch('showValidationErrors', [
            'errors' => $errors,
            'message' => $this->validationMessage,
            'count' => $fieldCount
        ]);
    }

    /**
     * Get formatted validation errors for display
     */
    public function getFormattedValidationErrors(): array
    {
        $formatted = [];
        foreach ($this->validationErrors as $field => $messages) {
            $fieldLabel = $this->getFieldLabel($field);
            $formatted[] = [
                'field' => $field,
                'label' => $fieldLabel,
                'messages' => $messages
            ];
        }
        return $formatted;
    }

    /**
     * Get user-friendly field labels
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Student Name',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'whatsapp_no' => 'WhatsApp Number',
            'address' => 'Address',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'category' => 'Category',
            'stream' => 'Stream',
            'course_id' => 'Course',
            'batch_id' => 'Batch',
            'admission_date' => 'Admission Date',
            'address_line1' => 'Permanent Address Line 1',
            'address_line2' => 'Permanent Address Line 2',
            'city' => 'Permanent City',
            'state' => 'Permanent State',
            'district' => 'Permanent District',
            'pincode' => 'Permanent Pin Code',
            'country' => 'Permanent Country',
            'corr_address_line1' => 'Correspondence Address Line 1',
            'corr_address_line2' => 'Correspondence Address Line 2',
            'corr_city' => 'Correspondence City',
            'corr_state' => 'Correspondence State',
            'corr_district' => 'Correspondence District',
            'corr_pincode' => 'Correspondence Pin Code',
            'corr_country' => 'Correspondence Country',
            'school_name' => 'School Name',
            'school_address' => 'School Address',
            'board' => 'Board',
            'class' => 'Class',
            'academic_session' => 'Academic Session',
            'discount_type' => 'Discount Type',
            'discount_value' => 'Discount Value',
            'mode' => 'Payment Mode',
            'installments' => 'Number of Installments',
            'fee_total' => 'Total Fee',
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
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
        try {
            $this->clearValidationErrors();
            
            // Validate based on current step
            if ($this->step === 1) {
                $this->validate($this->stepRules($this->step));
                
                // Show success message
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Student information validated successfully'
                ]);
            }
            
            if ($this->step < 2) {
                $this->step++;
                $this->dispatch('stepChanged', step: $this->step);
            }
        } catch (ValidationException $e) {
            $this->displayValidationErrors($e->errors());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please fix the validation errors before proceeding'
            ]);
            
            // Log validation errors for server-side debugging
            Log::warning('Admission edit validation failed', [
                'errors' => $e->errors(),
                'input' => request()->all(),
                'step' => $this->step,
                'same_as_permanent' => $this->same_as_permanent,
            ]);
            
            // Add a general error message
            $this->addError('step', "Please fix the validation errors in Step {$this->step} before proceeding.");
            
            return;
        }
    }

    public function prev()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->dispatch('stepChanged', step: $this->step);
        }
    }

    public function goToStep(int $to)
    {
        // Prevent jumping ahead without validating current step
        if ($to > $this->step) {
            try {
                $this->validate($this->stepRules($this->step));
                $this->clearValidationErrors();
            } catch (ValidationException $e) {
                $this->displayValidationErrors($e->errors());
                $this->addError('step', "Please fix the validation errors in Step {$this->step} before proceeding.");
                throw $e;
            }
        }
        $this->step = max(1, min(2, $to));
        $this->dispatch('stepChanged', step: $this->step);
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
                    'enrollment_id'     => $this->generated_enrollment_id ?: $this->enrollment_id,
                    'status'            => $this->student_status,
                ]);

                // Handle file uploads
                $this->handleFileUploads($student);

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

    private function handleFileUploads($student)
    {
        $studentNeedsSave = false;

        // Handle photo upload
        if ($this->photo_upload) {
            // Delete old photo if exists
            if (!empty($student->photo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            
            // Store new photo
            $photoPath = $this->photo_upload->store('students/photos', 'public');
            $student->photo = $photoPath;
            $this->existing_photo = $photoPath;
            $this->photo_upload = null;
            $studentNeedsSave = true;
        }

        // Handle Aadhaar upload
        if ($this->aadhaar_upload) {
            // Delete old Aadhaar if exists
            if (!empty($student->aadhaar_document_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->aadhaar_document_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->aadhaar_document_path);
            }
            
            // Store new Aadhaar
            $aadhaarPath = $this->aadhaar_upload->store('students/aadhaar', 'public');
            $student->aadhaar_document_path = $aadhaarPath;
            $this->existing_aadhaar = $aadhaarPath;
            $this->aadhaar_upload = null;
            $studentNeedsSave = true;
        }

        // Save student if files were uploaded
        if ($studentNeedsSave) {
            $student->save();
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
        $courses = Course::all();
        $batches = Batch::where('course_id', $this->course_id)->get();
        
        return view('livewire.admin.admissions.edit', [
            'courses' => $courses,
            'batches' => $batches,
        ]);
    }

    /**
     * Update generated enrollment ID when class, stream, or academic_session changes
     */
    public function updateGeneratedEnrollmentId()
    {
        if ($this->class && $this->stream && $this->academic_session) {
            try {
                $this->generated_enrollment_id = $this->generateEnrollmentId();
            } catch (\Exception $e) {
                $this->generated_enrollment_id = null;
            }
        } else {
            $this->generated_enrollment_id = null;
        }
    }

    /**
     * Generate unique enrollment ID based on stream and class (F25AE260001, E25AE270001, etc.)
     * Format: [Stream Letter][Session Year]AE[Class][Sequence]
     */
    private function generateEnrollmentId(): string
    {
    if (!$this->stream) {
        throw new \Exception('Stream is required to generate enrollment ID');
    }
    
    if (!$this->class) {
        throw new \Exception('Class is required to generate enrollment ID');
    }

    // Get session year start (e.g., "2024-25" -> "24", "2025-26" -> "25")
    $sessionYear = $this->academic_session ? $this->getSessionYearStart($this->academic_session) : date('y');
    
    $streamPrefix = match ($this->stream) {
        'Foundation' => 'F',
        'Engineering' => 'E',
        'Medical' => 'M',
        'Other' => 'O',
        default => 'O'
    };
    
    // Use class number directly (5, 6, 7, 8, 9, 10, 11, 12)
    $classNumber = $this->class;

    // Build the pattern to search for: F24AE6, E25AE11, etc.
    $pattern = $streamPrefix . $sessionYear . 'AE2' . $classNumber;
    
    // Find the last enrollment ID for this stream, year, and class (excluding current student)
    $lastStudent = \App\Models\Student::where('stream', $this->stream)
        ->where('enrollment_id', 'like', $pattern . '%')
        ->where('id', '!=', $this->admission->student->id) // Exclude current student
        ->orderBy('enrollment_id', 'desc')
        ->first();

    if ($lastStudent && $lastStudent->enrollment_id) {
        // Extract the sequence number from the last enrollment ID
        $lastNumber = (int)substr($lastStudent->enrollment_id, -4);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    return $pattern . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * Livewire listeners for field changes
     */
    public function updatedClass()
    {
        $this->updateGeneratedEnrollmentId();
    }

    public function updatedStream()
    {
        $this->updateGeneratedEnrollmentId();
    }

    public function updatedAcademicSession()
    {
        $this->updateGeneratedEnrollmentId();
    }

    /**
     * Update installment amount
     */
    public function updateInstallmentAmount($index, $amount)
    {
        if (isset($this->plan[$index])) {
            $this->plan[$index]['amount'] = (float)$amount;
            $this->custom_installments = true;
            $this->validateInstallmentTotal();
        }
    }

    /**
     * Update installment due date
     */
    public function updateInstallmentDate($index, $date)
    {
        if (isset($this->plan[$index])) {
            $this->plan[$index]['due_on'] = $date;
            $this->custom_installments = true;
        }
    }

    /**
     * Validate that installment amounts match total
     */
    public function validateInstallmentTotal()
    {
        if ($this->mode === 'installment' && !empty($this->plan)) {
            $total = array_sum(array_column($this->plan, 'amount'));
            $difference = abs($total - $this->fee_total);
            // Allow for small floating point differences (up to 1 rupee)
            if ($difference > 1.00) {
                $this->addError('plan', "Installment amounts (₹{$total}) must equal the total fee amount (₹{$this->fee_total}). Difference: ₹{$difference}");
            } else {
                $this->resetErrorBag('plan');
            }
        }
    }

    /**
     * Reset to auto-calculated installments
     */
    public function resetInstallments()
    {
        $this->custom_installments = false;
        $this->recalculate();
        $this->resetErrorBag('plan');
    }

    /**
     * Generate installment plan
     */
    public function generateInstallmentPlan()
    {
        if ($this->mode === 'installment' && $this->installments >= 2) {
            $this->custom_installments = false;
            $this->recalculate();
            $this->resetErrorBag('plan');
        }
    }

    /**
     * Get existing photo URL for display
     */
    public function getExistingPhotoUrlProperty(): ?string
    {
        $path = $this->existing_photo ?? null;
        if (!$path) {
            return null;
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Get existing Aadhaar URL for display
     */
    public function getExistingAadhaarUrlProperty(): ?string
    {
        $path = $this->existing_aadhaar ?? null;
        if (!$path) {
            return null;
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Get existing Aadhaar filename for display
     */
    public function getExistingAadhaarFilenameProperty(): ?string
    {
        $path = $this->existing_aadhaar ?? null;
        return $path ? basename($path) : null;
    }
}
