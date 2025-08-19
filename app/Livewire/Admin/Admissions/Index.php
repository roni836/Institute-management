<?php

namespace App\Livewire\Admin\Admissions;

use Livewire\Component;

use Livewire\Attributes\Layout;
#[Layout('components.layouts.admin')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.admissions.index');
    }
}
