<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Course $course;

    public function rules()
    {
        return [
            'course.name'            => 'required|string|max:255',
            'course.batch_code'      => 'nullable|string|max:255',
            'course.duration_months' => 'nullable|integer|min:1|max:120',
            'course.gross_fee'       => 'required|numeric|min:0',
            'course.discount'        => 'nullable|numeric|min:0',
        ];
    }

    public function save()
    {
        $this->validate();
        $this->course->save();

        session()->flash('ok', 'Course updated.');
        return redirect()->route('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.edit');
    }
}
