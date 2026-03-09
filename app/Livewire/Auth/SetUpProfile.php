<?php

namespace App\Livewire\Auth;

use App\Actions\Profile\CompleteProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.guest')]
/**
 * Captures and persists required student profile information after registration/login.
 */
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

    #[Validate('nullable|string|max:100')]
    public string $barangay = '';

    #[Validate('nullable|string|max:255')]
    public string $street_house_number = '';

    // PSGC codes used by dropdowns and API calls.
    #[Validate('nullable|string|max:20')]
    public string $province_code = '';

    #[Validate('nullable|string|max:20')]
    public string $municipality_code = '';

    #[Validate('nullable|string|max:20')]
    public string $barangay_code = '';

    #[Validate('nullable|string|max:20')]
    public string $postal_code = '';

    #[Validate('required|integer|min:1|max:9999')]
    public string $required_hours = '';

    #[Validate('required|string|max:11')]
    public string $contact_number = '';

    #[Validate('required|string|max:255')]
    public string $school_attended = '';

    public bool $showConfirmation = false;
    public bool $showSuccess = false;

    public array $provinceOptions = ['' => 'Select Province'];
    public array $municipalityOptions = ['' => 'Select Municipality'];
    public array $barangayOptions = ['' => 'Select Barangay'];

    public ?string $locationLoadError = null;
    public function mount(): void
    {
        // Pre-fill the form from the authenticated user's current profile data.
        $user = $this->getUserForProfile();
        if (!$user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }
        $this->first_name = $user->first_name ?? '';
        $this->middle_name = $user->middle_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->gender = $user->gender ?? '';
        $dob = $user->date_of_birth;
        if ($dob) {
            $this->date_of_birth = $dob instanceof \Carbon\CarbonInterface
                ? $dob->format('m/d/Y')
                : \Carbon\Carbon::parse((string) $dob)->format('m/d/Y');
        }

        $address = $user->address ?? [];
        if (is_string($address)) {
            $address = [];
        }
        $addr = is_array($address) ? $address : [];

        $this->province = $addr['province'] ?? $addr['state_province'] ?? '';
        $this->municipality = $addr['municipality'] ?? $addr['city'] ?? '';
        $this->barangay = $addr['barangay'] ?? '';
        $this->street_house_number = (string) (
            $addr['street_house_number']
            ?? trim(((string) ($addr['street'] ?? '')) . ' ' . ((string) ($addr['house_number'] ?? '')))
        );
        $this->province_code = (string) ($addr['province_code'] ?? '');
        $this->municipality_code = (string) ($addr['municipality_code'] ?? '');
        $this->barangay_code = (string) ($addr['barangay_code'] ?? '');


        $this->required_hours = (string) ($user->number_of_hours ?? '');
        $this->contact_number = $user->contact_number ?? '';
        $this->school_attended = $user->school_attended ?? '';

        // Build cascading dropdown options from PSGC API.
        $this->loadProvinces();

        if ($this->province_code === '' && $this->province !== '') {
            $this->province_code = $this->findCodeByName($this->provinceOptions, $this->province);
        }

        if ($this->province_code !== '') {
            $this->loadMunicipalities($this->province_code);
            $this->province = $this->provinceOptions[$this->province_code] ?? $this->province;
        }

        if ($this->municipality_code === '' && $this->municipality !== '') {
            $this->municipality_code = $this->findCodeByName($this->municipalityOptions, $this->municipality);
        }

        if ($this->municipality_code !== '') {
            $this->loadBarangays($this->municipality_code);
            $this->municipality = $this->municipalityOptions[$this->municipality_code] ?? $this->municipality;
        }

        if ($this->barangay_code === '' && $this->barangay !== '') {
            $this->barangay_code = $this->findCodeByName($this->barangayOptions, $this->barangay);
        }

        if ($this->barangay_code !== '') {
            $this->barangay = $this->barangayOptions[$this->barangay_code] ?? $this->barangay;
        }
    }

    protected function getUserForProfile(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
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
            'postal_code' => ['nullable', 'string', 'max:20'],
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

    public function submitProfile(CompleteProfile $completeProfile): void
    {
        // Persist normalized profile fields and mark profile as completed.
        $this->validate();
        $user = $this->getUserForProfile();
        if (!$user) {
            $this->addError('first_name', 'Session expired. Please sign up again.');
            return;
        }

        $dateOfBirth = $this->date_of_birth
            ? \Carbon\Carbon::createFromFormat('m/d/Y', $this->date_of_birth)->format('Y-m-d')
            : null;

        $completeProfile->execute($user, [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $dateOfBirth,
            'address' => [
                'province' => $this->province,
                'municipality' => $this->municipality,
                'street_house_number' => $this->street_house_number,
                'province_code' => $this->province_code,
                'municipality_code' => $this->municipality_code,
                'barangay_code' => $this->barangay_code,

            ],
            'number_of_hours' => (int) $this->required_hours,
            'contact_number' => $this->contact_number,
            'school_attended' => $this->school_attended,
            'course' => null,
        ]);

        $this->showConfirmation = false;
        $this->showSuccess = true;
    }

    public function closeSuccess(): void
    {
        $this->showSuccess = false;
        $this->redirect(route('home'), navigate: true);
    }

    public function updatedProvinceCode(string $value): void
    {
        //reset dependent fields when changes
        $this->province = $this->provinceOptions[$value] ?? '';
        $this->municipality_code = '';
        $this->municipality = '';
        $this->barangay_code = '';
        $this->barangay = '';
        $this->municipalityOptions = ['' => 'Select Municipality'];
        $this->barangayOptions = ['' => 'Select Barangay'];

        if ($value !== '') {
            $this->loadMunicipalities($value);
        }
    }
    public function updatedMunicipalityCode(string $value): void
    {
        // Reset barangay when municipality changes.
        $this->municipality = $this->municipalityOptions[$value] ?? '';
        $this->barangay = '';
        $this->barangay_code = '';
        $this->barangayOptions = ['' => 'Select Barangay'];

        if ($value !== '') {
            $this->loadBarangays($value);
        }
    }
    public function updatedBarangayCode(string $value): void
    {
        $this->barangay = $this->barangayOptions[$value] ?? '';
    }

    //private helper to find PSGC code by name from options list (used when pre-filling form from existing user data)
    // Returns the code if found, or empty string if not found.
    // load provinces/municipalities/barangays from PSGC API and find the code that matches the given name.
    private function loadProvinces(): void
    {
        $rows = $this->psgcGet('provinces/');
        $options = ['' => 'Select Province'];
        foreach ($rows as $row) {
            if (isset($row['code'], $row['name'])) {
                $options[(string) $row['code']] = (string) $row['name'];
            }
            $this->provinceOptions = $options;
        }
    }


    private function loadMunicipalities(string $provinceCode): void
    {
        $rows = $this->psgcGet("provinces/{$provinceCode}/cities-municipalities/");
        $options = ['' => 'Select Municipality'];

        foreach ($rows as $row) {
            if (isset($row['code'], $row['name'])) {
                $options[(string) $row['code']] = (string) $row['name'];
            }
        }

        $this->municipalityOptions = $options;
    }

    private function loadBarangays(string $municipalityCode): void
    {
        $rows = $this->psgcGet("cities-municipalities/{$municipalityCode}/barangays/");
        $options = ['' => 'Select Barangay'];

        foreach ($rows as $row) {
            if (isset($row['code'], $row['name'])) {
                $options[(string) $row['code']] = (string) $row['name'];
            }
        }

        $this->barangayOptions = $options;
    }

    // get the PSGC code for a given name from the options list, or return empty string if not found.
    private function psgcGet(string $path): array
    {
        try {
            // PSGC sometimes returns text/html content-type, so parse JSON body safely.
            $response = Http::timeout(15)
                ->retry(2, 250)
                ->get('https://psgc.gitlab.io/api/' . ltrim($path, '/'));

            if (!$response->successful()) {
                $this->locationLoadError = 'Unable to load location data from PSGC.';
                return [];
            }

            $data = $response->json();
            if (!is_array($data)) {
                $decoded = json_decode($response->body(), true);
                $data = is_array($decoded) ? $decoded : [];
            }

            $this->locationLoadError = null;

            /** @var array<int, array<string, mixed>> $data */
            return $data;
        } catch (\Throwable $e) {
            report($e);
            $this->locationLoadError = 'Unable to load location data from PSGC.';
            return [];
        }
    }
    /**
     * @param array<string, string> $options
     */
    private function findCodeByName(array $options, string $name): string
    {
        foreach ($options as $code => $label) {
            if ($code !== '' && strcasecmp($label, $name) === 0) {
                return $code;
            }
        }

        return '';
    }
    public function render()
    {
        return view('livewire.auth.set-up-profile', [
            'provinceOptions' => $this->provinceOptions,
            'municipalityOptions' => $this->municipalityOptions,
            'barangayOptions' => $this->barangayOptions,
            'locationLoadError' => $this->locationLoadError,
        ]);
    }
}
