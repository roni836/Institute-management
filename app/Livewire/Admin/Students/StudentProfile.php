<?php

namespace App\Livewire\Admin\Students;

use App\Models\Batch;
use App\Models\Student;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class StudentProfile extends Component
{
    public $student;
    public $batch;
    public $payments;
    public function mount($id)
    {
        $this->student = Student::with([
            'admissions'
        ])->findOrFail($id);
        $this->batch = Batch::whereIn('id', $this->student->admissions->pluck('batch_id'))->first();
        $this->payments = Transaction::where('admission_id', $this->student->admissions->first()->id)->get();
    }
    public function render()
    {
        return view('livewire.admin.students.student-profile');
    }
}
