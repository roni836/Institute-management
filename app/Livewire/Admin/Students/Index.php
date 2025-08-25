<?php
namespace App\Livewire\Admin\Students;

use App\Models\Student;
use App\Models\Batch;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    #[Validate('string')]
    public string $q = '';

    #[Validate('nullable|string|in:active,completed')]
    public ?string $status = null;

    #[Validate('nullable|integer|exists:batches,id')]
    public ?int $batchId = null;

    #[Validate('integer|min:5|max:50')]
    public int $perPage = 10;

    protected $queryString = ['q', 'status', 'batchId', 'perPage', 'page'];

    public function updating($name)
    {
        if (in_array($name, ['q', 'status', 'batchId', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function delete(int $id)
    {
        Student::findOrFail($id)->delete();
        Cache::forget('student_stats'); // Clear stats cache on delete
        session()->flash('ok', 'Student deleted.');
    }

    private function getStudentsQuery()
    {
        return Student::query()
            ->with('admissions.batch') // Eager-load to avoid N+1 issues
            ->when($this->q, fn($q) => $q->where(function($qq) {
                $term = "%{$this->q}%";
                $qq->where('first_name', 'like', $term)
                   ->orWhere('last_name', 'like', $term)
                   ->orWhere('email', 'like', $term)
                   ->orWhere('phone', 'like', $term);
            }))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->batchId, fn($q) => $q->whereHas('admissions', fn($a) => 
                $a->where('batch_id', $this->batchId)
            ))
            ->latest();
    }

    private function getStats()
    {
        return Cache::remember('student_stats', 300, function () {
            $counts = Student::selectRaw("
                count(*) as total,
                count(case when status = 'active' then 1 end) as active,
                count(case when status = 'completed' then 1 end) as completed,
                count(case when MONTH(created_at) = ? then 1 end) as this_month
            ", [now()->month])->first();

            return [
                'total' => $counts->total,
                'active' => $counts->active,
                'completed' => $counts->completed,
                'thisMonth' => $counts->this_month,
            ];
        });
    }

    private function getBatches()
    {
        // Cache batches for 1 hour
        return Cache::remember('batches', 3600, function () {
            return Batch::orderBy('batch_name')->get();
        });
    }

    public function render()
    {
        return view('livewire.admin.students.index', [
            'students' => $this->getStudentsQuery()->paginate($this->perPage),
            'batches' => $this->getBatches(),
            'stats' => $this->getStats(),
        ]);
    }
}