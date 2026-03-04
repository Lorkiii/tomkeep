<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class TermsAndConditions extends Component
{
    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    public function agree(): void
    {
        // TODO: persist agreement (e.g. session or DB) when needed
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.terms-and-conditions');
    }
}
