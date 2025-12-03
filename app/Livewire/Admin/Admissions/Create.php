<?php
namespace App\Livewire\Admin\Admissions;

use App\Mail\AdmissionConfirmationMail;
use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
                          // Stepper
    public int $step = 1; // 1: Student, 2: Admission, 3: Plan & Review

    // Student fields (new student at admission time)
    public $name, $father_name, $mother_name, $email, $phone, $address, $admission;
    public $gender, $category, $alt_phone, $dob, $session, $mother_occupation, $father_occupation;
    public $school_name, $school_address, $board, $class;
    public $stream;
    public string $student_status = 'active';

    // Admission fields
    public $batch_id, $admission_date, $discount = 0.00, $mode = 'full';
    public $fee_total                            = 0.00, $installments                            = 2, $plan                            = [];
    public $custom_installments                  = false; // Flag to track if user has customized installments
    public $applyGst                             = false; // Flag to track if GST should be applied
    public $gstAmount                            = 0.00;  // Amount of GST
    public $gstRate                              = 18.00; // GST rate in percentage
    public $flexiblePayment                      = false; // Flag to track if flexible payment is enabled
    public $flexibleAmount                       = 0.00;  // Custom amount for flexible payment

    public ?string $searchPhone    = '';
    public bool $isExistingStudent = false;
    public $foundStudents = [];
    public $selectedStudentId = null;
    public bool $showStudentSelection = false;

    public function mount()
    {
        $this->admission_date = now()->toDateString();
        $this->applyGst       = false;
        $this->recalculate();
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['batch_id', 'discount', 'mode', 'installments', 'admission_date', 'applyGst', 'gstRate', 'flexiblePayment', 'flexibleAmount'], true)) {
            $this->recalculate();
        }

        // Check for duplicate admission when batch is selected
        // if ($name === 'batch_id' && $value && $this->phone) {
        //     $this->checkDuplicateAdmission();
        // }

        // Reset custom installments flag when mode changes
        if ($name === 'mode') {
            $this->custom_installments = false;
        }

        // Reset custom installments flag when number of installments changes
        if ($name === 'installments') {
            $this->custom_installments = false;
        }
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
        $student = Student::find($studentId);
        if ($student) {
            $this->selectedStudentId = $studentId;
            $this->showStudentSelection = false;
            
            // Pre-fill form with selected student data
            $this->name              = $student->name;
            $this->email             = $student->email;
            $this->phone             = $student->phone;
            $this->father_name       = $student->father_name;
            $this->mother_name       = $student->mother_name;
            $this->address           = $student->address;
            $this->dob               = $student->dob;
            $this->session           = $student->session;
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
        $this->reset(['name', 'email', 'father_name', 'mother_name', 'address', 'gender', 'category', 'dob', 'session', 'alt_phone', 'mother_occupation', 'father_occupation', 'school_name', 'school_address', 'board', 'class', 'stream']);
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
            'name'              => ['required', 'string', 'max:255'],
            'father_name'       => ['nullable', 'string', 'max:255'],
            'mother_name'       => ['nullable', 'string', 'max:255'],
            'email'             => ['nullable', 'email:unique:students,email', 'max:255'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'address'           => ['nullable', 'string'],
            'dob'               => ['nullable'],
            'session'           => ['nullable'],
            'gender'            => ['nullable', 'string', 'in:male,female,others'],
            'category'          => ['nullable', 'string', 'in:sc,st,obc,general,other'],
            'alt_phone'         => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'stream'            => ['required', 'string', 'in:Engineering,Foundation,Medical,Other'],
            'student_status'    => ['nullable', 'in:active,inactive,alumni'],

            // step 2 - Education details
            'school_name'       => ['nullable', 'string', 'max:255'],
            'school_address'    => ['nullable', 'string'],
            'board'             => ['nullable', 'string', 'max:255'],
            'class'             => ['nullable', 'string', 'max:255'],

            // step 3 - Admission details
            'batch_id'          => [
                'required',
                'exists:batches,id',
            ],
            'admission_date'    => ['required', 'date'],
            'discount'          => ['nullable', 'numeric', 'min:0'],
            'mode'              => ['required', 'in:full,installment'],
            'fee_total'         => ['required', 'numeric', 'min:0'],
            'applyGst'          => ['boolean'],
            'gstRate'           => ['required_if:applyGst,true', 'numeric', 'min:0', 'max:100'],
            'gstAmount'         => ['required_if:applyGst,true', 'numeric', 'min:0'],
            'flexiblePayment'   => ['boolean'],
            'flexibleAmount'    => ['required_if:flexiblePayment,true', 'numeric', 'min:0'],
            'installments'      => [
                Rule::requiredIf(fn() => $this->mode === 'installment'),
                'integer', 'min:2',
            ],
            'plan'              => [
                function ($attribute, $value, $fail) {
                    if ($this->mode === 'installment' && ! empty($this->plan)) {
                        $total = array_sum(array_column($this->plan, 'amount'));
                        if (abs($total - $this->fee_total) > 0.01) {
                            $fail('Installment amounts must equal the total fee amount.');
                        }

                        // Validate dates are not in the past
                        // foreach ($this->plan as $installment) {
                        //     if (Carbon::parse($installment['due_on'])->isPast()) {
                        //         $fail('Installment due dates cannot be in the past.');
                        //         break;
                        //     }
                        // }
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
            1       => [
                'name'   => ['required', 'string', 'max:255'],
                'email'  => ['nullable', 'email', 'max:255'],
                'phone'  => ['nullable', 'string', 'max:20'],
                'stream' => ['nullable', 'string', 'in:Engineering,Foundation,Medical,Other'],
            ],
            2       => [
                'school_name'    => ['nullable', 'string', 'max:255'],
                'school_address' => ['nullable', 'string'],
                'board'          => ['nullable', 'string', 'max:255'],
                'class'          => ['nullable', 'string', 'max:255'],
            ],
            3       => [
                'batch_id'       => [
                    'required',
                    'exists:batches,id',
                ],
                'admission_date' => ['required', 'date'],
                'discount'       => ['nullable', 'numeric', 'min:0'],
                'mode'           => ['required', 'in:full,installment'],
                'installments'   => [
                    Rule::requiredIf(fn() => $this->mode === 'installment'),
                    'integer', 'min:2',
                ],
                'fee_total'      => ['required', 'numeric', 'min:0'],
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

    public function recalculate(): void
    {
        $batch     = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->net_fee ?? 0.00;
        $discount  = max(0.00, (float) $this->discount);

        // Use flexible amount if flexible payment is enabled
        if ($this->flexiblePayment) {
            $subtotal = max(0.00, round((float) $this->flexibleAmount, 2));
        } else {
            $subtotal = max(0.00, round(((float) $courseFee) - $discount, 2));
        }

        // Calculate GST if applicable
        if ($this->applyGst) {
            $this->gstAmount = round(($subtotal * $this->gstRate) / 100, 2);
            $total           = $subtotal + $this->gstAmount;
        } else {
            $this->gstAmount = 0.00;
            $total           = $subtotal;
        }

        $this->fee_total = $total;

        // Don't recalculate if user has customized installments
        if ($this->custom_installments && $this->mode === 'installment') {
            return;
        }

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

    /**
     * Generate unique enrollment ID based on stream and class (F25AE260001, E25AE270001, etc.)
     * Format: [Stream Letter][Session Year]AE[Class][Sequence]
     */
    private function generateEnrollmentId(): string
    {
        if (! $this->stream) {
            throw new \Exception('Stream is required to generate enrollment ID');
        }
        
        if (! $this->class) {
            throw new \Exception('Class is required to generate enrollment ID');
        }

        // Get session year start (e.g., "2024-25" -> "24", "2025-26" -> "25")
        $sessionYear = $this->session ? $this->getSessionYearStart($this->session) : date('y');
        
        $streamPrefix = match ($this->stream) {
            'Foundation'  => 'F',
            'Engineering' => 'E',
            'Medical'     => 'M',
            'Other'       => 'O',
            default       => 'O'
        };
        
        // Use class number directly (5, 6, 7, 8, 9, 10, 11, 12)
        $classNumber = $this->class;

        // Build the prefix pattern
        if (strtoupper((string)$this->class) === 'TS') {
            // TS-specific format: TS + [Stream Letter] + [Session YY]
            $pattern = 'TS' . $streamPrefix . $sessionYear;
        } else {
            // Default format: [Stream Letter][Session YY]AE[Class]
            $pattern = $streamPrefix . $sessionYear . 'AE' . $classNumber;
        }
        
        // Find the last enrollment ID for this stream, year, and class
        $lastStudent = Student::where('stream', $this->stream)
            ->where('enrollment_id', 'like', $pattern . '%')
            ->orderBy('enrollment_id', 'desc')
            ->first();

        if ($lastStudent && $lastStudent->enrollment_id) {
            // Extract the sequence number from the last enrollment ID
            $lastNumber = (int) substr($lastStudent->enrollment_id, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $pattern . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
     * Generate unique student UID (STU20250001, STU20250002, ...)
     */
    private function generateStudentUid(): string
    {
        $year = date('Y');

        // Get the last student created this year
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent && $lastStudent->student_uid) {
            // Extract the last 4 digits (serial number part)
            $lastNumber = (int) substr($lastStudent->student_uid, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $thisYear   = date('y');       // e.g., '25'
        $comingYear = (date('y') + 1); // e.g., '26'

        // Build the UID
        $id = $thisYear . $comingYear . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $id;
    }

    /**
     * Generate unique roll number (A0001, A0002, ...)
     */
    private function generateRollNumber(): string
    {
        $lastStudent = Student::orderBy('id', 'desc')->first();

        if ($lastStudent && $lastStudent->roll_no) {
            // Extract the number from the last roll number
            $lastNumber = (int) substr($lastStudent->roll_no, 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'A' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get next roll number for display (without creating)
     */
    public function getNextRollNumber(): string
    {
        return $this->generateRollNumber();
    }

    /**
     * Get next student UID for display (without creating)
     */
    public function getNextStudentUid(): string
    {
        return $this->generateStudentUid();
    }

    /**
     * Get next enrollment ID for display (without creating)
     */
    public function getNextEnrollmentId(): string
    {
        if (! $this->stream) {
            return 'Select stream first';
        }
        if (! $this->class) {
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
            $this->plan[$index]['amount'] = (float) $amount;
            $this->custom_installments    = true;
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
            $this->custom_installments    = true;
        }
    }

    /**
     * Validate that installment amounts match total
     */
    public function validateInstallmentTotal()
    {
        if ($this->mode === 'installment' && ! empty($this->plan)) {
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
                } else {
                    // Create new student
                    $student = Student::create([
                        'name'              => $this->name,
                        'email'             => $this->email,
                        'phone'             => $this->phone,
                        'dob'               => $this->dob,
                        'roll_no'           => $this->generateRollNumber(),
                        'student_uid'       => $this->generateStudentUid(),
                        'enrollment_id'     => $this->generateEnrollmentId(),
                        'father_name'       => $this->father_name,
                        'mother_name'       => $this->mother_name,
                        'address'           => $this->address,
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
                        'admission_date'    => $this->admission_date,
                    ]);
                }

                // Create admission
                $admission = Admission::create([
                    'student_id'     => $student->id,
                    'batch_id'       => $this->batch_id,
                    'admission_date' => $this->admission_date,
                    'mode'           => $this->mode,
                    'discount'       => $this->discount,
                    'fee_total'      => $this->fee_total,
                    'fee_due'        => $this->fee_total,
                    'status'         => 'active',
                    'session'        => $this->session,
                    'is_gst'         => $this->applyGst ?? false,
                ]);

                // 3) Create payment schedule
                if ($this->mode === 'installment') {
                    // Create multiple installments
                    foreach ($this->plan as $p) {
                        $admission->schedules()->create([
                            'installment_no' => $p['no'],
                            'due_date'       => $p['due_on'],
                            'amount'         => $p['amount'],
                            'status'         => 'pending',
                        ]);
                    }
                } else {
                    // Create single payment schedule for full payment
                    $admission->schedules()->create([
                        'installment_no' => 1,
                        'due_date'       => $this->admission_date,
                        'amount'         => $this->fee_total,
                        'status'         => 'pending',
                    ]);
                }
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
        return view('livewire.admin.admissions.create', [
            'batches'  => Batch::with('course')->latest()->get(),
            'progress' => match ($this->step) {
                1 => 25, 2 => 50, 3 => 75,     default => 100,
            },
        ]);
    }
}
