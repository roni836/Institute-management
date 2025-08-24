<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $q = '';

    protected $queryString = ['q','page'];

    public function updating($name, $value)
    {
        if ($name === 'q') $this->resetPage();
    }

    public function delete(int $id)
    {
        Course::findOrFail($id)->delete();
        session()->flash('ok', 'Course deleted.');
    }

    public function render()
    {
        $courses = Course::query()
            ->when($this->q, fn($q) => $q->where(function($qq){
                $term = "%{$this->q}%";
                $qq->where('name','like',$term)
                   ->orWhere('batch_code','like',$term);
            }))
            ->latest()
            ->paginate(10);

        // Add statistics data
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'Active')->count();
        $upcomingCourses = Course::where('status', 'Upcoming')->count();
        $totalEnrolled = Course::sum('students_count') ?? 0;

        return view('livewire.admin.courses.index', compact(
            'courses',
            'totalCourses',
            'activeCourses',
            'upcomingCourses',
            'totalEnrolled'
        ));
    }
}
