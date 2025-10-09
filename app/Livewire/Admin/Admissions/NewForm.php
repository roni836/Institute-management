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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class NewForm extends Component
{
    use WithFileUploads;

    // Stepper
    public int $step = 1; // 1: Student, 2: Admission & Payment

    // Student fields (new student at admission time)
    public $name, $father_name, $mother_name, $email, $phone, $whatsapp_no, $address, $admission;
    public $gender, $category, $alt_phone, $dob, $session, $academic_session, $mother_occupation, $father_occupation;
    public $school_name, $school_address, $board, $class;
    public $stream;
    public string $student_status = 'active';
    public ?string $module1 = null;
    public ?string $module2 = null;
    public ?string $module3 = null;
    public ?string $module4 = null;
    public ?string $module5 = null;
    public bool $id_card_required = false;
    
    // Address fields
    public $address_type = 'permanent';
    public $address_line1, $address_line2, $city, $state, $district, $pincode, $country = 'India';
    public $corr_address_line1, $corr_address_line2, $corr_city, $corr_state, $corr_district, $corr_pincode, $corr_country = 'India';
    public $same_as_permanent = false;

    // Course and Admission fields
    public $course_id = null;
    public $selected_course = null;
    public $selected_batch = null;
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
    public $photo_upload;
    public $aadhaar_upload;
    public ?string $existing_photo = null;
    public ?string $existing_aadhaar = null;

    public function mount()
    {
        $this->admission_date = now()->toDateString();
        $this->applyGst = false;
        $this->recalculate();
        $this->school_name = '';
        $this->school_address = '';
        $this->board = '';
        $this->photo_upload = null;
        $this->aadhaar_upload = null;
        $this->module1 = null;
        $this->module2 = null;
        $this->module3 = null;
        $this->module4 = null;
        $this->module5 = null;
        $this->id_card_required = false;
        $this->selected_batch = null;

        if ($this->batch_id) {
            $this->loadBatchData($this->batch_id);
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
        $this->selected_batch = null;
    }

    /**
     * Load batch details for summary display
     */
    public function loadBatchData($batchId): void
    {
        $this->selected_batch = $batchId ? Batch::find($batchId) : null;
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
            $this->school_name = $student->school_name;
            $this->school_address = $student->school_address;
            $this->board = $student->board;
            $this->gender = $student->gender;
            $this->category = $student->category;
            $this->alt_phone = $student->alt_phone;
            $this->mother_occupation = $student->mother_occupation;
            $this->father_occupation = $student->father_occupation;
            $this->stream = $student->stream;
            $this->class = $student->class;
            $this->student_status = $student->status;
            $this->existing_photo = $student->photo;
            $this->existing_aadhaar = $student->aadhaar_document_path;
            $this->photo_upload = null;
            $this->aadhaar_upload = null;
            
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
            'corr_city', 'corr_state', 'corr_district', 'corr_pincode', 'corr_country', 'same_as_permanent',
            'school_name', 'school_address', 'board', 'class',
            'module1', 'module2', 'module3', 'module4', 'module5', 'id_card_required'
        ]);
        $this->photo_upload = null;
        $this->aadhaar_upload = null;
        $this->existing_photo = null;
        $this->existing_aadhaar = null;
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
            'school_name' => ['nullable', 'string', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:255'],
            'board' => ['nullable', 'string', 'max:100'],
            'class' => ['nullable', 'string', 'max:255'],
            'module1' => ['nullable', 'string', 'max:255'],
            'module2' => ['nullable', 'string', 'max:255'],
            'module3' => ['nullable', 'string', 'max:255'],
            'module4' => ['nullable', 'string', 'max:255'],
            'module5' => ['nullable', 'string', 'max:255'],
            'id_card_required' => ['boolean'],
            
            // Address fields
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'pincode' => ['required', 'digits:6'],
            'country' => ['nullable', 'string', 'max:100'],
            'same_as_permanent' => ['boolean'],
            'photo_upload' => ['nullable', 'image', 'max:2048'],
            'aadhaar_upload' => ['nullable', 'mimes:jpeg,jpg,png,pdf', 'max:4096'],
            'corr_address_line1' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'string', 'max:255'],
            'corr_address_line2' => ['nullable', 'string', 'max:255'],
            'corr_city' => ['nullable', 'string', 'max:100'],
            'corr_state' => ['nullable', 'string', 'max:100'],
            'corr_district' => ['nullable', 'string', 'max:100'],
            'corr_pincode' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'digits:6'],
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
            'gstRate' => [Rule::requiredIf(fn() => $this->applyGst), 'numeric', 'min:0', 'max:100'],
            'gstAmount' => [Rule::requiredIf(fn() => $this->applyGst), 'numeric', 'min:0'],
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

    protected function normalizeModule(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /** Rules per step (for Next buttons) */
    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'school_name' => ['nullable', 'string', 'max:255'],
                'school_address' => ['nullable', 'string', 'max:255'],
                'board' => ['nullable', 'string', 'max:100'],
                'class' => ['nullable', 'string', 'max:255'],
                'module1' => ['nullable', 'string', 'max:255'],
                'module2' => ['nullable', 'string', 'max:255'],
                'module3' => ['nullable', 'string', 'max:255'],
                'module4' => ['nullable', 'string', 'max:255'],
                'module5' => ['nullable', 'string', 'max:255'],
                'id_card_required' => ['boolean'],
                'photo_upload' => ['nullable', 'image', 'max:2048'],
                'aadhaar_upload' => ['nullable', 'mimes:jpeg,jpg,png,pdf', 'max:4096'],
                'pincode' => ['required', 'digits:6'],
                'corr_pincode' => [Rule::requiredIf(fn() => !$this->same_as_permanent), 'nullable', 'digits:6'],
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
        try {
            $this->validate($this->stepRules($this->step));
        } catch (ValidationException $e) {
            // bail out so we stay on the current step
            throw $e;
        }

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
        $pattern = $streamPrefix . $sessionYear . 'AE' . $classNumber;
        
        // Find the last enrollment ID for this stream, year, and class
        $lastStudent = Student::where('stream', $this->stream)
            ->where('enrollment_id', 'like', $pattern . '%')
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
     * Get class information for enrollment ID generation
     */
    private function getClassForEnrollment(): ?string
    {
        // First priority: use the class field directly
        if ($this->class) {
            return $this->class;
        }
        
        // Try to get class from batch information
        if ($this->selected_batch && isset($this->selected_batch->class)) {
            return $this->selected_batch->class;
        }
        
        // Try to get class from course information
        if ($this->selected_course && isset($this->selected_course->class)) {
            return $this->selected_course->class;
        }
        
        // Default class mapping based on stream
        return match ($this->stream) {
            'Foundation' => '6',
            'Engineering' => '11', 
            'Medical' => '11',
            'Other' => '10',
            default => '10'
        };
    }
    
    /**
     * Extract session year start from session string (e.g., "2024-25" -> "24", "2025-26" -> "25")
     */
    private function getSessionYearStart(string $session): string
    {
        // Extract the starting year from session format "YYYY-YY"
        if (preg_match('/^(\d{4})-\d{2}$/', $session, $matches)) {
            return substr($matches[1], -2); // Get last 2 digits of the year
        }
        
        // Fallback to current year if session format is invalid
        return date('y');
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
        if (!$this->class) {
            return 'Select class first';
        }
        try {
            return $this->generateEnrollmentId();
        } catch (\Exception $e) {
            return 'Select stream and class first';
        }
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

    public function getExistingPhotoUrlProperty(): ?string
    {
        if (!$this->existing_photo) {
            return null;
        }

        if (Storage::disk('public')->exists($this->existing_photo)) {
            return asset('storage/' . ltrim($this->existing_photo, '/'));
        }

        return null;
    }

    public function getExistingAadhaarUrlProperty(): ?string
    {
        if (!$this->existing_aadhaar) {
            return null;
        }

        if (Storage::disk('public')->exists($this->existing_aadhaar)) {
            return asset('storage/' . ltrim($this->existing_aadhaar, '/'));
        }

        return null;
    }

    public function getExistingAadhaarFilenameProperty(): ?string
    {
        return $this->existing_aadhaar ? basename($this->existing_aadhaar) : null;
    }

    public function save()
    {
        // Ensure correspondence address is populated if same_as_permanent is true
        if ($this->same_as_permanent) {
            $this->copyPermanentToCorrespondence();
        }

        try {
            $data = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for server-side debugging
            Log::warning('Admission save validation failed', [
                'errors' => $e->errors(),
                'input' => request()->all(),
            ]);

            // Dispatch a client event so frontend can show debug info
            try {
                $this->dispatch('validationFailed', errors: $e->errors());
            } catch (\Throwable $ex) {
                Log::debug('Failed to dispatch validationFailed event: ' . $ex->getMessage());
            }

            // Re-throw so Livewire shows validation errors in the UI as well
            throw $e;
        }

        // If validation passes, reach this dd for quick debugging
        // dd("tst");

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
                        'class' => $this->class,
                        'academic_session' => $this->academic_session,
                        'school_name' => $this->school_name,
                        'school_address' => $this->school_address,
                        'board' => $this->board,
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
                        'class' => $this->class,
                        'academic_session' => $this->academic_session,
                        'school_name' => $this->school_name,
                        'school_address' => $this->school_address,
                        'board' => $this->board,
                        'admission_date' => $this->admission_date,
                        'roll_no' => $this->generateRollNumber(),
                        'student_uid' => $this->generateStudentUid(),
                        'enrollment_id' => $enrollmentId,
                        'status' => $this->student_status,
                    ]);
                }

                $studentNeedsSave = false;

                if ($this->photo_upload) {
                    if (!empty($student->photo) && Storage::disk('public')->exists($student->photo)) {
                        Storage::disk('public')->delete($student->photo);
                    }
                    $photoPath = $this->photo_upload->store('students/photos', 'public');
                    $student->photo = $photoPath;
                    $this->existing_photo = $photoPath;
                    $this->photo_upload = null;
                    $studentNeedsSave = true;
                } else {
                    $this->existing_photo = $student->photo;
                }

                if ($this->aadhaar_upload) {
                    if (!empty($student->aadhaar_document_path) && Storage::disk('public')->exists($student->aadhaar_document_path)) {
                        Storage::disk('public')->delete($student->aadhaar_document_path);
                    }
                    $aadhaarPath = $this->aadhaar_upload->store('students/aadhaar', 'public');
                    $student->aadhaar_document_path = $aadhaarPath;
                    $this->existing_aadhaar = $aadhaarPath;
                    $this->aadhaar_upload = null;
                    $studentNeedsSave = true;
                } else {
                    $this->existing_aadhaar = $student->aadhaar_document_path;
                }

                if ($studentNeedsSave) {
                    $student->save();
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
                    'discount_type' => $this->discount_type,
                    'discount_value' => $this->discount_value,
                    'fee_total' => $this->fee_total,
                    'fee_due' => $this->fee_total,
                    'status' => 'active',
                    'session' => $this->session ?? date('Y'), // Default to current year if not provided
                    'is_gst' => $this->applyGst ?? false,
                    'gst_amount' => $this->gstAmount ?? 0,
                    'gst_rate' => $this->gstRate ?? 0,
                    'stream' => $this->stream, 
                    // Add student's stream to admission record
                    'module1' => $this->normalizeModule($this->module1),
                    'module2' => $this->normalizeModule($this->module2),
                    'module3' => $this->normalizeModule($this->module3),
                    'module4' => $this->normalizeModule($this->module4),
                    'module5' => $this->normalizeModule($this->module5),
                    'id_card_required' => (bool) $this->id_card_required,
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
