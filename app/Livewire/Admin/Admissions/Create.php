<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;
use App\Mail\AdmissionConfirmationMail;
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
    public string $student_status = 'active';

    // Admission fields
    public $batch_id, $admission_date, $discount = 0.00, $mode = 'full';
    public $fee_total                            = 0.00, $installments                            = 2, $plan                            = [];

    public ?string $searchPhone = '';
    public bool $isExistingStudent = false;

    public function mount()
    {
        $this->admission_date = now()->toDateString();
        $this->recalculate();
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['batch_id', 'discount', 'mode', 'installments', 'admission_date'], true)) {
            $this->recalculate();
        }
        
        // Check for duplicate admission when batch is selected
        if ($name === 'batch_id' && $value && $this->phone) {
            $this->checkDuplicateAdmission();
        }
    }

    public function updatedSearchPhone()
    {
        $student = Student::where('phone', $this->searchPhone)->first();
        if ($student) {
            $this->isExistingStudent = true;
            
            // Pre-fill form with existing student data
            $this->name = $student->name;
            $this->email = $student->email;
            $this->phone = $student->phone;
            $this->father_name = $student->father_name;
            $this->mother_name = $student->mother_name;
            $this->address = $student->address;
            $this->student_status = $student->status;
        } else {
            $this->isExistingStudent = false;
            $this->reset(['name', 'email', 'father_name', 'mother_name', 'address']);
            // Clear any batch-related errors when switching to new student
            $this->resetErrorBag('batch_id');
        }
        
        // Check if student is already admitted to the selected batch
        if ($this->batch_id) {
            $this->checkDuplicateAdmission();
        }
    }

    /**
     * Check if student is already admitted to the selected batch
     */
    public function checkDuplicateAdmission()
    {
        if (!$this->batch_id || !$this->phone) {
            return;
        }

        $student = Student::where('phone', $this->phone)->first();
        if ($student) {
            $existingAdmission = Admission::where('student_id', $student->id)
                ->where('batch_id', $this->batch_id)
                ->where('status', '!=', 'cancelled')
                ->first();
            
            if ($existingAdmission) {
                $this->addError('batch_id', 'This student is already admitted to this batch.');
                return;
            }
        }
        
        // Clear any previous errors
        $this->resetErrorBag('batch_id');
    }

    /** Full rules (used on final save) */
    public function rules(): array
    {
        $rules = [
            // step 1
            'name'           => ['required', 'string', 'max:255'],
            'father_name'    => ['nullable', 'string', 'max:255'],
            'mother_name'    => ['nullable', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],
            'student_status' => ['nullable', 'in:active,inactive,alumni'],

            // step 2
            'batch_id'       => [
                'required', 
                'exists:batches,id',
                function ($attribute, $value, $fail) {
                    if ($this->phone && $value) {
                        $student = Student::where('phone', $this->phone)->first();
                        if ($student) {
                            $existingAdmission = Admission::where('student_id', $student->id)
                                ->where('batch_id', $value)
                                ->where('status', '!=', 'cancelled')
                                ->first();
                            
                            if ($existingAdmission) {
                                $fail('This student is already admitted to this batch.');
                            }
                        }
                    }
                }
            ],
            'admission_date' => ['required', 'date'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'mode'           => ['required', 'in:full,installment'],
            'fee_total'      => ['required', 'numeric', 'min:0'],
            'installments'   => [
                Rule::requiredIf(fn() => $this->mode === 'installment'),
                'integer', 'min:2',
            ],
        ];


        return $rules;
    }

    /** Rules per step (for Next buttons) */
    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'name'        => ['required', 'string', 'max:255'],
                'email'       => ['nullable', 'email', 'max:255'],
                'phone'       => ['nullable', 'string', 'max:20'],
            ],
            2 => [
                'batch_id'       => [
                    'required', 
                    'exists:batches,id',
                    function ($attribute, $value, $fail) {
                        if ($this->phone && $value) {
                            $student = Student::where('phone', $this->phone)->first();
                            if ($student) {
                                $existingAdmission = Admission::where('student_id', $student->id)
                                    ->where('batch_id', $value)
                                    ->where('status', '!=', 'cancelled')
                                    ->first();
                                
                                if ($existingAdmission) {
                                    $fail('This student is already admitted to this batch.');
                                }
                            }
                        }
                    }
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
        $courseFee = $batch?->course?->gross_fee ?? 0.00;
        $discount  = max(0.00, (float) $this->discount);

        $total           = max(0.00, round(((float) $courseFee) - $discount, 2));
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



    /**
     * Generate unique student UID (STU20250001, STU20250002, ...)
     */
    private function generateStudentUid(): string
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStudent && $lastStudent->student_uid) {
            // Extract the number from the last student UID
            $lastNumber = (int) substr($lastStudent->student_uid, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'STU' . $year . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
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

    public function save()
    {
        $data = $this->validate();

        $admission = null;
        
        try {
            DB::transaction(function () use ($data) {
            // Find or create student
            $student = Student::where('phone', $this->phone)->first();

            if (!$student) {
                $student = Student::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'roll_no' => $this->generateRollNumber(),
                    'student_uid' => $this->generateStudentUid(),
                    'father_name' => $this->father_name,
                    'mother_name' => $this->mother_name,
                    'address' => $this->address,
                    'status' => $this->student_status,
                    'admission_date' => $this->admission_date,
                ]);
            }

            // Create admission
            $admission = Admission::create([
                'student_id' => $student->id,
                'batch_id' => $this->batch_id,
                'admission_date' => $this->admission_date,
                'mode' => $this->mode,
                'discount' => $this->discount,
                'fee_total' => $this->fee_total,
                'fee_due' => $this->fee_total,
                'status' => 'active',
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
        
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate admission error
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError('batch_id', 'This student is already admitted to this batch.');
                return;
            }
            throw $e; // Re-throw other database errors
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
                1          => 33, 2 => 66,     default => 100,
            },
        ]);
    }
}
