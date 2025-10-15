<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use ZipArchive;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
class ManageMedia extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedFiles = [];
    public $selectAll = false;
    public $filterType = 'all'; // all, photo, aadhaar

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => 'all']
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
        $this->selectedFiles = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedFiles = $this->getStudentsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedFiles = [];
        }
    }

    public function toggleFileSelection($studentId)
    {
        if (in_array($studentId, $this->selectedFiles)) {
            $this->selectedFiles = array_diff($this->selectedFiles, [$studentId]);
        } else {
            $this->selectedFiles[] = $studentId;
        }
        
        $this->selectAll = count($this->selectedFiles) === $this->getStudentsQuery()->count();
    }

    public function getStudentsQuery()
    {
        $query = Student::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('enrollment_id', 'like', '%' . $this->search . '%')
                  ->orWhere('student_uid', 'like', '%' . $this->search . '%')
                  ->orWhere('roll_no', 'like', '%' . $this->search . '%');
            });
        }

        // Apply file type filter
        if ($this->filterType === 'photo') {
            $query->whereNotNull('photo');
        } elseif ($this->filterType === 'aadhaar') {
            $query->whereNotNull('aadhaar_document_path');
        } else {
            // Show students with either photo or aadhaar
            $query->where(function ($q) {
                $q->whereNotNull('photo')
                  ->orWhereNotNull('aadhaar_document_path');
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function downloadSelected()
    {
        if (empty($this->selectedFiles)) {
            session()->flash('error', 'Please select at least one file to download.');
            return;
        }

        $students = Student::whereIn('id', $this->selectedFiles)->get();
        $zipFileName = 'media_files_' . now()->format('Y_m_d_H_i_s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            session()->flash('error', 'Could not create zip file.');
            return;
        }

        $fileCount = 0;
        foreach ($students as $student) {
            // Add photo if exists
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                $photoPath = storage_path('app/public/' . $student->photo);
                $photoExtension = pathinfo($student->photo, PATHINFO_EXTENSION);
                $photoName = ($student->enrollment_id ?: $student->student_uid) . '.' . $photoExtension;
                $zip->addFile($photoPath, 'photos/' . $photoName);
                $fileCount++;
            }

            // Add aadhaar if exists
            if ($student->aadhaar_document_path && Storage::disk('public')->exists($student->aadhaar_document_path)) {
                $aadhaarPath = storage_path('app/public/' . $student->aadhaar_document_path);
                $aadhaarExtension = pathinfo($student->aadhaar_document_path, PATHINFO_EXTENSION);
                $aadhaarName = 'aadhaar_' . ($student->enrollment_id ?: $student->student_uid) . '.' . $aadhaarExtension;
                $zip->addFile($aadhaarPath, 'aadhaar/' . $aadhaarName);
                $fileCount++;
            }
        }

        $zip->close();

        if ($fileCount === 0) {
            session()->flash('error', 'No files found for selected students.');
            return;
        }

        return Response::download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function downloadSingle($studentId, $type)
    {
        $student = Student::findOrFail($studentId);
        
        if ($type === 'photo' && $student->photo && Storage::disk('public')->exists($student->photo)) {
            $filePath = storage_path('app/public/' . $student->photo);
            $extension = pathinfo($student->photo, PATHINFO_EXTENSION);
            $fileName = ($student->enrollment_id ?: $student->student_uid) . '.' . $extension;
            return Response::download($filePath, $fileName);
        }
        
        if ($type === 'aadhaar' && $student->aadhaar_document_path && Storage::disk('public')->exists($student->aadhaar_document_path)) {
            $filePath = storage_path('app/public/' . $student->aadhaar_document_path);
            $extension = pathinfo($student->aadhaar_document_path, PATHINFO_EXTENSION);
            $fileName = 'aadhaar_' . ($student->enrollment_id ?: $student->student_uid) . '.' . $extension;
            return Response::download($filePath, $fileName);
        }
        
        session()->flash('error', 'File not found.');
    }

    public function render()
    {
        $students = $this->getStudentsQuery()->paginate(12);
        
        return view('livewire.admin.manage-media', [
            'students' => $students,
            'totalFiles' => $this->getStudentsQuery()->count()
        ]);
    }
}
