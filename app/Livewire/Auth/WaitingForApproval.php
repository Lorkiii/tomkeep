<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class WaitingForApproval extends Component
{
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.waiting-for-approval');
    }
}
