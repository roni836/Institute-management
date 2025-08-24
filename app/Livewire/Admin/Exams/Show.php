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
    public $students_appeared_in_exam = [];
    public $students_subject_wise = [];
    public function mount($examid){
        //If the exam id exists then load the exam model
        $this->exam = Exam::with('examSubjects')->findOrFail($examid);
        //This will give id of all the subjects associated with the exam
        $exam_subject_ids = $this->exam->examSubjects->pluck('id');
        // dd($exam_subject_ids);
        $total_max_marks = ExamSubject::whereIn('id', $exam_subject_ids)->sum('max_marks');
        // dd($total_max_marks);

        //Get all the students who appeared in the exam along with their total marks and percentage
        $this->students_appeared_in_exam = Mark::whereIn('exam_subject_id', $exam_subject_ids)
        ->with('student')
        ->get()
        ->groupBy('student_id')
        ->map(function ($marks, $student_id) use ($total_max_marks) {
            $total_marks_obtained = $marks->sum('marks_obtained');
            $percentage = ($total_max_marks > 0)
                ? ($total_marks_obtained / $total_max_marks) * 100
                : 0;

            return [
                'student' => $marks->first()->student,
                'total_marks_obtained' => $total_marks_obtained,
                'total_max_marks' => $total_max_marks,
                'percentage' => round($percentage, 2),
            ];
        })
        ->sortByDesc('percentage')
        ->values()
        ->all();
        // dd($this->students_appeared_in_exam);

        $marks_subject_wise = Mark::whereIn('exam_subject_id', $exam_subject_ids)
        ->with(['student', 'examSubject'])
        ->get()
        ->groupBy('student_id');

        $this->students_subject_wise = $marks_subject_wise->map(function ($student_marks) {
            return $student_marks->map(function ($m) {
                return [
                    'subject' => $m->examSubject->subject->name,
                    'marks_obtained' => $m->marks_obtained,
                    'max_marks' => $m->examSubject->max_marks,
                ];
            })->toArray();
        })->toArray();



        // dd($this->exam);
        // dd($examid);
        // $this->exam = $exam->load('examSubjects');
        // // dd($this->exam);
        // $exam_subject_ids = $this->exam->examSubjects->pluck('id');
        // dd($exam_subject_ids);
        // $total_max_marks = ExamSubject::whereIn('id', $exam_subject_ids)->sum('max_marks');

        // $this->students_appeared_in_exam = Mark::whereIn('exam_subject_id', $exam_subject_ids)
        //     ->with('student')
        //     ->get()
        //     ->groupBy('student_id')
        //     ->map(function ($marks, $student_id) use ($total_max_marks) {
        //         $total_marks_obtained = $marks->sum('marks_obtained');
        //         $percentage = ($total_max_marks > 0)
        //             ? ($total_marks_obtained / $total_max_marks) * 100
        //             : 0;

        //         return [
        //             'student' => $marks->first()->student,
        //             'total_marks_obtained' => $total_marks_obtained,
        //             'total_max_marks' => $total_max_marks,
        //             'percentage' => round($percentage, 2),
        //         ];
        //     })
        //     ->sortByDesc('percentage')
        //     ->values()
        //     ->all();

       
    }
    public function render()
    {
        return view('livewire.admin.exams.show');
    }
}
