<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterStudent;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
/**
 * Registration page component for creating student accounts.
 */
class SignUp extends Component
{
    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function signUp(RegisterStudent $registerStudent): void
    {
        // Validate form input, create user, then authenticate and continue to profile setup.
        $this->validate();

        $user = $registerStudent->execute(
            email: $this->email,
            username: $this->username,
            password: $this->password,
        );

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirect(route('profile.setup'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.sign-up');
    }
}
