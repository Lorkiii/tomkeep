<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.splash')]
class SplashScreen extends Component
{
    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    /**
     * Redirect to Terms page via Livewire (SPA-style, no full page reload).
     * Called from the frontend after the splash delay.
     */
    public function redirectToTerms(): void
    {
        $this->redirect(route('terms'), navigate: true);
    }

    /**
     * User accepted terms; redirect to login.
     */
    public function agree(): void
    {
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.splash-screen');
    }
}
