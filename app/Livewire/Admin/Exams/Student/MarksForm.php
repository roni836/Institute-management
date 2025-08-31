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
    public $correct = [];
    public $wrong = [];
    public $blank = [];
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
            $existingMarks = $subject->marks->first();

            $this->marks[$subject->id]   = $existingMarks->marks_obtained ?? 0;
            $this->correct[$subject->id] = $existingMarks->correct ?? 0;
            $this->wrong[$subject->id]   = $existingMarks->wrong ?? 0;
            $this->blank[$subject->id]   = $existingMarks->blank ?? 0;        }
    }

    // public function updatedCorrect($value , $key){
    //     logger()->info("Updated Correct for Subject ID: {$key}, Value: {$value}");

    //     $this->recalculateMarks($key);
    // }

    // public function updatedWrong($value , $key){
    //     $this->recalculateMarks($key);
    // }
    // public function updatedBlank($value , $key){
    //     $this->recalculateMarks($key);
    // }

    // private function recalculateMarks($examSubjectId)
    // {
    //     $correct = (int)$this->correct[$examSubjectId] ?? 0;
    //     $wrong   = (int)$this->wrong[$examSubjectId] ?? 0;
    //     $blank   = (int)$this->blank[$examSubjectId] ?? 0;

    //     // Apply marking scheme
    //     $this->marks[$examSubjectId] = ($correct *1) + ($wrong*0) + ($blank * 0);
    // }

    public function saveMarks()
    {
        $rules = [];
        $messages = [];
    
        foreach ($this->subjects as $subject) {
            $rules["marks.{$subject->id}"] = "required|numeric|min:0|max:{$subject->max_marks}";
            $rules["correct.{$subject->id}"] = "required|integer|min:0";
            $rules["wrong.{$subject->id}"]   = "required|integer|min:0";
            $rules["blank.{$subject->id}"]   = "required|integer|min:0";

            $messages["marks.{$subject->id}.required"] = "Marks are required for {$subject->subject->name}.";
            $messages["marks.{$subject->id}.numeric"]  = "Marks must be a number for {$subject->subject->name}.";
            $messages["marks.{$subject->id}.max"]      = "Marks cannot exceed {$subject->max_marks} in {$subject->subject->name}.";

            $messages["correct.{$subject->id}.required"] = "Correct count is required for {$subject->subject->name}.";
            $messages["wrong.{$subject->id}.required"]   = "Wrong count is required for {$subject->subject->name}.";
            $messages["blank.{$subject->id}.required"]   = "Blank count is required for {$subject->subject->name}.";
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
                    'correct'        => $this->correct[$examSubjectId] ?? 0,
                    'wrong'          => $this->wrong[$examSubjectId] ?? 0,
                    'blank'          => $this->blank[$examSubjectId] ?? 0,
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
        // return redirect()->back()->with('message', 'Marks saved successfully.');
    
    
    }
        public function render()
    {
        return view('livewire.admin.exams.student.marks-form');
    }
}
