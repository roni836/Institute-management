<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class Show extends Component
{
    public $exam;
    public $students;
   
    public function mount($examid){
        $this->exam = Exam::with('batch')->findOrFail($examid);
        $this->students = $this->exam->students;
    
    }
    public function render()
    {
        return view('livewire.admin.exams.show');
    }
}
