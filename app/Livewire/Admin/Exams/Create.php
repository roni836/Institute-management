<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Batch;
use App\Models\Exam;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $batch_id;
    public $name;
    public $exam_date;

    protected $rules = [
        'batch_id' => 'required|exists:batches,id',
        'name' => 'required|string|max:255',
        'exam_date' => 'nullable|date',
    ];

    public function save()
    {
        $this->validate();

        Exam::create([
            'batch_id' => $this->batch_id,
            'name' => $this->name,
            'exam_date' => $this->exam_date,
        ]);

        session()->flash('message', 'Exam created successfully!');
        return redirect()->route('admin.exams.index');
    }
    public function render()
    {
        $batches = Batch::all();
        return view('livewire.admin.exams.create',compact('batches'));
    }
}
