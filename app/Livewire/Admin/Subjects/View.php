<?php

namespace App\Livewire\Admin\Subjects;

use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class View extends Component
{
    public Subject $subject;
    public function mount($id)
    {
        $this->subject = Subject::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.subjects.view');
    }
}
