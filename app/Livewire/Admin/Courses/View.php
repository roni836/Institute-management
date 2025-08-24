<?php
namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class View extends Component
{
    public Course $course;

    public function mount($id)
    {
        $this->course = Course::with('batches')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.courses.view');
    }
}
