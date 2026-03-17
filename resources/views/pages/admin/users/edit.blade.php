<x-admin.layouts.dashboard :title="'Edit User - ' . ($managedUser->first_name ?: $managedUser->email)" active="users">
        @php
        $address = is_array($managedUser->address) ? $managedUser->address : [];
        $managedUserName = trim(collect([$managedUser->first_name, $managedUser->middle_name, $managedUser->last_name])->filter()->implode(' '));
        @endphp

        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">User Management</p>
                        <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Edit
                                {{ $managedUserName !== '' ? $managedUserName : 'User Profile' }}
                        </h1>
                        <p class="mt-3 max-w-3xl text-sm text-slate-600">Update profile fields, role assignment, and
                                account
                                metadata from one form.</p>
                </div>

                <a href="{{ route('admin.users.show', $managedUser) }}"
                     wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] sm:w-fit">Cancel</a>
        </div>

        <form action="{{ route('admin.users.update', $managedUser) }}" method="POST"
                class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
                @csrf
                @method('PATCH')
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <div><label for="managed-user-username"
                                        class="block text-sm font-semibold text-slate-600">Username</label><input
                                        id="managed-user-username" name="username" type="text"
                                        value="{{ old('username', $managedUser->username) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('username')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-email"
                                        class="block text-sm font-semibold text-slate-600">Email</label><input
                                        id="managed-user-email" name="email" type="email"
                                        value="{{ old('email', $managedUser->email) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('email')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-role"
                                        class="block text-sm font-semibold text-slate-600">Role</label><select
                                        id="managed-user-role" name="role"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">
                                        <option value="student" @selected(old('role', $managedUser->role) === 'student')>
                                                Student</option>
                                        <option value="admin" @selected(old('role', $managedUser->role) === 'admin')>Admin
                                        </option>
                                </select>@error('role')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-position"
                                        class="block text-sm font-semibold text-slate-600">Position</label><input
                                        id="managed-user-position" name="position" type="text"
                                        value="{{ old('position', $managedUser->position ?: 'OJT Trainee') }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('position')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-first-name"
                                        class="block text-sm font-semibold text-slate-600">First
                                        Name</label><input id="managed-user-first-name" name="first_name" type="text"
                                        value="{{ old('first_name', $managedUser->first_name) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('first_name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-middle-name"
                                        class="block text-sm font-semibold text-slate-600">Middle
                                        Name</label><input id="managed-user-middle-name" name="middle_name" type="text"
                                        value="{{ old('middle_name', $managedUser->middle_name) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('middle_name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-last-name" class="block text-sm font-semibold text-slate-600">Last
                                        Name</label><input id="managed-user-last-name" name="last_name" type="text"
                                        value="{{ old('last_name', $managedUser->last_name) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('last_name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-gender"
                                        class="block text-sm font-semibold text-slate-600">Gender</label><select
                                        id="managed-user-gender" name="gender"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">
                                        <option value="">Select</option>@foreach(['Male', 'Female', 'Other'] as $gender)
                                        <option value="{{ $gender }}" @selected(old('gender', $managedUser->gender) === $gender)>{{ $gender }}
                                        </option>@endforeach
                                </select>@error('gender')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror</div>
                        <div><label for="managed-user-date-of-birth"
                                        class="block text-sm font-semibold text-slate-600">Date of
                                        Birth</label><input id="managed-user-date-of-birth" name="date_of_birth"
                                        type="date"
                                        value="{{ old('date_of_birth', optional($managedUser->date_of_birth)->format('Y-m-d')) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('date_of_birth')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-contact-number"
                                        class="block text-sm font-semibold text-slate-600">Contact
                                        Number</label><input id="managed-user-contact-number" name="contact_number"
                                        type="text" value="{{ old('contact_number', $managedUser->contact_number) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('contact_number')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-school-attended"
                                        class="block text-sm font-semibold text-slate-600">School
                                        Attended</label><input id="managed-user-school-attended" name="school_attended"
                                        type="text" value="{{ old('school_attended', $managedUser->school_attended) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('school_attended')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-course"
                                        class="block text-sm font-semibold text-slate-600">Course</label><input
                                        id="managed-user-course" name="course" type="text"
                                        value="{{ old('course', $managedUser->course) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('course')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-number-of-hours"
                                        class="block text-sm font-semibold text-slate-600">Required
                                        Hours</label><input id="managed-user-number-of-hours" name="number_of_hours"
                                        type="number" min="0"
                                        value="{{ old('number_of_hours', $managedUser->number_of_hours) }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('number_of_hours')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-province"
                                        class="block text-sm font-semibold text-slate-600">Province</label><input
                                        id="managed-user-province" name="province" type="text"
                                        value="{{ old('province', $address['province'] ?? '') }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('province')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-municipality"
                                        class="block text-sm font-semibold text-slate-600">Municipality</label><input
                                        id="managed-user-municipality" name="municipality" type="text"
                                        value="{{ old('municipality', $address['municipality'] ?? $address['city'] ?? '') }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('municipality')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="managed-user-barangay"
                                        class="block text-sm font-semibold text-slate-600">Barangay</label><input
                                        id="managed-user-barangay" name="barangay" type="text"
                                        value="{{ old('barangay', $address['barangay'] ?? '') }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('barangay')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="xl:col-span-2"><label for="managed-user-street-house-number"
                                        class="block text-sm font-semibold text-slate-600">Street / House
                                        Number</label><input id="managed-user-street-house-number"
                                        name="street_house_number" type="text"
                                        value="{{ old('street_house_number', $address['street_house_number'] ?? '') }}"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">@error('street_house_number')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="xl:col-span-3"><label for="managed-user-admin-notes"
                                        class="block text-sm font-semibold text-slate-600">Admin Notes</label><textarea
                                        id="managed-user-admin-notes" name="admin_notes" rows="4"
                                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">{{ old('admin_notes', $managedUser->admin_notes) }}</textarea>@error('admin_notes')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <label
                                class="flex items-center gap-3 rounded-[1.25rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4 text-sm font-semibold text-slate-700"><input
                                        type="hidden" name="profile_completed" value="0"><input type="checkbox"
                                        name="profile_completed" value="1" @checked(old('profile_completed', $managedUser->profile_completed))
                                class="h-4 w-4 rounded border-slate-300 text-[#1e4fa3] focus:ring-[#1e4fa3]">Profile
                                completed</label>
                        <label
                                class="flex items-center gap-3 rounded-[1.25rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4 text-sm font-semibold text-slate-700"><input
                                        type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active"
                                        value="1" @checked(old('is_active', $managedUser->is_active))
                                class="h-4 w-4 rounded border-slate-300 text-[#1e4fa3] focus:ring-[#1e4fa3]">Mark
                                attendance state
                                as active</label>
                </div>

                <div
                        class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:flex-wrap sm:justify-end">
                        <a href="{{ route('admin.users.show', $managedUser) }}"
                                wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">Cancel</a>
                        <button type="submit"
                                wire:click="updateUser" class="inline-flex items-center justify-center rounded-full border border-[#1e4fa3] bg-[#1e4fa3] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#173d79]">Save
                                User Changes</button>
                </div>
        </form>
</x-admin.layouts.dashboard>