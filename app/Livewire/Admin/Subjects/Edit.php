<?php
namespace App\Livewire\Admin\Subjects;

use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public $name, $course_id;
    public $subject;

    public function mount($id)
    {
        $subject = Subject::findOrFail($id);
        $this->subject   = $subject;
        $this->name      = $subject->name;
        $this->course_id = $subject->course_id;
    }

    public function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $this->subject->update($data);

        session()->flash('ok', 'Subject updated successfully.');
        return redirect()->route('admin.subjects.index');
    }

    public function render()
    {
        return view('livewire.admin.subjects.edit', [
            'courses' => \App\Models\Course::orderBy('name')->get(),
        ]);
    }
}
