<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use App\Models\Student;
use App\Models\Guardian;

use Livewire\Attributes\Layout;
#[Layout('components.layouts.admin')]
class Form extends Component
{
     public ?Student $student = null;

    public $first_name, $last_name, $email, $phone, $dob, $gender, $address, $status = 'active';
    public $g_name, $g_relation, $g_phone, $g_email;

    public function mount(?int $studentId = null){
        if($studentId){
            $this->student = Student::with('guardian')->findOrFail($studentId);
            $this->fill($this->student->only(['first_name','last_name','email','phone','dob','gender','address','status']));
            if($g = $this->student->guardian){
                $this->g_name = $g->name; $this->g_relation = $g->relation; $this->g_phone = $g->phone; $this->g_email = $g->email;
            }
        }
    }

    protected function rules(){
        return [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email|unique:students,email,'.($this->student->id ?? 'NULL'),
            'phone'      => 'nullable|string|max:20|unique:students,phone,'.($this->student->id ?? 'NULL'),
            'dob'        => 'nullable|date',
            'gender'     => 'nullable|in:male,female,other',
            'address'    => 'nullable|string|max:1000',
            'status'     => 'required|in:active,inactive,alumni',

            'g_name'     => 'nullable|string|max:120',
            'g_relation' => 'nullable|string|max:50',
            'g_phone'    => 'nullable|string|max:20',
            'g_email'    => 'nullable|email',
        ];
    }

    public function save(){
        $data = $this->validate();

        $student = $this->student
            ? tap($this->student)->update($data)
            : $this->student = Student::create($data);

        // upsert guardian
        if($this->g_name || $this->g_phone || $this->g_email){
            Guardian::updateOrCreate(
                ['student_id' => $student->id],
                ['name'=>$this->g_name,'relation'=>$this->g_relation,'phone'=>$this->g_phone,'email'=>$this->g_email]
            );
        }

        session()->flash('ok', 'Student saved');
        return redirect()->route('admin.students.index');
    }

    public function render(){
        return view('livewire.admin.students.form')->title($this->student ? 'Edit Student' : 'New Student');
    }
}
