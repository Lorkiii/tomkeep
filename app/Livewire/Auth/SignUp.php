<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class SignUp extends Component
{
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function signUp(): void
    {
        $this->validate();

        $user = User::query()->create([
            'email' => $this->email,
            'password' => $this->password,
            'first_name' => 'Pending',
            'middle_name' => '',
            'last_name' => 'User',
            'contact_number' => '00000000000',
            'address' => ['line' => ''],
            'course' => 'Pending',
            'date_of_birth' => now(),
            'school_attended' => '',
        ]);

        session()->put('pending_profile_user_id', $user->id);
        $this->redirect(route('profile.setup'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.sign-up');
    }
}
