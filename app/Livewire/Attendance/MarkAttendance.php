<?php

namespace App\Livewire\Attendance;

use Livewire\Component;

class MarkAttendance extends Component
{
    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    public function render()
    {
        return view('livewire.attendance.mark-attendance');
    }
}
