<?php

namespace App\Livewire\Admin\Batches;

use App\Models\Batch;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $course_id, $batch_name, $start_date, $end_date;

    public function rules()
    {
        return [
            'course_id'  => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function save()
    {
        $data = $this->validate();
        Batch::create($data);

        session()->flash('ok', 'Batch created.');
        return redirect()->route('admin.batches.index');
    }

    public function render()
    {
        return view('livewire.admin.batches.create', [
            'courses' => Course::orderBy('name')->get(),
        ]);
    }
}
