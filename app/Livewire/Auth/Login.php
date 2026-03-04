<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';

    /**
     * Invokable entry for full-page Livewire route (Route::get('/login', Login::class)).
     */
    public function __invoke()
    {
        return parent::__invoke();
    }

    protected function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(): void
    {
        $this->validate();
        // TODO: integrate with Laravel auth (Auth::attempt)
        $this->addError('username', 'Authentication not yet implemented.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
