<?php

namespace App\Livewire\Admin\Students;

use App\Models\Batch;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.admin')]

class StudentProfile extends Component
{
    public $student;
    public $batch;
    public $payments;
    public $performance;
    public function mount($id)
    {
        $this->student = Student::with([
            'admissions'
        ])->findOrFail($id);
        $this->batch = Batch::whereIn('id', $this->student->admissions->pluck('batch_id'))->first();
        $this->payments = Transaction::where('admission_id', $this->student->admissions->first()->id)->get();

        $this->performance = Mark::with(['examSubject.subject', 'examSubject.exam'])
        ->where('student_id', $this->student->id)
        ->get()
        ->map(function ($mark) {
            return [
                'subject' => $mark->examSubject->subject->name,
                'exam'    => $mark->examSubject->exam->name,
                'date'    => $mark->examSubject->exam->exam_date,
                'score'   => $mark->marks_obtained,
                'grade'   => $this->getGrade($mark->marks_obtained, $mark->examSubject->max_marks),
            ];
        });
    }

    private function getGrade($score, $max)
    {
        $percentage = ($score / $max) * 100;

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            default           => 'F',
        };
    }
    public function render()
    {
        return view('livewire.admin.students.student-profile');
    }
}
