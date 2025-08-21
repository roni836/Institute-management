<?php

namespace App\Livewire\Admin\Students;

use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class StudentProfile extends Component
{
    public $student;

    public function mount($id)
    {
        $this->student = Student::with([
           
            'admissions'
        ])->findOrFail($id);
    }
    public function render()
    {
        return view('livewire.admin.students.student-profile');
    }
}
