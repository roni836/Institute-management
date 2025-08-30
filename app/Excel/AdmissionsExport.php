<?php
namespace App\Excel;

use App\Models\Admission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdmissionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?string $status = null,
        public ?int $batchId = null,
        public ?string $q = null,
    ) {}

    public function query()
    {
        // NOTE: adjust date column name if different. Using 'admission_date' per your schema.
        return Admission::query()
            ->with(['student', 'batch.course'])
            ->when($this->fromDate && $this->toDate, function ($q) {
                $q->whereBetween('admission_date', [$this->fromDate, $this->toDate]);
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->batchId, fn($q) => $q->where('batch_id', $this->batchId))
            ->when($this->q, function ($q) {
                $term = "%{$this->q}%";
                $q->whereHas('student', fn($s) =>
                    $s->where('name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('email', 'like', $term)
                );
            })
            ->latest('admission_date');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Admission Date',
            'Status',
            'Student Name',
            'Student Phone',
            'Student Email',
            'Batch',
            'Course',
            'Fee Total',
            'Fee Due',
        ];
    }

    public function map($admission): array
    {
        return [
            $admission->id,
            optional($admission->admission_date)->format('Y-m-d'),
            $admission->status,
            optional($admission->student)->name,
            optional($admission->student)->phone,
            optional($admission->student)->email,
            optional($admission->batch)->batch_name,
            optional(optional($admission->batch)->course)->course_name,
            $admission->fee_total,
            $admission->fee_due,
        ];
    }
}
