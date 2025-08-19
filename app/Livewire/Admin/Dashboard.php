<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Student, Enrollment, Payment};

class Dashboard extends Component
{
   public function render(){
        $kpis = [
            'students'   => Student::count(),
            // 'admissions' => Enrollment::count(),
            'admissions' => 200,
            // 'due'        => Enrollment::sum('fee_due'),
            'due'        => 5000,
            'collected_m'=> 90000,
        ];
        return view('livewire.admin.dashboard', compact('kpis'))->layout('layouts.admin')->title('Dashboard');
    }
}
