<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;
 
#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $name, $batch_code, $duration_months, $gross_fee, $discount = 0;
    public $tution_fee = 0, $admission_fee = 0, $exam_fee = 0, $infra_fee = 0, $SM_fee = 0, $tech_fee = 0, $other_fee = 0;

    protected function rules()
    {
        return [
            'name'             => 'required|string|max:255',
            'batch_code'       => 'nullable|string|max:255',
            'duration_months'  => 'nullable|integer|min:1|max:120',
            'gross_fee'        => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'tution_fee'       => 'nullable|numeric|min:0',
            'admission_fee'    => 'nullable|numeric|min:0',
            'exam_fee'         => 'nullable|numeric|min:0',
            'infra_fee'        => 'nullable|numeric|min:0',
            'SM_fee'           => 'nullable|numeric|min:0',
            'tech_fee'         => 'nullable|numeric|min:0',
            'other_fee'        => 'nullable|numeric|min:0',
        ];
    }

    public function updated($propertyName)
    {
        // Recalculate gross fee whenever a fee-related property changes
        if (in_array($propertyName, [
            'tution_fee', 'admission_fee', 'exam_fee',
            'infra_fee', 'SM_fee', 'tech_fee', 'other_fee'
        ])) {
            $this->gross_fee = 
                (float) $this->tution_fee +
                (float) $this->admission_fee +
                (float) $this->exam_fee +
                (float) $this->infra_fee +
                (float) $this->SM_fee +
                (float) $this->tech_fee +
                (float) $this->other_fee;
        }
    }

    public function getNetFeeProperty()
    {
        return max(0, (float) $this->gross_fee - (float) $this->discount);
    }

    public function save()
    {
        $data = $this->validate();
        Course::create($data);

        session()->flash('ok', 'Course created.');
        return redirect()->route('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.create');
    }
}
