<?php

namespace App\Livewire\Admin\Students;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $dob;
    public $father_name;
    public $mother_name;
    public $address;
    public $photo;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:students,email',
        'phone' => 'nullable|string|max:20',
        'dob' => 'nullable|date',
        'father_name' => 'nullable|string|max:255',
        'mother_name' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'photo' => 'nullable|image|max:1024'
    ];

    public function save()
    {
        $data = $this->validate();
        
        if ($this->photo) {
            $data['photo'] = $this->photo->store('student-photos', 'public');
        }

        $data['student_uid'] = 'STU' . date('Y') . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['roll_no'] = 'ROLL' . date('Y') . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['admission_date'] = now();

        Student::create($data);

        session()->flash('success', 'Student created successfully.');
        return redirect()->route('admin.students.index');
    }

    public function render()
    {
        return view('livewire.admin.students.create');
    }
}
