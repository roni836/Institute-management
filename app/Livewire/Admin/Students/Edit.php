<?php

namespace App\Livewire\Admin\Students;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    use WithFileUploads;

    public Student $student;
    public $name;
    public $email;
    public $phone;
    public $dob;
    public $father_name;
    public $mother_name;
    public $address;
    public $status;
    public $photo;
    public $newPhoto;

    protected $rules = [
        'name' => 'required|string|max:255|min:2',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s\(\)]+$/',
        'dob' => 'nullable|date|before:today',
        'father_name' => 'nullable|string|max:255|min:2',
        'mother_name' => 'nullable|string|max:255|min:2',
        'address' => 'nullable|string|max:1000|min:5',
        'status' => 'required|in:active,inactive,completed',
        'newPhoto' => 'nullable|image|max:1024|mimes:jpeg,png,jpg'
    ];

    protected $messages = [
        'name.required' => 'Student name is required.',
        'name.min' => 'Student name must be at least 2 characters.',
        'email.email' => 'Please enter a valid email address.',
        'phone.regex' => 'Phone number can only contain numbers, spaces, hyphens, and parentheses.',
        'dob.before' => 'Date of birth must be in the past.',
        'father_name.min' => 'Father\'s name must be at least 2 characters.',
        'mother_name.min' => 'Mother\'s name must be at least 2 characters.',
        'address.min' => 'Address must be at least 5 characters.',
        'newPhoto.mimes' => 'Photo must be a JPEG, PNG, or JPG file.',
        'newPhoto.max' => 'Photo size must be less than 1MB.'
    ];

    public function mount($id)
    {
        try {
            $this->student = Student::findOrFail($id);
            $this->name = $this->student->name;
            $this->email = $this->student->email;
            $this->phone = $this->student->phone;
            $this->dob = $this->student->dob;
            $this->father_name = $this->student->father_name;
            $this->mother_name = $this->student->mother_name;
            $this->address = $this->student->address;
            $this->status = $this->student->status;
            $this->photo = $this->student->photo;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Student not found.');
            return redirect()->route('admin.students.index');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        // Clear validation errors when user starts typing
        if (in_array($propertyName, ['name', 'email', 'phone', 'dob', 'father_name', 'mother_name', 'address'])) {
            $this->resetErrorBag($propertyName);
        }
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'phone', 'dob', 'father_name', 'mother_name', 'address', 'status', 'newPhoto']);
        $this->resetErrorBag();
        
        // Reload original student data
        $this->name = $this->student->name;
        $this->email = $this->student->email;
        $this->phone = $this->student->phone;
        $this->dob = $this->student->dob;
        $this->father_name = $this->student->father_name;
        $this->mother_name = $this->student->mother_name;
        $this->address = $this->student->address;
        $this->status = $this->student->status;
    }

    public function hasChanges()
    {
        return $this->name !== $this->student->name ||
               $this->email !== $this->student->email ||
               $this->phone !== $this->student->phone ||
               $this->dob !== $this->student->dob ||
               $this->father_name !== $this->student->father_name ||
               $this->mother_name !== $this->student->mother_name ||
               $this->address !== $this->student->address ||
               $this->status !== $this->student->status ||
               $this->newPhoto !== null;
    }

    public function confirmLeave()
    {
        if ($this->hasChanges()) {
            return redirect()->route('admin.students.index')->with('warning', 'You have unsaved changes. Please save or reset the form before leaving.');
        }
        return redirect()->route('admin.students.index');
    }

    public function save()
    {
        try {
            $this->validate();

            // Handle email uniqueness validation for the current student
            if ($this->email && $this->email !== $this->student->email) {
                $this->validate([
                    'email' => 'unique:students,email,' . $this->student->id
                ], [
                    'email.unique' => 'This email is already taken by another student.'
                ]);
            }

            $data = [
                'name' => trim($this->name),
                'email' => $this->email ? trim($this->email) : null,
                'phone' => $this->phone ? trim($this->phone) : null,
                'dob' => $this->dob,
                'father_name' => $this->father_name ? trim($this->father_name) : null,
                'mother_name' => $this->mother_name ? trim($this->mother_name) : null,
                'address' => $this->address ? trim($this->address) : null,
                'status' => $this->status,
            ];

            // Handle photo upload if new photo is selected
            if ($this->newPhoto) {
                $data['photo'] = $this->newPhoto->store('student-photos', 'public');
            }

            $this->student->update($data);

            session()->flash('success', 'Student updated successfully.');
            return redirect()->route('admin.students.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are already handled by Livewire
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the student. Please try again.');
            Log::error('Student update error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.students.edit');
    }
}
