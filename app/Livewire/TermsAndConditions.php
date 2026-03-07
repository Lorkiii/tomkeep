<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.terms')]
class TermsAndConditions extends Component
{
    public bool $agreed = false;

    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    public function agree(): void
    {
        session()->put('terms_agreed', true);
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.terms-and-conditions');
    }
}
