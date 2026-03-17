<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-[1.6rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">All Users</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['all'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Every account in the system.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(244,244,246,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Students</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['students'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Accounts under internship attendance tracking.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Admins</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['admins'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Users with admin dashboard access.</p>
        </section>

        <section class="rounded-[1.6rem] border border-[#f1d6c5] bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Pending</p>
            <p class="mt-3 text-4xl font-black text-[#9f3f1d]">{{ $counts['pending'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Accounts still waiting for approval.</p>
        </section>
    </div>

    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">All Users</h2>
                <p class="mt-1 text-sm text-slate-500">Filter by role, status, or profile completion, then drill into any account.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</span>
                    <select wire:model.live="roleFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="all">All Roles</option>
                        <option value="admin">Admins</option>
                        <option value="student">Students</option>
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</span>
                    <select wire:model.live="statusFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Profile</span>
                    <select wire:model.live="profileFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="all">All Profiles</option>
                        <option value="complete">Complete</option>
                        <option value="incomplete">Incomplete</option>
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Search users</span>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</span>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Name, email, username, code"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white">
                </label>
            </div>
        </div>

        <div class="mt-6 space-y-4 lg:hidden">
            @forelse($users as $user)
            @php
            $userName = trim(collect([$user->first_name, $user->middle_name, $user->last_name])->filter()->implode(' '));
            @endphp
            <article wire:key="managed-user-card-{{ $user->id }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-4 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-base font-bold text-[#1e4fa3]">{{ $userName !== '' ? $userName : 'Profile Incomplete' }}</p>
                        <p class="mt-1 text-sm text-slate-500 break-words">{{ $user->email }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $user->username ?: 'No username' }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                            Student Code: <span class="font-bold text-slate-500">{{ $user->student_code ?: 'Pending generation' }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-[#e8f0ff] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">{{ $user->role }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $user->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($user->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ $user->status }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $user->profile_completed ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600' }}">{{ $user->profile_completed ? 'Complete' : 'Incomplete' }}</span>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.users.show', $user) }}" wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-white">Edit</a>
                    </div>
                </div>
            </article>
            @empty
            <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center text-sm text-slate-500">No users matched the current filters.</div>
            @endforelse
        </div>

        <div class="mt-6 hidden overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-4 py-4">Student Code</th>
                            <th class="px-4 py-4">User</th>
                            <th class="px-4 py-4">Role</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4">Profile</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-600">
                        @forelse($users as $user)
                        @php
                        $userName = trim(collect([$user->first_name, $user->middle_name, $user->last_name])->filter()->implode(' '));
                        @endphp
                        <tr wire:key="managed-user-{{ $user->id }}">
                            <td class="px-4 py-4 align-top">
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600"> <span class="font-bold text-slate-500">{{ $user->student_code ?: 'Pending generation' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-[#1e4fa3]">{{ $userName !== '' ? $userName : 'Profile Incomplete' }}</p>
                                <p class="mt-1 text-slate-500">{{ $user->email }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $user->username ?: 'No username' }}</p>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="rounded-full bg-[#e8f0ff] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">{{ $user->role }}</span>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $user->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($user->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ $user->status }}</span>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $user->profile_completed ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600' }}">{{ $user->profile_completed ? 'Complete' : 'Incomplete' }}</span>
                            </td>
                            <td class="px-4 py-4 align-top text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" wire:navigate class="inline-flex rounded-full border border-[#d5e0f0] bg-white px-3 py-2 text-xs font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">View</a>
                                        <a href="{{ route('admin.users.edit', $user) }}" wire:navigate class="inline-flex rounded-full border border-[#d5e0f0] bg-[#f7f9fc] px-3 py-2 text-xs font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-white">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-sm text-slate-500">No users matched the current filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-center text-sm text-slate-500 sm:text-left">Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} users.</p>
            {{ $users->links() }}
        </div>
        @endif
    </section>
</div>