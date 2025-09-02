<?php

namespace App\Livewire\Teacher\Attendance;

use App\Livewire\Admin\Attendance\Index as AdminAttendanceIndex;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.teacher')]
class Index extends AdminAttendanceIndex
{
    // This component extends the admin attendance index but uses the teacher layout
    // Teachers can manage attendance but with restricted functionality
}
