<?php
namespace App\Livewire\Admin\Admissions;

use App\Excel\AdmissionsExport;
use App\Excel\AdmissionsImport;
use App\Models\Admission;
use App\Models\Batch;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $q       = '';
    public ?string $status = null;
    public ?int $batchId   = null;
    public $importFile;

    // NEW: export modal state
    public bool $showExportModal = false;
    public ?string $fromDate     = null; // 'Y-m-d'
    public ?string $toDate       = null; // 'Y-m-d'

    protected $queryString = ['q', 'status', 'batchId', 'page'];

    // Open modal
    public function openExport(): void
    {
        $this->resetValidation();
        $this->showExportModal = true;
    }

    // Close modal
    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function export()
    {
        // Validate date range (both required; from <= to)
        $this->validate([
            'fromDate' => ['required', 'date'],
            'toDate'   => ['required', 'date', 'after_or_equal:fromDate'],
        ]);

        $name = 'admissions_' . now()->format('Ymd_His') . '.xlsx';

        // Pass filters (including current screen filters) into export
        $export = new AdmissionsExport(
            fromDate: $this->fromDate,
            toDate: $this->toDate,
            status: $this->status,
            batchId: $this->batchId,
            q: $this->q,
        );

        // Hide modal before returning response
        $this->showExportModal = false;

        return Excel::download($export, $name);
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
            ->when($this->q, fn($q) => $q->where(function ($qq) {
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
                'total'           => Admission::count(),
                'active'          => Admission::where('status', 'active')->count(),
                'completed'       => Admission::where('status', 'completed')->count(),
                'cancelled'       => Admission::where('status', 'cancelled')->count(),
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
            'batches'    => $this->getBatches(),
            'stats'      => $this->getStats(),
        ]);
    }
}
