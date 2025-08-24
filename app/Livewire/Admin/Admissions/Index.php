<?php
namespace App\Livewire\Admin\Admissions;

use App\Models\Admission;
use App\Models\Batch;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public ?string $status = null;
    public ?int $batchId = null;

    protected $queryString = ['q', 'status', 'batchId', 'page'];

    public function updating($name)
    {
        if (in_array($name, ['q', 'status', 'batchId'])) {
            $this->resetPage();
        }
    }

    public function delete(int $id)
    {
        Admission::findOrFail($id)->delete();
        session()->flash('ok', 'Admission deleted.');
    }

    public function render()
    {
        $admissions = Admission::with(['student', 'batch.course'])
            ->when($this->q, fn($q) => $q->where(function($qq) {
                $term = "%{$this->q}%";
                $qq->whereHas('student', fn($s) => 
                    $s->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                );
            }))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->batchId, fn($q) => $q->where('batch_id', $this->batchId))
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Admission::count(),
            'active' => Admission::where('status', 'active')->count(),
            'completed' => Admission::where('status', 'completed')->count(),
            'cancelled' => Admission::where('status', 'cancelled')->count(),
            'pendingPayments' => Admission::where('fee_due', '>', 0)->count(),
        ];

        return view('livewire.admin.admissions.index', [
            'admissions' => $admissions,
            'batches' => Batch::orderBy('batch_name')->get(),
            'stats' => $stats
        ]);
    }
}