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
   
    public function mount($examid)
    {
        $this->exam = \App\Models\Exam::with('students')->findOrFail($examid);

        $this->students = Mark::with(['student', 'examSubject.subject'])
            ->whereHas('examSubject', function($q) use ($examid) {
                $q->where('exam_id', $examid);
            })
            ->get();

        // dd($this->students);
    }
    public function render()
    {
        return view('livewire.admin.exams.show');
    }
}
