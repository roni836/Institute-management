<?php

namespace App\Livewire\Admin\Batches;

use App\Models\Batch;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Batch $batch;

    public function rules()
    {
        return [
            'batch.course_id'  => 'required|exists:courses,id',
            'batch.batch_name' => 'required|string|max:255',
            'batch.start_date' => 'nullable|date',
            'batch.end_date'   => 'nullable|date|after_or_equal:batch.start_date',
        ];
    }

    public function save()
    {
        $this->validate();
        $this->batch->save();

        session()->flash('ok', 'Batch updated.');
        return redirect()->route('admin.batches.index');
    }

    public function render()
    {
        return view('livewire.admin.batches.edit', [
            'courses' => Course::orderBy('name')->get(),
        ]);
    }
}
