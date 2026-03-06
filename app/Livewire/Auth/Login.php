<?php

namespace App\Livewire\Auth;

use App\Services\OjtUserStorage;
use Illuminate\Support\Facades\Hash;
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

    public function login(OjtUserStorage $storage): void
    {
        $this->validate();

        $user = $storage->findByUsername($this->username);
        if (!$user || !Hash::check($this->password, $user['password'] ?? '')) {
            $this->addError('username', 'Invalid username or password.');
            return;
        }

        session()->put('ojt_user_id', $user['id']);
        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
