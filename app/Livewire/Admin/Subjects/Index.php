<?php

namespace App\Livewire\Admin\Subjects;

use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    public function render()
    {
        $subjects = Subject::get();
        return view('livewire.admin.subjects.index',[
            'subjects' => $subjects
        ]);
    }
}
