<x-admin.layouts.dashboard :title="'User Details - ' . ($managedUser->first_name ?: $managedUser->email)"
    active="users">
    @if(session('admin_notice'))
        <div
            class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700 shadow-[0_18px_40px_-30px_rgba(16,185,129,0.7)]">
            {{ session('admin_notice') }}
        </div>
    @endif

    @php
        $address = is_array($managedUser->address) ? $managedUser->address : [];
        $managedUserName = trim(collect([$managedUser->first_name, $managedUser->middle_name, $managedUser->last_name])->filter()->implode(' '));
    @endphp

    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">User Management</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">
                {{ $managedUserName !== '' ? $managedUserName : 'Profile Incomplete' }}</h1>
            <p class="mt-3 max-w-3xl text-sm text-slate-600">Inspect account details, update profile data, or change
                approval status from this screen.</p>
        </div>

        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">Back
                to Users</a>
            <a href="{{ route('admin.users.edit', $managedUser) }}"
                class="inline-flex items-center justify-center rounded-full border border-[#1e4fa3] bg-[#1e4fa3] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#173d79]">Edit
                User</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.9fr)]">
        <section
            class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Email</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">{{ $managedUser->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Username</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">{{ $managedUser->username ?: 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Role</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">{{ ucfirst($managedUser->role) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Status</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">{{ ucfirst($managedUser->status) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Contact Number</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ $managedUser->contact_number ?: 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">School Attended</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ $managedUser->school_attended ?: 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Required Hours</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">{{ $managedUser->number_of_hours ?: 0 }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Student Code</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ $managedUser->student_code ?: 'Pending generation' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Date of Birth</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ optional($managedUser->date_of_birth)->format('M j, Y') ?: 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Profile State</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ $managedUser->profile_completed ? 'Complete' : 'Incomplete' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Active Attendance Flag
                    </p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ $managedUser->is_active ? 'Active now' : 'Inactive' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Last Seen</p>
                    <p class="mt-2 text-base font-semibold text-slate-700">
                        {{ optional($managedUser->last_seen_at)->format('M j, Y g:i A') ?: 'Not recorded' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Address</p>
                <div class="mt-3 grid gap-4 md:grid-cols-2">
                    <p class="text-sm text-slate-600"><span class="font-semibold text-slate-500">Province:</span>
                        {{ $address['province'] ?? 'Not provided' }}</p>
                    <p class="text-sm text-slate-600"><span class="font-semibold text-slate-500">Municipality:</span>
                        {{ $address['municipality'] ?? $address['city'] ?? 'Not provided' }}</p>
                    <p class="text-sm text-slate-600"><span class="font-semibold text-slate-500">Barangay:</span>
                        {{ $address['barangay'] ?? 'Not provided' }}</p>
                    <p class="text-sm text-slate-600"><span class="font-semibold text-slate-500">Street / House:</span>
                        {{ $address['street_house_number'] ?? 'Not provided' }}</p>
                </div>
            </div>
        </section>

        <section
            class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
            <div class="border-b border-[#f1c74a] pb-4">
                <h2 class="text-2xl font-bold text-[#1e4fa3]">Status Actions</h2>
                <p class="mt-1 text-sm text-slate-500">Update the account lifecycle without leaving the detail page.</p>
            </div>

            <form action="{{ route('admin.users.status', $managedUser) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="managed-user-status" class="block text-sm font-semibold text-slate-600">New
                        Status</label>
                    <select id="managed-user-status" name="status"
                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">
                        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $managedUser->status) === $value)>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="managed-user-admin-notes" class="block text-sm font-semibold text-slate-600">Admin
                        Notes</label>
                    <textarea id="managed-user-admin-notes" name="admin_notes" rows="5"
                        class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3]">{{ old('admin_notes', $managedUser->admin_notes) }}</textarea>
                    @error('admin_notes')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#173d79]">Save
                    Status Change</button>
            </form>

            <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-5 text-sm text-slate-600">
                <p class="font-semibold text-slate-700">Approval Metadata</p>
                <p class="mt-3"><span class="font-semibold text-slate-500">Approved By:</span>
                    {{ $managedUser->approvedBy?->first_name ? trim($managedUser->approvedBy->first_name . ' ' . $managedUser->approvedBy->last_name) : 'Not recorded' }}
                </p>
                <p class="mt-2"><span class="font-semibold text-slate-500">Approved At:</span>
                    {{ optional($managedUser->approved_at)->format('M j, Y g:i A') ?: 'Not recorded' }}</p>
                <p class="mt-2"><span class="font-semibold text-slate-500">Current Notes:</span>
                    {{ $managedUser->admin_notes ?: 'No notes yet.' }}</p>
            </div>
        </section>
    </div>
</x-admin.layouts.dashboard>