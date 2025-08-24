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
    public $examId;
    public $students;
    public $batches;
    public $examSubjects;
    public $selectedBatch = null;
    public $selectedStudent = null;
    public $marks = [];
    public $showForm = false;

  

    public function mount($exam_id){

        $this->examId = $exam_id;
        //Those students who are already assigned to this exam
        $assignedStudentIds = DB::table('exam_student')
                                ->where('exam_id', $this->examId)
                                ->pluck('student_id');


        //Students who are not assigned to this exam yet
        $this->students = Admission::with('student')
        ->whereNotIn('student_id', $assignedStudentIds)
        ->get();        
        // dd($this->students);
    }
    public function addStudent($student_id){
        DB::table('exam_student')->insert([
            'exam_id'   => $this->examId,
            'student_id'=> $student_id,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
        session()->flash('message', 'Student added successfully.');
    }
  

    public function render()
    {
        return view('livewire.admin.exams.student.create');
    }
}
