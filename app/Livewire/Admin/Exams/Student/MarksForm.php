<?php

namespace App\Livewire\Admin\Exams\Student;

use App\Models\ExamSubject;
use App\Models\Mark;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('components.layouts.admin')]

class MarksForm extends Component
{
    public $subjects;
    public $marks = [];
    public $student_id;
    public function mount($exam_id, $student_id){
        // dd($exam_id, $student_id);
        $this->subjects = ExamSubject::where('exam_id', $exam_id)->get();
        // dd($this->subjects);
    }

    public function saveMarks()
    {
        foreach ($this->marks as $examSubjectId => $enteredMarks) {
            // Validation (optional, but recommended)
            if ($enteredMarks < 0) {
                $this->addError('marks.' . $examSubjectId, 'Marks cannot be negative');
                continue;
            }
    
            $examSubject = \App\Models\ExamSubject::find($examSubjectId);
            if ($examSubject && $enteredMarks > $examSubject->max_marks) {
                $this->addError('marks.' . $examSubjectId, 'Marks cannot exceed ' . $examSubject->max_marks);
                continue;
            }
    
            // Save or update marks
            Mark::updateOrCreate(
                [
                    'student_id' => $this->student_id,
                    'exam_subject_id' => $examSubjectId,
                ],
                [
                    'marks_obtained' => $enteredMarks,
                ]
            );
        }
    
        session()->flash('message', 'Marks saved successfully.');
    }
        public function render()
    {
        return view('livewire.admin.exams.student.marks-form');
    }
}
