<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.guest')]
class SetUpProfile extends Component
{
    /** Invokable entry for full-page Livewire route. */
    public function __invoke()
    {
        return parent::__invoke();
    }

    #[Validate('required|string|max:30')]
    public string $first_name = '';

    #[Validate('required|string|max:30')]
    public string $middle_name = '';

    #[Validate('required|string|max:30')]
    public string $last_name = '';

    #[Validate('required|string|in:Male,Female,Other')]
    public string $gender = '';

    #[Validate('nullable|date_format:m/d/Y')]
    public string $date_of_birth = '';

    #[Validate('nullable|string|max:100')]
    public string $province = '';

    #[Validate('nullable|string|max:100')]
    public string $municipality = '';

    #[Validate('nullable|string|max:255')]
    public string $street_house_number = '';

    #[Validate('required|integer|min:1|max:9999')]
    public string $required_hours = '';

    #[Validate('required|string|max:11')]
    public string $contact_number = '';

    #[Validate('required|string|max:255')]
    public string $school_attended = '';

    public bool $showConfirmation = false;
    public bool $showSuccess = false;

    public function mount(): void
    {
        $user = $this->getUserForProfile();
        if (!$user) {
            $this->redirect(route('signup'), navigate: true);
            return;
        }
        $this->first_name = $user->first_name ?? '';
        $this->middle_name = $user->middle_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->gender = $user->gender ?? '';
        $this->date_of_birth = $user->date_of_birth instanceof \DateTimeInterface
            ? $user->date_of_birth->format('m/d/Y')
            : '';

        $address = $user->address ?? [];
        if (is_string($address)) {
            $address = ['street_house_number' => $address];
        }
        $this->province = is_array($address) ? ($address['province'] ?? '') : '';
        $this->municipality = is_array($address) ? ($address['municipality'] ?? '') : '';
        $this->street_house_number = is_array($address) ? ($address['street_house_number'] ?? '') : '';
        $this->required_hours = (string) ($user->required_hours ?? '');
        $this->contact_number = $user->contact_number ?? '';
        $this->school_attended = $user->school_attended ?? '';
    }

    protected function getUserForProfile(): ?User
    {
        if (Auth::check()) {
            return Auth::user();
        }
        $id = session('pending_profile_user_id');
        return $id ? User::find($id) : null;
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:30'],
            'middle_name' => ['required', 'string', 'max:30'],
            'last_name' => ['required', 'string', 'max:30'],
            'gender' => ['required', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date_format:m/d/Y'],
            'province' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'street_house_number' => ['nullable', 'string', 'max:255'],
            'required_hours' => ['required', 'integer', 'min:1', 'max:9999'],
            'contact_number' => ['required', 'string', 'max:11'],
            'school_attended' => ['required', 'string', 'max:255'],
        ];
    }

    public function openConfirmation(): void
    {
        $this->validate();
        $this->showConfirmation = true;
    }

    public function closeConfirmation(): void
    {
        $this->showConfirmation = false;
    }

    public function submitProfile(): void
    {
        $this->validate();
        $user = $this->getUserForProfile();
        if (!$user) {
            $this->addError('first_name', 'Session expired. Please sign up again.');
            return;
        }

        $user->update([
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth ? Carbon::createFromFormat('m/d/Y', $this->date_of_birth) : null,
            'address' => [
                'province' => $this->province,
                'municipality' => $this->municipality,
                'street_house_number' => $this->street_house_number,
            ],
            'required_hours' => (int) $this->required_hours,
            'contact_number' => $this->contact_number,
            'school_attended' => $this->school_attended,
        ]);

        session()->forget('pending_profile_user_id');
        $this->showConfirmation = false;
        $this->showSuccess = true;
    }

    public function closeSuccess(): void
    {
        $this->showSuccess = false;
        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.set-up-profile');
    }
}
