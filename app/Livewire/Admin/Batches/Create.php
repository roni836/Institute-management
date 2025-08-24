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
    public $selected_course;

    public function rules()
    {
        return [
            'course_id'  => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        if ($this->course_id) {
            $this->selected_course = Course::find($this->course_id);
            $this->updateEndDate();
        }
    }

    protected function updateEndDate()
    {
        if ($this->start_date && $this->selected_course?->duration_months) {
            $this->end_date = \Carbon\Carbon::parse($this->start_date)
                ->addMonths($this->selected_course->duration_months)
                ->format('Y-m-d');
        }
    }

    public function updatedStartDate($value)
    {
        if ($this->course_id && $value) {
            $course = Course::find($this->course_id);
            if ($course && $course->duration_months) {
                $this->end_date = \Carbon\Carbon::parse($value)
                    ->addMonths($course->duration_months)
                    ->format('Y-m-d');
            }
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
            'statuses' => ['Upcoming', 'Running', 'Completed']
        ]);
    }
}
