<?php

namespace App\Livewire\Public;

use Livewire\Attributes\On;
use Livewire\Component;

class LandingPage extends Component
{
    #[On("components.layouts.app")]
    public function render()
    {
        return view('livewire.public.landing-page');
    }
}
