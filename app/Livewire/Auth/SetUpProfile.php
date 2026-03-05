<?php

namespace App\Livewire\Auth;

use App\Services\OjtUserStorage;
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
    public string $street = '';

    #[Validate('nullable|string|max:50')]
    public string $house_number = '';

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

    public function mount(OjtUserStorage $storage): void
    {
        $user = $this->getUserForProfile($storage);
        if (!$user) {
            $this->redirect(route('signup'), navigate: true);
            return;
        }
        $this->first_name = $user['first_name'] ?? '';
        $this->middle_name = $user['middle_name'] ?? '';
        $this->last_name = $user['last_name'] ?? '';
        $this->gender = $user['gender'] ?? '';
        $dob = $user['date_of_birth'] ?? null;
        if ($dob) {
            $this->date_of_birth = is_string($dob) ? (str_contains($dob, '/') ? $dob : \Carbon\Carbon::parse($dob)->format('m/d/Y')) : '';
        }

        $address = $user['address'] ?? [];
        if (is_string($address)) {
            $address = [];
        }
        $addr = is_array($address) ? $address : [];
        $this->province = $addr['province'] ?? $addr['state_province'] ?? '';
        $this->municipality = $addr['municipality'] ?? $addr['city'] ?? '';
        $provinces = static::getProvinceMunicipalities();
        if ($this->province !== '' && $this->municipality !== '' && isset($provinces[$this->province]) && ! in_array($this->municipality, $provinces[$this->province], true)) {
            $this->municipality = '';
        }
        $this->street = $addr['street'] ?? $addr['street_address'] ?? '';
        $this->house_number = $addr['house_number'] ?? $addr['street_address_line_2'] ?? '';
        $this->postal_code = $addr['postal_code'] ?? '';
        $this->required_hours = (string) ($user['required_hours'] ?? '');
        $this->contact_number = $user['contact_number'] ?? '';
        $this->school_attended = $user['school_attended'] ?? '';
    }

    /** @return array<string, mixed>|null */
    protected function getUserForProfile(OjtUserStorage $storage): ?array
    {
        $id = session('ojt_user_id');
        return $id ? $storage->findById($id) : null;
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
            'street' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:50'],
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

    public function submitProfile(OjtUserStorage $storage): void
    {
        $this->validate();
        $user = $this->getUserForProfile($storage);
        if (!$user) {
            $this->addError('first_name', 'Session expired. Please sign up again.');
            return;
        }

        $dateOfBirth = $this->date_of_birth
            ? \Carbon\Carbon::createFromFormat('m/d/Y', $this->date_of_birth)->format('Y-m-d')
            : null;

        $storage->update($user['id'], [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $dateOfBirth,
            'address' => [
                'province' => $this->province,
                'municipality' => $this->municipality,
                'street' => $this->street,
                'house_number' => $this->house_number,
                'postal_code' => $this->postal_code,
            ],
            'required_hours' => (int) $this->required_hours,
            'contact_number' => $this->contact_number,
            'school_attended' => $this->school_attended,
        ]);

        $this->showConfirmation = false;
        $this->showSuccess = true;
    }

    public function closeSuccess(): void
    {
        $this->showSuccess = false;
        $this->redirect(route('home'), navigate: true);
    }

    /** Province -> municipalities (cascading dropdown). */
    public static function getProvinceMunicipalities(): array
    {
        return [
            'Abra' => ['Bangued', 'Bucay', 'Bucloc', 'Daguioman', 'Danglas', 'Dolores', 'La Paz', 'Lacub', 'Lagangilang', 'Lagayan', 'Langiden', 'Licuan-Baay', 'Luba', 'Malibcong', 'Manabo', 'Peñarrubia', 'Pidigan', 'Pilar', 'Sallapadan', 'San Isidro', 'San Juan', 'San Quintin', 'Tayum', 'Tineg', 'Tubo', 'Villaviciosa', 'Other'],
            'Bulacan' => ['Angat', 'Balagtas', 'Baliuag', 'Bocaue', 'Bulacan', 'Bustos', 'Calumpit', 'Doña Remedios Trinidad', 'Guiguinto', 'Hagonoy', 'Malolos', 'Marilao', 'Meycauayan', 'Norzagaray', 'Obando', 'Pandi', 'Paombong', 'Plaridel', 'Pulilan', 'San Ildefonso', 'San Jose del Monte', 'San Miguel', 'San Rafael', 'Santa Maria', 'Other'],
            'Cavite' => ['Alfonso', 'Amadeo', 'Bacoor', 'Carmona', 'Cavite City', 'Dasmariñas', 'General Emilio Aguinaldo', 'General Mariano Alvarez', 'General Trias', 'Imus', 'Indang', 'Kawit', 'Magallanes', 'Maragondon', 'Mendez', 'Naic', 'Noveleta', 'Rosario', 'Silang', 'Tagaytay', 'Tanza', 'Ternate', 'Trece Martires City', 'Other'],
            'Laguna' => ['Alaminos', 'Bay', 'Biñan', 'Cabuyao', 'Calamba', 'Calauan', 'Cavinti', 'Famy', 'Kalayaan', 'Liliw', 'Los Baños', 'Luisiana', 'Lumban', 'Mabitac', 'Magdalena', 'Majayjay', 'Nagcarlan', 'Paete', 'Pagsanjan', 'Pakil', 'Pangil', 'Pila', 'Rizal', 'San Pablo', 'San Pedro', 'Santa Cruz', 'Santa Maria', 'Santa Rosa', 'Siniloan', 'Victoria', 'Other'],
            'Metro Manila' => ['Caloocan', 'Las Piñas', 'Makati', 'Malabon', 'Mandaluyong', 'Manila', 'Marikina', 'Muntinlupa', 'Navotas', 'Parañaque', 'Pasay', 'Pasig', 'Pateros', 'Quezon City', 'San Juan', 'Taguig', 'Valenzuela', 'Other'],
            'Pampanga' => ['Angeles', 'Apalit', 'Arayat', 'Bacolor', 'Candaba', 'Floridablanca', 'Guagua', 'Lubao', 'Mabalacat', 'Macabebe', 'Magalang', 'Masantol', 'Mexico', 'Minalin', 'Porac', 'San Fernando', 'San Luis', 'San Simon', 'Santa Ana', 'Santa Rita', 'Santo Tomas', 'Sasmuan', 'Other'],
            'Rizal' => ['Angono', 'Antipolo', 'Baras', 'Binangonan', 'Cainta', 'Cardona', 'Jalajala', 'Morong', 'Pililla', 'Rodriguez', 'San Mateo', 'Tanay', 'Taytay', 'Teresa', 'Other'],
            'Other' => ['Other'],
        ];
    }

    public function getProvinceOptions(): array
    {
        return [
            '' => 'Select Province',
            'Abra' => 'Abra',
            'Bulacan' => 'Bulacan',
            'Cavite' => 'Cavite',
            'Laguna' => 'Laguna',
            'Metro Manila' => 'Metro Manila',
            'Pampanga' => 'Pampanga',
            'Rizal' => 'Rizal',
            'Other' => 'Other',
        ];
    }

    /** Municipalities for the currently selected province (cascading). */
    public function getMunicipalityOptions(): array
    {
        $provinces = static::getProvinceMunicipalities();
        $options = ['' => 'Select Municipality'];

        if ($this->province !== '' && isset($provinces[$this->province])) {
            foreach ($provinces[$this->province] as $mun) {
                $options[$mun] = $mun;
            }
        }

        return $options;
    }

    public function updatedProvince(): void
    {
        $municipalities = static::getProvinceMunicipalities();
        $allowed = $this->province !== '' && isset($municipalities[$this->province])
            ? $municipalities[$this->province]
            : [];
        if ($this->municipality !== '' && ! in_array($this->municipality, $allowed, true)) {
            $this->municipality = '';
        }
    }

    public function render()
    {
        return view('livewire.auth.set-up-profile', [
            'provinceOptions' => $this->getProvinceOptions(),
            'municipalityOptions' => $this->getMunicipalityOptions(),
        ]);
    }
}
