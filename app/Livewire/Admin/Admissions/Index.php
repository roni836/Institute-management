<?php
namespace App\Livewire\Admin\Admissions;

use App\Excel\AdmissionsExport;
use App\Excel\AdmissionsDetailsExport;
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
    public ?string $dateRange    = null; // For predefined date ranges

    protected $queryString = ['q', 'status', 'batchId', 'page'];

    // Open modal
    public function openExport(): void
    {
        $this->resetValidation();
        $this->reset(['fromDate', 'toDate', 'dateRange']);
        $this->showExportModal = true;
    }

    // Close modal
    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    // Handle predefined date range selection
    public function updatedDateRange($value): void
    {
        if ($value) {
            $this->setDateRange($value);
        }
    }

    public function export()
    {
        // Validate date range (both required; from <= to)
        $this->validate([
            'fromDate' => ['required', 'date'],
            'toDate'   => ['required', 'date', 'after_or_equal:fromDate'],
        ]);

        $name = 'admissions_' . now()->format('Ymd_His') . '.xlsx';

        // Query admissions with filters
        $admissions = $this->getAdmissionsQuery()
            ->when($this->fromDate, fn($q) => $q->whereDate('admission_date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('admission_date', '<=', $this->toDate))
            ->get();

        // Company details (customize as needed)
        $company = [
            ['Institute Name', 'Ahantra Edu Ventures Private Limited ((A franchisee of Mentors Eduserv)'],
            ['Address', "Purnea Bihar-854301"],
            ['Phone', '   9155588414, 9798986029'],
            ['Email', 'info@myinstitute.com'],
            [],
        ];

        $header = [
            'S.no', 'Name', 'Mobile', 'Enrollment Id', 'Batch', 'Course', 'Admission Date', 'Fee Total', 'Fee Due', 'Status'
        ];

        $rows = $admissions->map(function($a, $i) {
            return [
                $i+1,
                optional($a->student)->name,
                optional($a->student)->phone,
                optional($a->student)->enrollment_id,
                optional($a->batch)->batch_name,
                optional($a->batch?->course)->name,
                optional($a->admission_date)->format('d-M-Y'),
                $a->fee_total,
                $a->fee_due,
                $a->status,
            ];
        })->toArray();

        $data = array_merge($company, [$header], $rows);

        // Use PhpSpreadsheet to generate Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($data as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($colLetter . ($rowIdx + 1), $value);
            }
        }

        $this->showExportModal = false;

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportDetails()
    {
        $fileName = 'admission_details_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(
            new AdmissionsDetailsExport(
                search: $this->q,
                status: $this->status,
                batchId: $this->batchId,
                fromDate: $this->fromDate,
                toDate: $this->toDate
            ),
            $fileName
        );
    }

    public function exportAllDetails()
    {
        $fileName = 'all_admission_details_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(
            new AdmissionsDetailsExport(
                search: null,
                status: null,
                batchId: null,
                fromDate: null,
                toDate: null
            ),
            $fileName
        );
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

    // Set date range based on predefined options
    private function setDateRange(string $range): void
    {
        $today = now();
        
        switch ($range) {
            case 'last_week':
                $this->fromDate = $today->copy()->subWeek()->startOfWeek()->format('Y-m-d');
                $this->toDate = $today->copy()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
                
            case 'last_month':
                $this->fromDate = $today->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->toDate = $today->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
                
            case 'last_3_months':
                $this->fromDate = $today->copy()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->toDate = $today->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
                
            case 'last_6_months':
                $this->fromDate = $today->copy()->subMonths(6)->startOfMonth()->format('Y-m-d');
                $this->toDate = $today->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
                
            case 'last_year':
                $this->fromDate = $today->copy()->subYear()->startOfYear()->format('Y-m-d');
                $this->toDate = $today->copy()->subYear()->endOfYear()->format('Y-m-d');
                break;
                
            case 'this_month':
                $this->fromDate = $today->copy()->startOfMonth()->format('Y-m-d');
                $this->toDate = $today->copy()->endOfMonth()->format('Y-m-d');
                break;
                
            case 'this_year':
                $this->fromDate = $today->copy()->startOfYear()->format('Y-m-d');
                $this->toDate = $today->copy()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    // Clear date range and reset to null
    public function clearDateRange(): void
    {
        $this->reset(['fromDate', 'toDate', 'dateRange']);
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
