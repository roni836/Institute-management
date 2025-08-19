<?php

namespace App\Livewire\Admin\Students;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Batch;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public ?string $status = null;
    public ?int $batchId = null;

    protected $queryString = ['q','status','batchId','page'];

    public function updating($name, $value){ if (in_array($name, ['q','status','batchId'])) $this->resetPage(); }

    public function delete(int $id){
        Student::findOrFail($id)->delete();
        session()->flash('ok','Student deleted.');
    }

    public function render()
    {
        $students = Student::query()
            ->when($this->q, fn($q) =>
                $q->where(function($qq){
                    $qq->where('first_name','like',"%{$this->q}%")
                       ->orWhere('last_name','like',"%{$this->q}%")
                       ->orWhere('email','like',"%{$this->q}%")
                       ->orWhere('phone','like',"%{$this->q}%");
                })
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->batchId, function($q){
                $q->whereHas('admissions', fn($a) => $a->where('batch_id', $this->batchId));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.students.index', [
            'students' => $students,
            'batches'  => Batch::latest()->take(100)->get(),
        ]);
    }

}
