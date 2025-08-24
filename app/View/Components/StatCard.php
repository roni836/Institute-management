<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatCard extends Component
{
    public function __construct(
        public string $title,
        public string|int $value,
        public string $icon,
        public string $color = 'orange'
    ) {}

    public function render()
    {
        return view('components.stat-card');
    }
}
