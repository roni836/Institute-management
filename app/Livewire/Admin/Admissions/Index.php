<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Batch;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $q       = '';
    public ?string $status = null;
    public ?int $batchId   = null;

    protected $queryString = ['q', 'status', 'batchId', 'page'];

    public function updating($name, $value)
    {
        if (in_array($name, ['q', 'status', 'batchId'], true)) {
            $this->resetPage();
        }
    }

    public function delete(int $id)
    {
        Student::findOrFail($id)->delete();
        session()->flash('ok', 'Student deleted.');
    }

    public function render()
    {
        $students = Student::query()
            ->when($this->q, function ($q) {
                $q->where(function ($qq) {
                    $term = "%{$this->q}%";
                    // Use 'name' (you don't have first_name/last_name in your schema)
                    $qq->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('roll_no', 'like', $term)
                        ->orWhere('student_uid', 'like', $term);
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->batchId, function ($q) {
                $q->whereHas('admissions', fn($a) => $a->where('batch_id', $this->batchId));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.admissions.index', [
            'students' => $students,
            'batches'  => Batch::with('course')->latest()->take(100)->get(),
        ]);
    }
}
