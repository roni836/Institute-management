<?php
namespace App\Livewire\Admin\Subjects;

use App\Models\Course;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $name, $course_id;

    public function rules()
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'name'      => 'required|string|max:255',
        ];
    }
    public function save()
    {
        $data = $this->validate();

        Subject::create($data);
        session()->flash('ok', 'Subject created.');
        return redirect()->route('admin.subjects.index');
    }

    public function render()
    {
        return view('livewire.admin.subjects.create',[
            'courses' => Course::orderBy('name')->get(),
        ]);
    }
}
