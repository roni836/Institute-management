<?php

namespace App\Livewire\Admin\Batches;

use App\Models\Batch;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public $batch;
    public $course_id;
    public $batch_name;
    public $start_date;
    public $end_date;
    public $selected_course;

    public function mount(Batch $batch)
    {
        $this->batch = $batch;
        $this->course_id = $batch->course_id;
        $this->batch_name = $batch->batch_name;
        // Convert dates to string format for the input fields
        $this->start_date = optional($batch->start_date)->format('Y-m-d');
        $this->end_date = optional($batch->end_date)->format('Y-m-d');
        $this->selected_course = Course::find($this->course_id);
    }

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
        
        $this->batch->update([
            'course_id' => $this->course_id,
            'batch_name' => $this->batch_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ]);

        session()->flash('ok', 'Batch updated.');
        return redirect()->route('admin.batches.index');
    }

    public function updatedStartDate($value)
    {
        if ($this->selected_course && $value) {
            $this->end_date = \Carbon\Carbon::parse($value)
                ->addMonths($this->selected_course->duration_months)
                ->format('Y-m-d');
        }
    }

    public function updatedCourseId($value)
    {
        $this->selected_course = Course::find($value);
        if ($this->start_date && $this->selected_course) {
            $this->end_date = \Carbon\Carbon::parse($this->start_date)
                ->addMonths($this->selected_course->duration_months)
                ->format('Y-m-d');
        }
    }

    public function render()
    {
        return view('livewire.admin.batches.edit', [
            'courses' => Course::orderBy('name')->get(),
        ]);
    }
}
