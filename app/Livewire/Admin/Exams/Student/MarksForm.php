<?php

namespace App\Livewire\Admin\Exams\Student;

use App\Models\ExamSubject;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('components.layouts.admin')]

class MarksForm extends Component
{
    public $subjects;
    public $exam_id;
    public $marks = [];
    public $student_id;
    public function mount($exam_id, $student_id){
        // dd($exam_id, $student_id);
        $this->exam_id = $exam_id;
        $this->student_id = $student_id;
        // $this->subjects = ExamSubject::where('exam_id', $exam_id)->get();

        // dd($this->subjects);



        $this->subjects = ExamSubject::with(['marks' => function($q) use ($student_id) {
            $q->where('student_id', $student_id);
        }])->where('exam_id', $exam_id)->get();
    
        // Pre-fill marks array for binding
        foreach ($this->subjects as $subject) {
            $this->marks[$subject->id] = $subject->marks->first()->marks_obtained ?? null;
        }
    }

    public function saveMarks()
    {
        $rules = [];
        $messages = [];

        foreach ($this->subjects as $subject) {
            $rules["marks.{$subject->id}"] = "required|numeric|min:0|max:{$subject->max_marks}";
            $messages["marks.{$subject->id}.required"] = "Marks are required for {$subject->subject->name}.";
            $messages["marks.{$subject->id}.numeric"]  = "Marks must be a number for {$subject->subject->name}.";
            $messages["marks.{$subject->id}.max"]      = "Marks cannot exceed {$subject->max_marks} in {$subject->subject->name}.";
        }

         $this->validate($rules, $messages);
        foreach ($this->marks as $examSubjectId => $enteredMarks) {
            // // Validation (optional, but recommended)
            // if ($enteredMarks < 0) {
            //     $this->addError('marks.' . $examSubjectId, 'Marks cannot be negative');
            //     continue;
            // }
    
            // $examSubject = \App\Models\ExamSubject::find($examSubjectId);
            // if ($examSubject && $enteredMarks > $examSubject->max_marks) {
            //     $this->addError('marks.' . $examSubjectId, 'Marks cannot exceed ' . $examSubject->max_marks);
            //     continue;
            // }

    
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
        DB::table('exam_student')->updateOrInsert(
            [
                'exam_id' => $this->exam_id,
                'student_id' => $this->student_id,
                'created_at' => now(), // only set when creating a new record
                'updated_at' => now(), // always update timestamp
            ],
            [] // nothing to update, just ensure exists
        );
    
        session()->flash('message', 'Marks saved successfully.');
    }
        public function render()
    {
        return view('livewire.admin.exams.student.marks-form');
    }
}
