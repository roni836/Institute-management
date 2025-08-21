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
            ->where('role', 'teacher') // If using Spatie, see note at bottom
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.teachers.index', compact('teachers'));
    }
}
