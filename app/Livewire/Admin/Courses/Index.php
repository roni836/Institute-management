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
    public ?string $statusFilter = null;
    public ?string $sortField = null;
    public string $sortDirection = 'asc';

    protected $queryString = ['q', 'statusFilter', 'sortField', 'page'];

    public function updating($name)
    {
        if (in_array($name, ['q', 'statusFilter'])) {
            $this->resetPage();
        }
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
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->sortField, fn($q) => $q->orderBy($this->sortField, $this->sortDirection))
            ->latest()
            ->paginate(10);

        // Add statistics data
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'Active')->count();
        $upcomingCourses = Course::where('status', 'Upcoming')->count();
        $totalEnrolled = Course::sum('students_count') ?? 0;

        return view('livewire.admin.courses.index', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'upcomingCourses' => $upcomingCourses,
            'totalEnrolled' => $totalEnrolled
        ]);
    }
}
