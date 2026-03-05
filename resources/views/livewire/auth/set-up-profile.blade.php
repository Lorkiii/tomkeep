<div>
<x-ojt-card heading="Set Up Your Profile" maxWidth="max-w-md" headingClass="text-slate-500 font-medium">
    <form wire:submit="openConfirmation" class="space-y-3">
        <div>
            <label for="profile-first-name" class="block text-sm font-medium text-slate-700">First Name <span class="text-red-500">*</span></label>
            <input
                id="profile-first-name"
                type="text"
                wire:model="first_name"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="First Name"
            />
            @error('first_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-middle-name" class="block text-sm font-medium text-slate-700">Middle Name <span class="text-red-500">*</span></label>
            <input
                id="profile-middle-name"
                type="text"
                wire:model="middle_name"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="Middle Name"
            />
            @error('middle_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-last-name" class="block text-sm font-medium text-slate-700">Surname <span class="text-red-500">*</span></label>
            <input
                id="profile-last-name"
                type="text"
                wire:model="last_name"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="Surname"
            />
            @error('last_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-gender" class="block text-sm font-medium text-slate-700">Gender <span class="text-red-500">*</span></label>
            <select
                id="profile-gender"
                wire:model="gender"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
            >
                <option value="">Select gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-date-of-birth" class="block text-sm font-medium text-slate-700">Birthday</label>
            <input
                id="profile-date-of-birth"
                type="text"
                wire:model="date_of_birth"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="MM/DD/YYYY"
            />
            @error('date_of_birth')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <h3 class="mb-2 text-sm font-bold text-slate-800">Address</h3>
            <div class="space-y-3">
                <div>
                    <select
                        id="profile-province"
                        wire:model.live="province"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 px-4 text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                    >
                        @foreach($provinceOptions ?? [] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Province</p>
                    @error('province')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="profile-municipality" class="sr-only">Municipality</label>
                    <select
                        id="profile-municipality"
                        wire:model="municipality"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 px-4 text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 disabled:bg-slate-100 disabled:text-slate-500"
                        @if($province === '' || $province === null)
                            disabled
                        @endif
                    >
                        @foreach($municipalityOptions ?? [] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Municipality</p>
                    @if($province === '' || $province === null)
                        <p class="mt-0.5 text-xs text-amber-600">Select a province first to see municipalities.</p>
                    @endif
                    @error('municipality')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input
                        id="profile-street"
                        type="text"
                        wire:model="street"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                        placeholder="e.g. Main Street, Rizal Avenue"
                    />
                    <p class="mt-1 text-xs text-slate-500">Street</p>
                    @error('street')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input
                        id="profile-house-number"
                        type="text"
                        wire:model="house_number"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                        placeholder="e.g. 123, Bldg. A Unit 5"
                    />
                    <p class="mt-1 text-xs text-slate-500">House Number</p>
                    @error('house_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input
                        id="profile-postal-code"
                        type="text"
                        wire:model="postal_code"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                        placeholder="e.g. 3000, 1000"
                    />
                    <p class="mt-1 text-xs text-slate-500">Postal / Zip Code</p>
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div>
            <label for="profile-required-hours" class="block text-sm font-medium text-slate-700">No. of Hours <span class="text-red-500">*</span></label>
            <input
                id="profile-required-hours"
                type="number"
                wire:model="required_hours"
                min="1"
                max="9999"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="e.g. 350"
            />
            @error('required_hours')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-contact-number" class="block text-sm font-medium text-slate-700">Contact Number <span class="text-red-500">*</span></label>
            <input
                id="profile-contact-number"
                type="text"
                wire:model="contact_number"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="e.g. 09920003349"
            />
            @error('contact_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-school-attended" class="block text-sm font-medium text-slate-700">University Attended <span class="text-red-500">*</span></label>
            <input
                id="profile-school-attended"
                type="text"
                wire:model="school_attended"
                class="mt-1 w-full rounded-xl border border-slate-200 py-2.5 px-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                placeholder="e.g. National University Bulacan Inc."
            />
            @error('school_attended')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button
                type="submit"
                style="background-color: #1f4082;"
                class="w-full rounded-xl px-4 py-3 font-medium font-semibold text-white shadow transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#1f4082] focus:ring-offset-2"
            >
                Submit
            </button>
        </div>
    </form>
</x-ojt-card>

    {{-- Confirmation modal --}}
    @if($showConfirmation)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="confirmation-title">
            <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
                <h3 id="confirmation-title" class="text-lg font-bold text-slate-900">Confirmation</h3>
                <div class="mt-4 flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[#1e3a5f]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-700">Are you sure all the information provided is correct?</p>
                        <p class="mt-2 text-sm font-medium text-amber-700">Number of hours cannot be changed later.</p>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        wire:click="closeConfirmation"
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                    >
                        Wait
                    </button>
                    <button
                        type="button"
                        wire:click="submitProfile"
                        class="flex-1 rounded-xl bg-[#1e3a5f] px-4 py-2.5 font-medium text-white shadow transition hover:bg-[#152a47] focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:ring-offset-2"
                    >
                        Yes
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Success modal --}}
    @if($showSuccess)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="success-title">
            <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
                <h3 id="success-title" class="text-lg font-bold text-slate-900">Account Created</h3>
                <div class="mt-4 flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-slate-900">Account Successfully Created!</p>
                        <p class="mt-1 text-sm text-slate-600">The system received your account creation request. Please wait for confirmation message on your email.</p>
                    </div>
                </div>
                <div class="mt-6">
                    <button
                        type="button"
                        wire:click="closeSuccess"
                        class="w-full rounded-xl bg-[#1e3a5f] px-4 py-2.5 font-medium text-white shadow transition hover:bg-[#152a47] focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:ring-offset-2"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-guest-footer />
</div>
