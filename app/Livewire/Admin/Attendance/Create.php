<?php

namespace App\Livewire\Admin\Attendance;

use App\Models\Student;
use App\Models\Admission;
use App\Models\StudentAttendance;
use App\Models\Batch;
use App\Excel\AttendanceImport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedDate;
    public $selectedBatch = '';
    public $attendanceData = [];
    public $searchStudent = '';
    public $perPage = 20;
    
    // Excel import properties
    public $excelFile;
    public $showImportModal = false;
    public $importProgress = 0;
    public $importErrors = [];
    public $importSuccess = false;

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

    public function importAttendance()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $this->importProgress = 0;
            $this->importErrors = [];
            $this->importSuccess = false;

            Excel::import(new AttendanceImport(), $this->excelFile);

            $this->importSuccess = true;
            $this->importProgress = 100;
            
            session()->flash('success', 'Attendance imported successfully!');
            
            // Close modal and refresh data
            $this->showImportModal = false;
            $this->excelFile = null;
            $this->loadStudents();
            
        } catch (\Exception $e) {
            $this->importErrors[] = $e->getMessage();
            session()->flash('error', 'Error importing attendance: ' . $e->getMessage());
        }
    }

    public function downloadSampleTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['roll_no', 'student_phone', 'date', 'status', 'remarks']);
            
            // Add sample data
            fputcsv($file, ['ROLL20250001', '9876543210', '2025-01-15', 'present', 'On time']);
            fputcsv($file, ['ROLL20250002', '9876543211', '2025-01-15', 'absent', 'Sick leave']);
            fputcsv($file, ['ROLL20250003', '9876543212', '2025-01-15', 'late', 'Traffic delay']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
