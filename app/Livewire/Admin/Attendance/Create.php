<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.admin.attendance.create');
    }
}
