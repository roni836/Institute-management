<?php

namespace App\Livewire\Admin\Batches;

use App\Models\Batch;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public ?string $courseFilter = null;
    public ?string $statusFilter = null;

    protected $queryString = ['q', 'courseFilter', 'statusFilter', 'page'];

    public function updating($name)
    {
        if (in_array($name, ['q', 'courseFilter', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    public function delete(int $id)
    {
        Batch::findOrFail($id)->delete();
        session()->flash('ok', 'Batch deleted.');
    }

    public function render()
    {
        $batches = Batch::with('course')
            ->when($this->q, fn($q) => $q->where(function($qq){
                $term = "%{$this->q}%";
                $qq->where('batch_name','like',$term)
                   ->orWhereHas('course', fn($c) => $c->where('name','like',$term));
            }))
            ->when($this->courseFilter, fn($q) => $q->where('course_id', $this->courseFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        // Add batch statistics
        $totalBatches = Batch::count();
        $runningBatches = Batch::whereNotNull('start_date')
            ->where('end_date', '>', now())
            ->count();
        $upcomingBatches = Batch::where('start_date', '>', now())->count();
        $completedBatches = Batch::where('end_date', '<', now())->count();

        return view('livewire.admin.batches.index', [
            'batches' => $batches,
            'courses' => Course::orderBy('name')->get(),
            'totalBatches' => $totalBatches,
            'runningBatches' => $runningBatches,
            'upcomingBatches' => $upcomingBatches,
            'completedBatches' => $completedBatches
        ]);
    }
}
