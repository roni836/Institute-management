<?php

namespace App\Livewire\Teacher\Exams;

use App\Livewire\Admin\Exams\Index as AdminExamsIndex;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.teacher')]
class Index extends AdminExamsIndex
{
    // This component extends the admin exams index but uses the teacher layout
    // Teachers can view exams but with restricted functionality
}
