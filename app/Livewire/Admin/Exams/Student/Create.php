<?php

namespace App\Livewire\Admin\Exams\Student;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $exam;
    public $examId;
    public $students;
    public $batches;
    public $examSubjects;
    public $selectedBatch = null;
    public $selectedStudent = null;
    public $marks = [];
    public $showForm = false;

  

    public function mount($exam_id){
        $this->exam = Exam::find($exam_id);
        $this->examId = $exam_id;
        //Those students who are already assigned to this exam
        $assignedStudentIds = DB::table('exam_student')
                                ->where('exam_id', $this->examId)
                                ->pluck('student_id');


        //Students who are not assigned to this exam yet
        $this->students = Admission::where('batch_id', $this->exam->batch_id)->whereNotIn('student_id', $assignedStudentIds)->get();
       
    }
  

    public function render()
    {
        return view('livewire.admin.exams.student.create');
    }
}
