<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public $name, $batch_code, $duration_months, $gross_fee, $discount = 0;

    protected function rules()
    {
        return [
            'name'             => 'required|string|max:255',
            'batch_code'       => 'nullable|string|max:255',
            'duration_months'  => 'nullable|integer|min:1|max:120',
            'gross_fee'        => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
        ];
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
