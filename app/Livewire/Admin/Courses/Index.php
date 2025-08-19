<?php

namespace App\Livewire\Admin\Courses;

use Livewire\Component;

use Livewire\Attributes\Layout;
#[Layout('components.layouts.admin')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.courses.index');
    }
}
