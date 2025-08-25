<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Batch;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $batch_id; 
    public $name;
    public $exam_date;
    public $subjects = [];
    public $selectedSubjects = [];

    protected $rules = [
        'batch_id' => 'required|exists:batches,id',
        'name' => 'required|string|max:255',
        'exam_date' => 'nullable|date',
        // 'subject_ids' => 'required|array|min:1',

    ];
    public function mount(){
        $this->subjects = Subject::all();
    }

    public function save()
    {
        $this->validate();

        Exam::create([
            'batch_id' => $this->batch_id,
            'name' => $this->name,
            'exam_date' => $this->exam_date,

        ]);

        $validSubjects = collect($this->selectedSubjects)
        ->filter(fn($s) => !empty($s['checked']) && !empty($s['max_marks']));

        if ($validSubjects->isEmpty()) {
            $this->addError('selectedSubjects', 'Please select at least one subject with max marks.');
            return;
        }

        // create exam
        $exam = Exam::create([
            'batch_id'  => $this->batch_id,
            'name'      => $this->name,
            'exam_date' => $this->exam_date,
        ]);

        // insert into exam_subjects table
        foreach ($validSubjects as $subjectId => $data) {
            DB::table('exam_subjects')->insert([
                'exam_id'    => $exam->id,
                'subject_id' => $subjectId,
                'max_marks'  => $data['max_marks'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        session()->flash('message', 'Exam created successfully!');
        return redirect()->route('admin.exams.index');
    }
    public function render()
    {
        $batches = Batch::all();
        return view('livewire.admin.exams.create',compact('batches'));
    }
}
