<?php
namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public $name;
    public $batch_code;
    public $duration_months;
    public $gross_fee;
    public $discount;
    public $status;
    public $course;
    public $tution_fee = 0, $admission_fee = 0, $exam_fee = 0, $infra_fee = 0, $SM_fee = 0, $tech_fee = 0, $other_fee = 0;

    public function mount(Course $id)
    {
        $this->course          = $id;
        $this->name            = $id->name;
        $this->batch_code      = $id->batch_code;
        $this->duration_months = $id->duration_months;
        $this->gross_fee       = $id->gross_fee;
        $this->discount        = $id->discount;
        $this->status          = $id->status;
        $this->tution_fee      = $id->tution_fee ?? 0;
        $this->admission_fee   = $id->admission_fee ?? 0;
        $this->exam_fee        = $id->exam_fee ?? 0;
        $this->infra_fee       = $id->infra_fee ?? 0;
        $this->SM_fee          = $id->SM_fee ?? 0;
        $this->tech_fee        = $id->tech_fee ?? 0;
        $this->other_fee       = $id->other_fee ?? 0;
    }

    public function rules()
    {
        return [
            'name'            => 'required|string|max:255',
            'batch_code'      => 'nullable|string|max:255',
            'duration_months' => 'nullable|integer|min:1|max:120',
            'gross_fee'       => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0|max:' . $this->gross_fee,
            'status'          => 'required|in:Active,Upcoming',
            'tution_fee'      => 'nullable|numeric|min:0',
            'admission_fee'   => 'nullable|numeric|min:0',
            'exam_fee'        => 'nullable|numeric|min:0',
            'infra_fee'       => 'nullable|numeric|min:0',
            'SM_fee'          => 'nullable|numeric|min:0',
            'tech_fee'        => 'nullable|numeric|min:0',
            'other_fee'       => 'nullable|numeric|min:0',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $this->course->update($data);

        session()->flash('ok', 'Course updated.');
        return redirect()->route('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.edit');
    }
}
