<?php

namespace App\Livewire\Admin\Admissions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Admission;
use App\Models\Batch;
use App\Models\Student;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Admission $admission;

    public function mount(Admission $admission)
    {
        $this->admission = $admission;
    }

    public function save()
    {
        $this->validate([
            'admission.student_id'     => 'required|exists:students,id',
            'admission.batch_id'       => 'required|exists:batches,id',
            'admission.admission_date' => 'required|date',
            'admission.discount'       => 'nullable|numeric|min:0',
            'admission.mode'           => 'required|in:full,installment',
        ]);

        $this->admission->save();

        session()->flash('ok', 'Admission updated successfully.');
        return redirect()->route('admin.admissions.index');
    }

    public function render()
    {
        return view('livewire.admin.admissions.edit', [
            'students' => Student::all(),
            'batches'  => Batch::with('course')->get(),
        ]);
    }
}
