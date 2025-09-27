<?php

namespace App\Livewire\Admin\Admissions;

use App\Mail\AdmissionConfirmationMail;
use App\Models\Admission;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class NewForm extends Component
{
    // Stepper
    public int $step = 1; // 1: Student, 2: Admission & Payment

    // Student fields (new student at admission time)
    public $name, $father_name, $mother_name, $email, $phone, $whatsapp_no, $address, $admission;
    public $gender, $category, $alt_phone, $dob, $session, $academic_session, $mother_occupation, $father_occupation;
    public $stream;
    public string $student_status = 'active';
    
    // Address fields
    public $address_type = 'permanent';
    public $address_line1, $address_line2, $city, $state, $district, $pincode, $country = 'India';
    public $corr_address_line1, $corr_address_line2, $corr_city, $corr_state, $corr_district, $corr_pincode, $corr_country = 'India';
    public $same_as_permanent = false;

    // Course and Admission fields
    public $course_id = null;
    public $selected_course = null;
    public $batch_id, $admission_date, $mode = 'full';
    public $fee_total = 0.00, $installments = 2, $plan = [];
    
    // Discount fields
    public $discount_type = 'fixed';  // fixed or percentage
    public $discount_value = 0.00;    // Amount or percentage value
    public $discount = 0.00;          // Calculated discount amount
    
    public $subtotal = 0.00;          // Amount before GST
    public $lateFee = 0.00;           // Any late fees
    public $tuitionFee = 0.00;        // Base tuition fee
    public $otherFee = 0.00;          // Other fees
    
    public $custom_installments = false; // Flag to track if user has customized installments
    public $applyGst = false;         // Flag to track if GST should be applied
    public $gstAmount = 0.00;         // Amount of GST
    public $gstRate = 18.00;          // GST rate in percentage

    public ?string $searchPhone = '';
    public bool $isExistingStudent = false;
    public $foundStudents = [];
    public $selectedStudentId = null;
    public bool $showStudentSelection = false;

    public function mount()
    {
        $this->admission_date = now()->toDateString();
        $this->applyGst = false;
        $this->recalculate();
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

        // Reset custom installments flag when mode changes
        if ($name === 'mode') {
            $this->custom_installments = false;
        }

        // Reset custom installments flag when number of installments changes
        if ($name === 'installments') {
            $this->custom_installments = false;
        }

        // Notify frontend (Alpine) about property changes so Alpine can sync
        try {
            $this->dispatch('propertyChanged', property: $name, value: $value);
        } catch (\Throwable $e) {
            // Ignore dispatch failures to avoid breaking server flow
            Log::debug('Failed to dispatch propertyChanged: ' . $e->getMessage());
        }

        // propertyChanged dispatch is already used to notify frontend via Livewire client events.
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
    }

    public function updatedSearchPhone()
    {
        if (empty($this->searchPhone)) {
            $this->resetStudentSearch();
            return;
        }

        $students = Student::where('phone', $this->searchPhone)->get();
        
        if ($students->count() > 0) {
            $this->foundStudents = $students;
            $this->isExistingStudent = true;
            
            if ($students->count() === 1) {
                // Only one student found, auto-select
                $this->selectStudent($students->first()->id);
                $this->showStudentSelection = false;
            } else {
                // Multiple students found, show selection
                $this->showStudentSelection = true;
                $this->resetStudentForm();
            }
        } else {
            $this->resetStudentSearch();
        }
    }

    /**
     * Select a student from the found students list
     */
    public function selectStudent($studentId)
    {
        $student = Student::with('addresses')->find($studentId);
        if ($student) {
            $this->selectedStudentId = $studentId;
            $this->showStudentSelection = false;
            
            // Pre-fill form with selected student data
            $this->name = $student->name;
            $this->email = $student->email;
            $this->phone = $student->phone;
            $this->whatsapp_no = $student->whatsapp_no;
            $this->father_name = $student->father_name;
            $this->mother_name = $student->mother_name;
            $this->address = $student->address;
            $this->dob = $student->dob;
            $this->session = $student->session;
            $this->academic_session = $student->academic_session;
            $this->gender = $student->gender;
            $this->category = $student->category;
            $this->alt_phone = $student->alt_phone;
            $this->mother_occupation = $student->mother_occupation;
            $this->father_occupation = $student->father_occupation;
            $this->stream = $student->stream;
            $this->student_status = $student->status;
            
            // Pre-fill addresses if available
            $permanentAddress = $student->addresses->where('type', 'permanent')->first();
            $corrAddress = $student->addresses->where('type', 'correspondence')->first();
            
            if ($permanentAddress) {
                $this->address_line1 = $permanentAddress->address_line1;
                $this->address_line2 = $permanentAddress->address_line2;
                $this->city = $permanentAddress->city;
                $this->state = $permanentAddress->state;
                $this->district = $permanentAddress->district;
                $this->pincode = $permanentAddress->pincode;
                $this->country = $permanentAddress->country;
            }
            
            if ($corrAddress) {
                $this->corr_address_line1 = $corrAddress->address_line1;
                $this->corr_address_line2 = $corrAddress->address_line2;
                $this->corr_city = $corrAddress->city;
                $this->corr_state = $corrAddress->state;
                $this->corr_district = $corrAddress->district;
                $this->corr_pincode = $corrAddress->pincode;
                $this->corr_country = $corrAddress->country;
                $this->same_as_permanent = false;
            } else {
                $this->same_as_permanent = true;
            }
        }
    }

    /**
     * Reset student search and form
     */
    public function resetStudentSearch()
    {
        $this->isExistingStudent = false;
        $this->foundStudents = [];
        $this->selectedStudentId = null;
        $this->showStudentSelection = false;
        $this->resetStudentForm();
    }

    /**
     * Reset student form fields
     */
    public function resetStudentForm()
    {
        $this->reset([
            'name', 'email', 'father_name', 'mother_name', 'address', 'gender', 'category', 
            'dob', 'session', 'academic_session', 'alt_phone', 'whatsapp_no', 'mother_occupation', 
            'father_occupation', 'stream', 'address_line1', 'address_line2', 'city', 'state', 
            'district', 'pincode', 'country', 'corr_address_line1', 'corr_address_line2', 
            'corr_city', 'corr_state', 'corr_district', 'corr_pincode', 'corr_country', 'same_as_permanent'
        ]);
        $this->resetErrorBag('batch_id');
    }

    /**
     * Create new student option
     */
    public function createNewStudent()
    {
        $this->showStudentSelection = false;
        $this->selectedStudentId = null;
        $this->isExistingStudent = false;
        $this->phone = $this->searchPhone; // Keep the searched phone number
        $this->resetStudentForm();
    }

    /** Full rules (used on final save) */
    public function rules(): array
    {
        $rules = [
            // step 1 - Student details
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_no' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'dob' => ['nullable'],
            'session' => ['nullable'],
            'academic_session' => ['nullable', 'string', 'max:10'],
            'gender' => ['nullable', 'string', 'in:male,female,others'],
            'category' => ['nullable', 'string', 'in:sc,st,obc,general,other'],
            'alt_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'stream' => ['required', 'string', 'in:Engineering,Foundation,Medical,Other'],
            'student_status' => ['nullable', 'in:active,inactive,alumni'],
            
            // Address fields
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            
            'same_as_permanent' => ['boolean'],
            'corr_address_line1' => ['required_if:same_as_permanent,false', 'string', 'max:255'],
            'corr_address_line2' => ['nullable', 'string', 'max:255'],
            'corr_city' => ['nullable', 'string', 'max:100'],
            'corr_state' => ['nullable', 'string', 'max:100'],
            'corr_district' => ['nullable', 'string', 'max:100'],
            'corr_pincode' => ['nullable', 'string', 'max:10'],
            'corr_country' => ['nullable', 'string', 'max:100'],

            // step 2 - Admission details
            'course_id' => [
                'required',
                'exists:courses,id',
            ],
            'batch_id' => [
                'required',
                'exists:batches,id',
            ],
            'admission_date' => ['required', 'date'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'mode' => ['required', 'in:full,installment'],
            'fee_total' => ['required', 'numeric', 'min:0'],
            'applyGst' => ['boolean'],
            'gstRate' => ['required_if:applyGst,true', 'numeric', 'min:0', 'max:100'],
            'gstAmount' => ['required_if:applyGst,true', 'numeric', 'min:0'],
            'installments' => [
                Rule::requiredIf(fn() => $this->mode === 'installment'),
                'integer', 'min:2',
            ],
            'plan' => [
                function ($attribute, $value, $fail) {
                    if ($this->mode === 'installment' && !empty($this->plan)) {
                        $total = array_sum(array_column($this->plan, 'amount'));
                        if (abs($total - $this->fee_total) > 0.01) {
                            $fail('Installment amounts must equal the total fee amount.');
                        }
                    }
                },
            ],
        ];

        return $rules;
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
                'stream' => ['required', 'string', 'in:Engineering,Foundation,Medical,Other'],
                'course_id' => [
                    'required',
                    'exists:courses,id',
                ],
                'batch_id' => [
                    'required',
                    'exists:batches,id',
                ],
                'admission_date' => ['required', 'date'],
                'discount_type' => ['required', 'in:fixed,percentage'],
                'discount_value' => ['nullable', 'numeric', 'min:0'],
                'mode' => ['required', 'in:full,installment'],
                'installments' => [
                    Rule::requiredIf(fn() => $this->mode === 'installment'),
                    'integer', 'min:2',
                ],
                'fee_total' => ['required', 'numeric', 'min:0'],
            ],
            default => [],
        };
    }

    public function next()
    {
        $this->validate($this->stepRules($this->step));
        if ($this->step < 2) {
            $this->step++;
            // Dispatch via Livewire's client dispatch for Alpine
            $this->dispatch('stepChanged', step: $this->step);
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
            $this->validate($this->stepRules($this->step));
        }
        $this->step = max(1, min(2, $to));
        $this->dispatch('stepChanged', step: $this->step);
    }

    public function recalculate(): void
    {
        // Get course information from selected course or batch->course
        if ($this->selected_course) {
            $courseFee = $this->selected_course->gross_fee ?? 0.00;
        } else {
            $batch = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
            $courseFee = $batch?->course?->gross_fee ?? 0.00;
            
            // Update selected_course if batch is selected but course is not
            if ($batch && $batch->course && !$this->selected_course) {
                $this->selected_course = $batch->course;
                $this->course_id = $batch->course->id;
            }
        }
        
        // Calculate discount based on type and value
        $this->tuitionFee = $this->selected_course->tution_fee ?? 0.00;
        $this->otherFee = $this->selected_course->other_fee ?? 0.00;
        
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
        );        // Dispatch GST toggled event if GST status changed
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

    /**
     * Generate unique enrollment ID based on stream (F25APU0001, E25APU0001, M25APU0001, O25APU0001)
     */
    private function generateEnrollmentId(): string
    {
        if (!$this->stream) {
            throw new \Exception('Stream is required to generate enrollment ID');
        }

        $year = date('y'); // 2-digit year (25 for 2025)
        $streamPrefix = match ($this->stream) {
            'Foundation' => 'F',
            'Engineering' => 'E',
            'Medical' => 'M',
            'Other' => 'O',
            default => 'O'
        };

        // Find the last enrollment ID for this stream and year
        $lastStudent = Student::where('stream', $this->stream)
            ->where('enrollment_id', 'like', $streamPrefix . $year . 'APU%')
            ->orderBy('enrollment_id', 'desc')
            ->first();

        if ($lastStudent && $lastStudent->enrollment_id) {
            // Extract the number from the last enrollment ID
            $lastNumber = (int)substr($lastStudent->enrollment_id, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $streamPrefix . $year . 'APU' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate a sequential roll number
     */
    private function generateRollNumber(): string
    {
        $year = date('y'); // Last two digits of current year
        $streamPrefix = match ($this->stream) {
            'Foundation' => 'F',
            'Engineering' => 'E',
            'Medical' => 'M',
            'Other' => 'O',
            default => 'X'
        };
        
        // Find the last roll number for this stream and year
        $lastStudent = Student::where('stream', $this->stream)
            ->where('roll_no', 'like', 'ROLL' . $streamPrefix . $year . '%')
            ->orderBy('roll_no', 'desc')
            ->first();
            
        if ($lastStudent && $lastStudent->roll_no) {
            // Extract the number from the last roll number
            $lastNumber = (int)substr($lastStudent->roll_no, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'ROLL' . $streamPrefix . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate a unique student UID
     */
    private function generateStudentUid(): string
    {
        // Generate a unique ID using timestamp, random number and stream prefix
        $timestamp = now()->format('ymdHis');
        $streamPrefix = substr($this->stream, 0, 1);
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return 'STU' . $streamPrefix . $timestamp . $random;
    }

    /**
     * Get next enrollment ID for display (without creating)
     */
    public function getNextEnrollmentId(): string
    {
        if (!$this->stream) {
            return 'Select stream first';
        }
        return $this->generateEnrollmentId();
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
            if (abs($total - $this->fee_total) > 0.01) {
                $this->addError('plan', 'Installment amounts must equal the total fee amount.');
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

    public function save()
    {
        $data = $this->validate();

        $admission = null;

        try {
            DB::transaction(function () use ($data, &$admission) {
                // Find or create student
                if ($this->selectedStudentId) {
                    // Use selected existing student
                    $student = Student::find($this->selectedStudentId);
                    
                    // Update student with new information
                    $student->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'whatsapp_no' => $this->whatsapp_no,
                        'dob' => $this->dob,
                        'father_name' => $this->father_name,
                        'mother_name' => $this->mother_name,
                        'address' => $this->address,
                        'gender' => $this->gender,
                        'category' => $this->category,
                        'alt_phone' => $this->alt_phone,
                        'mother_occupation' => $this->mother_occupation,
                        'father_occupation' => $this->father_occupation,
                        'stream' => $this->stream,
                        'academic_session' => $this->academic_session,
                        'status' => $this->student_status,
                        'admission_date' => $this->admission_date,
                    ]);
                } else {
                    // Generate unique identifiers for new student
                    $enrollmentId = $this->generateEnrollmentId();
                    
                    // Create new student
                    $student = Student::create([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'whatsapp_no' => $this->whatsapp_no,
                        'dob' => $this->dob,
                        'father_name' => $this->father_name,
                        'mother_name' => $this->mother_name,
                        'address' => $this->address,
                        'gender' => $this->gender,
                        'category' => $this->category,
                        'alt_phone' => $this->alt_phone,
                        'mother_occupation' => $this->mother_occupation,
                        'father_occupation' => $this->father_occupation,
                        'stream' => $this->stream,
                        'academic_session' => $this->academic_session,
                        'status' => $this->student_status,
                        'admission_date' => $this->admission_date,
                        'enrollment_id' => $enrollmentId,
                        'roll_no' => $this->generateRollNumber(),
                        'student_uid' => $this->generateStudentUid(),
                    ]);
                }
                
                // Save or update addresses
                // Permanent Address
                $permanentAddress = [
                    'type' => 'permanent',
                    'address_line1' => $this->address_line1,
                    'address_line2' => $this->address_line2,
                    'city' => $this->city,
                    'state' => $this->state,
                    'district' => $this->district,
                    'pincode' => $this->pincode,
                    'country' => $this->country,
                    'is_primary' => true,
                ];
                
                // Check if permanent address exists
                $existingPermanentAddress = $student->addresses()->where('type', 'permanent')->first();
                if ($existingPermanentAddress) {
                    $existingPermanentAddress->update($permanentAddress);
                } else {
                    $student->addresses()->create($permanentAddress);
                }
                
                // Correspondence Address - only create if not same as permanent
                if (!$this->same_as_permanent) {
                    $corrAddress = [
                        'type' => 'correspondence',
                        'address_line1' => $this->corr_address_line1,
                        'address_line2' => $this->corr_address_line2,
                        'city' => $this->corr_city,
                        'state' => $this->corr_state,
                        'district' => $this->corr_district,
                        'pincode' => $this->corr_pincode,
                        'country' => $this->corr_country,
                        'is_primary' => false,
                    ];
                    
                    // Check if correspondence address exists
                    $existingCorrAddress = $student->addresses()->where('type', 'correspondence')->first();
                    if ($existingCorrAddress) {
                        $existingCorrAddress->update($corrAddress);
                    } else {
                        $student->addresses()->create($corrAddress);
                    }
                } else {
                    // Delete correspondence address if it exists and same_as_permanent is true
                    $student->addresses()->where('type', 'correspondence')->delete();
                }

                // Create admission
                $admission = Admission::create([
                    'student_id' => $student->id,
                    'course_id' => $this->course_id,
                    'batch_id' => $this->batch_id,
                    'admission_date' => $this->admission_date,
                    'mode' => $this->mode,
                    'discount' => $this->discount,
                    'discount_type' => $this->discount_type,
                    'discount_value' => $this->discount_value,
                    'fee_total' => $this->fee_total,
                    'fee_due' => $this->fee_total,
                    'status' => 'active',
                    'session' => $this->session ?? date('Y'), // Default to current year if not provided
                    'is_gst' => $this->applyGst ?? false,
                    'gst_amount' => $this->gstAmount ?? 0,
                    'gst_rate' => $this->gstRate ?? 0,
                ]);

                // Create payment schedule
                if ($this->mode === 'installment') {
                    // Create multiple installments
                    foreach ($this->plan as $p) {
                        $admission->schedules()->create([
                            'installment_no' => $p['no'],
                            'due_date' => $p['due_on'],
                            'amount' => $p['amount'],
                            'status' => 'pending',
                        ]);
                    }
                } else {
                    // Create single payment schedule for full payment
                    $admission->schedules()->create([
                        'installment_no' => 1,
                        'due_date' => $this->admission_date,
                        'amount' => $this->fee_total,
                        'status' => 'pending',
                    ]);
                }
                
                // Log successful admission creation
                Log::info('New admission created', [
                    'admission_id' => $admission->id,
                    'student_id' => $student->id,
                    'batch_id' => $this->batch_id,
                    'fee_total' => $this->fee_total
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Admission creation failed: ' . $e->getMessage());
            $this->addError('general', 'Failed to create admission. Please try again.');
            return;
        }

        // Send admission confirmation email if student has email
        if ($admission && $admission->student->email) {
            try {
                Mail::to($admission->student->email)->send(new AdmissionConfirmationMail($admission));
            } catch (\Exception $e) {
                // Log error but don't fail the admission process
                Log::error('Failed to send admission confirmation email: ' . $e->getMessage());
            }
        }

        session()->flash('ok', 'Admission created');
        return redirect()->route('admin.admissions.index');
    }

    public function render()
    {
        return view('livewire.admin.admissions.new-form', [
            'courses' => Course::orderBy('name')->get(),
            'batches' => $this->course_id 
                ? Batch::where('course_id', $this->course_id)->with('course')->latest()->get()
                : Batch::with('course')->latest()->get(),
            'progress' => match ($this->step) {
                1 => 50, 
                2 => 100,
                default => 0,
            },
        ]);
    }
}
