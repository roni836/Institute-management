<?php

namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('components.layouts.admin')]
class Cancel extends Component
{
    public $admission;
    public $student;
    public function mount($admission){
        $this->admission = Admission::find($admission);

        // $this->student  = Student::find
        $this->student  = $this->admission->student; //dmiss
        // $this->student_id = $this->admission->student_id;
        // dd($this->student_id);
    }
    public function save(){
        $this->admission->status = 'cancelled';
        $this->admission->save();

        $this->student->status = 'inactive';
        $this->student->save();
        return redirect()->route('admin.admissions.index');
    }
    public function render()
    {
        return view('livewire.admin.admissions.cancel');
    }
}
