<?php

namespace App\Livewire\Admin\Exams;

use App\Models\Batch;
use App\Models\Exam;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;


#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $batch_id = '';
    public $perPage = 10;

    protected $queryString = ['search', 'batch_id', 'perPage'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingBatchId() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $exams = Exam::query()
            ->with('batch')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhereHas('batch', fn($b) => $b->where('batch_name', 'like', "%{$this->search}%"));
            })
            ->when($this->batch_id, fn($q) => $q->where('batch_id', $this->batch_id))
            ->orderByDesc('exam_date')
            ->paginate($this->perPage);

        return view('livewire.admin.exams.index', [
            'exams' => $exams,
            'batches' => Batch::orderBy('batch_name')->get(),
        ]);
    }
    
}
