<?php

namespace App\Livewire\Admin\Exams\Student;

use App\Models\Exam;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
  

    public function render()
    {
        return view('livewire.admin.exams.student.create');
    }
}
