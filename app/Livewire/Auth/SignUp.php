<?php

namespace App\Livewire\Auth;

use App\Services\OjtUserStorage;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
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
            'email' => ['required', 'email'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function signUp(OjtUserStorage $storage): void
    {
        $this->validate();

        if ($storage->findByEmail($this->email)) {
            $this->addError('email', 'This email is already registered.');
            return;
        }
        if ($storage->findByUsername($this->username)) {
            $this->addError('username', 'This username is already taken.');
            return;
        }

        $user = $storage->create([
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'first_name' => 'Pending',
            'middle_name' => '',
            'last_name' => 'User',
            'contact_number' => '',
            'address' => ['province' => '', 'municipality' => '', 'street' => '', 'house_number' => '', 'postal_code' => ''],
            'required_hours' => 0,
            'school_attended' => '',
        ]);

        session()->put('ojt_user_id', $user['id']);
        $this->redirect(route('profile.setup'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.sign-up');
    }
}
