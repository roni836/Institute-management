<?php

namespace App\Livewire\Admin\Attendance;

use App\Models\StudentAttendance;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class View extends Component
{
    use WithPagination;

    public $date;
    public $perPage = 20;
    public $searchStudent = '';
    public $filterStatus = '';

    public function mount($date)
    {
        $this->date = $date;
    }

    public function updatingSearchStudent()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getAttendanceRecords()
    {
        $query = StudentAttendance::with(['student', 'admission.batch.course'])
            ->whereDate('date', $this->date)
            ->orderBy('created_at', 'desc');

        if ($this->searchStudent) {
            $query->whereHas('student', function($q) {
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('roll_no', 'like', '%' . $this->searchStudent . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate($this->perPage);
    }

    public function getDateStats()
    {
        return StudentAttendance::whereDate('date', $this->date)
            ->selectRaw('
                COUNT(*) as total_students,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused_count
            ')
            ->first();
    }

    public function render()
    {
        $attendanceRecords = $this->getAttendanceRecords();
        $dateStats = $this->getDateStats();
        
        return view('livewire.admin.attendance.view', [
            'attendanceRecords' => $attendanceRecords,
            'dateStats' => $dateStats
        ]);
    }
}
