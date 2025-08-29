<?php
namespace App\Livewire\Admin\Admissions;

use Maatwebsite\Excel\Facades\Excel;
use App\Excel\AdmissionsExport;
use App\Excel\AdmissionsImport;
use Livewire\WithFileUploads; 
use App\Models\Admission;
use App\Models\Batch;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $q = '';
    public ?string $status = null;
    public ?int $batchId = null;
    public $importFile;

    protected $queryString = ['q', 'status', 'batchId', 'page'];

     public function export()
    {
        $name = 'admissions_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AdmissionsExport, $name);
    }

    public function import()
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx,csv,xls', 'max:10240'],
        ]);

        Excel::import(new AdmissionsImport, $this->importFile->getRealPath());

        $this->reset('importFile');
        session()->flash('ok', 'Admissions imported successfully.');
    }

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

    private function getAdmissionsQuery()
    {
        return Admission::query()
            ->with(['student', 'batch.course'])
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
            ->latest();
    }

    private function getStats()
    {
        // Cache stats for 5 minutes to reduce database queries
        return Cache::remember('admission_stats', 300, function () {
            return [
                'total' => Admission::count(),
                'active' => Admission::where('status', 'active')->count(),
                'completed' => Admission::where('status', 'completed')->count(),
                'cancelled' => Admission::where('status', 'cancelled')->count(),
                'pendingPayments' => Admission::where('fee_due', '>', 0)->count(),
            ];
        });
    }

    private function getBatches()
    {
        // Cache batches for 1 hour as they likely change infrequently
        return Cache::remember('batches', 3600, function () {
            return Batch::orderBy('batch_name')->get();
        });
    }

    public function render()
    {
        return view('livewire.admin.admissions.index', [
            'admissions' => $this->getAdmissionsQuery()->paginate(10),
            'batches' => $this->getBatches(),
            'stats' => $this->getStats(),
        ]);
    }
}