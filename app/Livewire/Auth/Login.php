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
        // Basic login validation for the submitted credentials.
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(): void
    {
        // Validate first so empty submissions do not reach the auth layer.
        $this->validate();

        // Attempt to authenticate using the username and password from the form.
        if (!Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            $this->addError('username', 'Invalid username or password.');
            return;
        }

        // Prevent session fixation after a successful login.
        request()->session()->regenerate();

        $user = Auth::user();

        // Admins go to the admin dashboard.
        // Students are routed based on profile-completion and approval status.
        if ($user?->role === 'admin') {
            $target = 'admin.dashboard';
        } elseif (! $user?->profile_completed) {
            $target = 'profile.setup';
        } elseif ($user?->status === 'rejected') {
            $target = 'application-rejected';
        } elseif ($user?->status !== 'approved') {
            $target = 'waiting-approval';
        } else {
            $target = 'home';
        }

        // Livewire navigate keeps the experience smoother than a hard full-page reload.
        $this->redirect(route($target), navigate: true);
    }

    public function render()
    {
        // Render the login Blade view used by this Livewire component.
        return view('livewire.auth.login');
    }
}
