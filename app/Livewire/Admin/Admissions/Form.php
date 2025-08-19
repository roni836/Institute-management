<?php

namespace App\Livewire\Admin\Admissions;

use Livewire\Component;
use App\Models\Admission;
use App\Models\Student;
use App\Models\Batch;

use Livewire\Attributes\Layout;
#[Layout('components.layouts.admin')]
class Form extends Component
{
   public ?Admission $admission = null;

    public $student_id, $batch_id, $admission_date, $discount = 0, $mode = 'full';
    public $fee_total = 0;     // computed from course fee - discount
    public $installments = 1;  // when mode=installment
    public array $plan = [];   // [ ['amount'=>...,'due_on'=>...], ... ]

    public function mount(?int $admissionId = null){
        $this->admission_date = now()->toDateString();
        if($admissionId){
            $this->admission = Admission::with('batch.course','payments')->findOrFail($admissionId);
            $this->student_id = $this->admission->student_id;
            $this->batch_id   = $this->admission->batch_id;
            $this->discount   = $this->admission->discount;
            $this->mode       = $this->admission->mode;
            $this->fee_total  = $this->admission->fee_total;
        }
        $this->recalculate();
    }

    public function updated($name, $value){ if(in_array($name, ['batch_id','discount','mode','installments'])) $this->recalculate(); }

    public function recalculate(){
        $batch = $this->batch_id ? Batch::with('course')->find($this->batch_id) : null;
        $courseFee = $batch?->course?->fee ?? 0;
        $discount  = max(0, (int)$this->discount);
        $total     = max(0, $courseFee - $discount);
        $this->fee_total = $total;

        // build simple plan (equal parts)
        $this->plan = [];
        $n = ($this->mode === 'installment') ? max(2, (int)$this->installments) : 1;
        $base = intdiv($total, $n);
        $rem  = $total - ($base * $n);
        for($i=1; $i<=$n; $i++){
            $amount = $base + ($i === 1 ? $rem : 0); // put remainder into first installment
            $due = now()->addMonths($i-1)->toDateString();
            $this->plan[] = ['no'=>$i, 'amount'=>$amount, 'due_on'=>$due];
        }
    }

    protected function rules(){
        return [
            'student_id'      => 'required|exists:students,id',
            'batch_id'        => 'required|exists:batches,id',
            'admission_date'  => 'required|date',
            'discount'        => 'nullable|integer|min:0',
            'mode'            => 'required|in:full,installment',
            'fee_total'       => 'required|integer|min:0',
        ];
    }

    public function save(){
        $data = $this->validate();
        $data['fee_due'] = $this->fee_total;

        $admission = $this->admission
            ? tap($this->admission)->update($data)
            : Admission::create($data);

        // create payment schedule if installment
        if($this->mode === 'installment'){
            // keep existing paid ones; recreate future unpaid schedule
            $admission->payments()->whereNull('paid_on')->delete();
            foreach($this->plan as $p){
                $admission->payments()->create([
                    'amount' => $p['amount'],
                    'paid_on'=> null,
                    'method' => null,
                    'reference' => null,
                    'installment_no' => $p['no'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // for full payment, no schedule entries unless you record instantly on Payments page
            $admission->payments()->whereNull('paid_on')->delete();
        }

        session()->flash('ok','Admission saved');
        return redirect()->route('admin.admissions.index');
    }

    public function render(){
        return view('livewire.admin.admissions.form', [
            'students' => Student::orderBy('first_name')->get(),
            'batches'  => \App\Models\Batch::with('course')->latest()->get(),
        ])->title($this->admission ? 'Edit Admission' : 'New Admission');
    }
}
