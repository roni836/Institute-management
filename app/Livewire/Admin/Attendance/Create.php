<?php

namespace App\Livewire\Admin\Attendance;

use App\Models\Student;
use App\Models\Admission;
use App\Models\StudentAttendance;
use App\Models\Batch;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    use WithPagination;

    public $selectedDate;
    public $selectedBatch = '';
    public $attendanceData = [];
    public $searchStudent = '';
    public $perPage = 20;

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function updatedSelectedBatch()
    {
        $this->resetPage();
        $this->loadStudents();
    }

    public function updatedSearchStudent()
    {
        $this->resetPage();
    }

    public function loadStudents()
    {
        $query = Admission::with(['student', 'batch'])
            ->where('status', 'active');

        if ($this->selectedBatch) {
            $query->where('batch_id', $this->selectedBatch);
        }

        if ($this->searchStudent) {
            $query->whereHas('student', function($q) {
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                  ->orWhere('roll_no', 'like', '%' . $this->searchStudent . '%');
            });
        }

        $admissions = $query->paginate($this->perPage);

        // Initialize attendance data for each student
        foreach ($admissions as $admission) {
            $key = $admission->student_id . '_' . $admission->id;
            if (!isset($this->attendanceData[$key])) {
                // Check if attendance already exists for this date
                $existingAttendance = StudentAttendance::where('student_id', $admission->student_id)
                    ->where('admission_id', $admission->id)
                    ->whereDate('date', $this->selectedDate)
                    ->first();

                $this->attendanceData[$key] = [
                    'student_id' => $admission->student_id,
                    'admission_id' => $admission->id,
                    'status' => $existingAttendance ? $existingAttendance->status : 'present',
                    'remarks' => $existingAttendance ? $existingAttendance->remarks : '',
                    'exists' => $existingAttendance ? true : false,
                    'attendance_id' => $existingAttendance ? $existingAttendance->id : null
                ];
            }
        }

        return $admissions;
    }

    public function updateAttendance($key, $field, $value)
    {
        $this->attendanceData[$key][$field] = $value;
    }

    public function saveAttendance()
    {
        $savedCount = 0;
        $updatedCount = 0;

        foreach ($this->attendanceData as $key => $data) {
            if ($data['exists']) {
                // Update existing attendance
                StudentAttendance::where('id', $data['attendance_id'])->update([
                    'status' => $data['status'],
                    'remarks' => $data['remarks'],
                ]);
                $updatedCount++;
            } else {
                // Create new attendance
                StudentAttendance::create([
                    'student_id' => $data['student_id'],
                    'admission_id' => $data['admission_id'],
                    'date' => $this->selectedDate,
                    'status' => $data['status'],
                    'remarks' => $data['remarks'],
                ]);
                $savedCount++;
            }
        }

        $message = '';
        if ($savedCount > 0) {
            $message .= "Created {$savedCount} attendance records. ";
        }
        if ($updatedCount > 0) {
            $message .= "Updated {$updatedCount} attendance records. ";
        }

        session()->flash('success', $message ?: 'No changes made.');
        
        // Reload students to refresh the data
        $this->loadStudents();
    }

    public function render()
    {
        $admissions = $this->loadStudents();
        $batches = Batch::with('course')->get();
        
        return view('livewire.admin.attendance.create', [
            'admissions' => $admissions,
            'batches' => $batches
        ]);
    }
}
