<?php

namespace App\Livewire\Admin\Attendance;

use App\Models\StudentAttendance;
use App\Excel\AttendanceExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public $searchDate = '';
    public $perPage = 15;

    public function updatingSearchDate()
    {
        $this->resetPage();
    }

    public function getAttendanceStats()
    {
        $query = StudentAttendance::query()
            ->selectRaw('
                date,
                COUNT(*) as total_students,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused_count
            ')
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($this->searchDate) {
            $query->whereDate('date', $this->searchDate);
        }

        return $query->paginate($this->perPage);
    }

    public function exportAttendance($date = null)
    {
        $filename = $date ? "attendance_{$date}.xlsx" : "attendance_all.xlsx";
        
        return Excel::download(new AttendanceExport($date), $filename);
    }

    public function render()
    {
        $attendanceStats = $this->getAttendanceStats();
        
        return view('livewire.admin.attendance.index', [
            'attendanceStats' => $attendanceStats
        ]);
    }
}
