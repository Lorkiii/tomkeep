<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
/**
 * Username/password login component backed by Laravel Auth.
 */
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
        // Attempt authentication and route users based on profile completion state.
        $this->validate();

        if (!Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            $this->addError('username', 'Invalid username or password.');
            return;
        }

        request()->session()->regenerate();

        $target = Auth::user()?->profile_completed ? 'home' : 'profile.setup';
        $this->redirect(route($target), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
