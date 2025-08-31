<?php

namespace App\Livewire\Admin\Exams\Student;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class MarksDetail extends Component
{
    public $exam_id, $student_id;
    public $subjects = [];

    public function mount($exam_id, $student_id)
    {
        $this->exam_id = $exam_id;
        $this->student_id = $student_id;

        $this->subjects = ExamSubject::with(['marks' => function ($q) use ($student_id) {
            $q->where('student_id', $student_id);
        }])->where('exam_id', $exam_id)->get();
    }
    public function render()
    {
        $student = Student::find($this->student_id);
        $exam    = Exam::find($this->exam_id);
        return view('livewire.admin.exams.student.marks-detail', [
            'student' => $student,
            'exam' => $exam,
            'subjects' => $this->subjects
        ]);
    }
}
