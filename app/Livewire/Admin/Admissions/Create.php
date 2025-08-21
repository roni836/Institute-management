<?php

namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    // Student fields (always new student at admission time)
    public $name, $father_name, $mother_name, $roll_no, $student_uid,
           $email, $phone, $address, $admission;
    public string $student_status = 'active';   // default status

    // Admission fields
    public $batch_id, $admission_date, $discount = 0.00, $mode = 'full';
    public $fee_total = 0.00, $installments = 2, $plan = [];

    public function mount()
    {
        $this->admission_date = now()->toDateString();
        $this->recalculate();
    }

    public function updated($name, $value)
    {
        if (in_array($name, ['batch_id','discount','mode','installments','admission_date'], true)) {
            $this->recalculate();
        }
    }

    public function rules(): array
    {
        return [
            // student fields
            'name'            => 'required|string|max:255',
            'father_name'     => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'roll_no'         => 'required|string|unique:students,roll_no',
            'student_uid'     => 'required|string|unique:students,student_uid',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'student_status'  => 'nullable|in:active,inactive,alumni',

            // admission fields
            'batch_id'        => 'required|exists:batches,id',
            'admission_date'  => 'required|date',
            'discount'        => 'nullable|numeric|min:0',
            'mode'            => 'required|in:full,installment',
            'fee_total'       => 'required|numeric|min:0',
            'installments'    => 'nullable|integer|min:2',
        ];
    }

    public function recalculate(): void
    {
        $batch = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->gross_fee ?? 0.00;
        $discount  = max(0.00, (float)$this->discount);

        $total = max(0.00, round(((float)$courseFee) - $discount, 2));
        $this->fee_total = $total;

        $this->plan = [];
        $n = ($this->mode === 'installment') ? max(2, (int)$this->installments) : 1;

        $anchor = $this->admission_date ? Carbon::parse($this->admission_date) : now();

        if ($n === 1) {
            $this->plan[] = ['no'=>1,'amount'=>$total,'due_on'=>$anchor->toDateString()];
            return;
        }

        $per = floor(($total / $n) * 100) / 100;
        $sum = round($per * $n, 2);
        $rem = round($total - $sum, 2);

        for ($i = 1; $i <= $n; $i++) {
            $amt = $per + ($i === 1 ? $rem : 0.00);
            $due = $anchor->copy()->addMonths($i - 1)->toDateString();
            $this->plan[] = ['no'=>$i,'amount'=>$amt,'due_on'=>$due];
        }
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            // 1) Create student
            $student = Student::create([
                'name'            => $this->name,
                'father_name'     => $this->father_name,
                'mother_name'     => $this->mother_name,
                'roll_no'         => $this->roll_no,
                'student_uid'     => $this->student_uid,
                'email'           => $this->email,
                'phone'           => $this->phone,
                'address'         => $this->address,
                'status'          => $this->student_status,
                'admission_date'  => $this->admission_date,
            ]);

            // 2) Create admission
            $admission = Admission::create([
                'student_id'     => $student->id,
                'batch_id'       => $this->batch_id,
                'admission_date' => $this->admission_date,
                'discount'       => (float)$this->discount,
                'mode'           => $this->mode,
                'fee_total'      => (float)$this->fee_total,
                'fee_due'        => (float)$this->fee_total,
                'status'         => 'active',
            ]);

            // 3) Create schedule if installments
            if ($this->mode === 'installment') {
                foreach ($this->plan as $p) {
                    $admission->schedules()->create([
                        'installment_no' => $p['no'],
                        'due_date'       => $p['due_on'],
                        'amount'         => $p['amount'],
                        'status'         => 'pending',
                    ]);
                }
            }
        });

        session()->flash('ok', 'Admission created');
        return redirect()->route('admin.admissions.index');
    }

    public function render()
    {
        return view('livewire.admin.admissions.create', [
            'batches' => Batch::with('course')->latest()->get(),
        ]);
    }
}
