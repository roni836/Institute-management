<?php
namespace App\Livewire\Admin\Teachers;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    #[Url( as : 'q')]
    public string $search = '';

    public int $perPage = 15;

    public function updatingSearch()
    {$this->resetPage();}
    public function updatingPerPage()
    {$this->resetPage();}

    public function render()
    {
        $teachers = User::query()
            ->where('role', 'teacher')
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        // Add statistics
        $totalTeachers = User::where('role', 'teacher')->count();
        $activeTeachers = User::where('role', 'teacher')->where('status', 'active')->count();
        $coursesTaught = 18; // Replace with actual count from course_teacher pivot
        $studentsTaught = 1247; // Replace with actual count from relationships

        return view('livewire.admin.teachers.index', compact(
            'teachers',
            'totalTeachers',
            'activeTeachers',
            'coursesTaught',
            'studentsTaught'
        ));
    }
}
