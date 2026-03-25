<div class="space-y-6">
    @if($feedback)
    <div class="rounded-[1.35rem] border px-5 py-4 text-sm font-medium shadow-[0_18px_40px_-30px_rgba(15,23,42,0.25)] {{ $feedback['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($feedback['type'] === 'info' ? 'border-sky-200 bg-sky-50 text-sky-700' : 'border-rose-200 bg-rose-50 text-rose-700') }}">
        {{ $feedback['message'] }}
    </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <section class="rounded-[1.6rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Pending</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['pending'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Students waiting for an admin decision.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(244,244,246,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Approved</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['approved'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Students already cleared for dashboard access.</p>
        </section>

        <section class="rounded-[1.6rem] border border-[#f1d6c5] bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Rejected</p>
            <p class="mt-3 text-4xl font-black text-[#9f3f1d]">{{ $counts['rejected'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Applications returned or declined by admin review.</p>
        </section>
    </div>

    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-[#1e4fa3]">Student Approval Queue</h2>
                <p class="mt-1 text-sm text-slate-500">Approve completed student profiles, or reject pending applications with notes.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label class="block sm:w-48">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status Filter</span>
                    <select
                        wire:model.live="statusFilter"
                        class="w-full appearance-none rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="all">All Students</option>
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Search students</span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name, email, username, or code"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white sm:w-80">
                </label>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @forelse($students as $student)
            @php
            $studentName = trim(collect([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
            ])->filter()->implode(' '));
            @endphp

            <article wire:key="student-{{ $student->id }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-5 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-xl font-bold text-[#1e4fa3]">{{ $studentName !== '' ? $studentName : 'Student Profile Incomplete' }}</h3>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $student->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($student->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $student->status }}
                            </span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ $student->profile_completed ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ $student->profile_completed ? 'Profile Complete' : 'Profile Incomplete' }}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 text-sm text-slate-600 md:grid-cols-2 xl:grid-cols-4">
                            <p><span class="font-semibold text-slate-500">Email:</span> {{ $student->email }}</p>
                            <p><span class="font-semibold text-slate-500">Username:</span> {{ $student->username ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Submitted:</span> {{ optional($student->created_at)->format('M j, Y g:i A') ?: 'Unknown' }}</p>
                            <p><span class="font-semibold text-slate-500">Student Code:</span> {{ $student->student_code ?: 'Pending generation' }}</p>
                        </div>

                        @if($student->admin_notes)
                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                            <p class="font-semibold text-slate-500">Latest Admin Notes</p>
                            <p class="mt-1 leading-6">{{ $student->admin_notes }}</p>
                        </div>
                        @endif

                        @if($student->status === 'approved')
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            Approved {{ optional($student->approved_at)->format('M j, Y g:i A') ?: 'recently' }}.
                        </div>
                        @endif
                    </div>

                    <div class="w-full max-w-xl xl:w-[25rem]">
                        @if($student->status !== 'approved')
                        <label class="block text-sm font-semibold text-slate-600">
                            Admin Notes
                            <textarea
                                wire:model.blur="notes.{{ $student->id }}"
                                rows="4"
                                placeholder="Add remarks for the student or the approval decision"
                                class="mt-2 w-full rounded-[1.25rem] border border-[#d5e0f0] bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3]"></textarea>
                        </label>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-3">
                            <button
                                type="button"
                                wire:click="openProfile({{ $student->id }})"
                                class="inline-flex items-center justify-center rounded-2xl border border-[#d5e0f0] bg-white px-5 py-3 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-[#f7f9fc]"
                            >
                                View Profile
                            </button>

                            @if($student->status === 'pending')
                            <button
                                type="button"
                                wire:click="promptApprove({{ $student->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center rounded-2xl bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#173d79] disabled:cursor-not-allowed disabled:opacity-60">
                                Approve Student
                            </button>

                            <button
                                type="button"
                                wire:click="promptReject({{ $student->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-60">
                                Reject Student
                            </button>
                            @elseif($student->status === 'rejected')
                            <button
                                type="button"
                                wire:click="promptApprove({{ $student->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center rounded-2xl bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#173d79] disabled:cursor-not-allowed disabled:opacity-60">
                                Approve Instead
                            </button>
                            @else
                            <p class="text-sm text-slate-500">No further action is needed here for approved students.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
            @empty
            <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center text-sm text-slate-500">
                No students matched the current filter.
            </div>
            @endforelse
        </div>

        @if($students->hasPages())
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Showing {{ $students->firstItem() }}-{{ $students->lastItem() }} of {{ $students->total() }} students.
            </p>
            {{ $students->links() }}
        </div>
        @endif
    </section>

    @if($showConfirmationModal)
    <x-ui.modal :title="($pendingAction ?? 'approve') === 'reject' ? 'Confirm Student Rejection' : 'Confirm Student Approval'" max-width="max-w-lg">
        <x-slot:icon>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ ($pendingAction ?? 'approve') === 'reject' ? 'bg-rose-100 text-rose-600' : 'bg-[#e8f0ff] text-[#1e4fa3]' }}">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    @if(($pendingAction ?? 'approve') === 'reject')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-7.12 12.3A2 2 0 0 0 4.9 19h14.2a2 2 0 0 0 1.73-2.99l-7.12-12.3a2 2 0 0 0-3.44 0Z" />
                    @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    @endif
                </svg>
            </div>
        </x-slot:icon>

        <p class="font-medium text-slate-900">
            @if(($pendingAction ?? 'approve') === 'reject')
            You are about to reject <span class="text-rose-600">{{ $pendingStudentName }}</span>.
            @else
            You are about to approve <span class="text-[#1e4fa3]">{{ $pendingStudentName }}</span>.
            @endif
        </p>

        <p class="mt-2 text-slate-600">
            @if(($pendingAction ?? 'approve') === 'reject')
            This will keep the student out of the dashboard and save the current admin notes as the rejection reason.
            @else
            This will grant dashboard access and generate the student code if one does not exist yet.
            @endif
        </p>

        @if(($pendingAction ?? 'approve') === 'reject' && !empty($notes[$pendingStudentId ?? 0] ?? null))
        <div class="mt-4 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <p class="font-semibold">Rejection Notes</p>
            <p class="mt-1 leading-6">{{ $notes[$pendingStudentId ?? 0] }}</p>
        </div>
        @elseif(($pendingAction ?? 'approve') === 'approve' && !empty($notes[$pendingStudentId ?? 0] ?? null))
        <div class="mt-4 rounded-2xl border border-[#d7e2f5] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700">
            <p class="font-semibold text-slate-600">Approval Notes</p>
            <p class="mt-1 leading-6">{{ $notes[$pendingStudentId ?? 0] }}</p>
        </div>
        @endif

        <x-slot:actions>
            <button
                type="button"
                wire:click="closeConfirmationModal"
                class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Cancel
            </button>

            <button
                type="button"
                wire:click="confirmPendingAction"
                class="inline-flex flex-1 items-center justify-center rounded-2xl px-4 py-3 text-sm font-semibold text-white transition {{ ($pendingAction ?? 'approve') === 'reject' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#1e4fa3] hover:bg-[#173d79]' }}">
                {{ ($pendingAction ?? 'approve') === 'reject' ? 'Confirm Rejection' : 'Confirm Approval' }}
            </button>
        </x-slot:actions>
    </x-ui.modal>
    @endif

    @if($showProfileModal && $profileStudent)
        <x-ui.modal title="Student Profile" max-width="max-w-3xl">
            <x-slot:icon>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e8f0ff] text-[#1e4fa3]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 0 0-16 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
                    </svg>
                </div>
            </x-slot:icon>

            <div class="space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-base font-bold text-[#1e4fa3]">{{ $profileStudent['name'] }}</p>
                        <p class="mt-1 text-sm text-slate-500 break-words">{{ $profileStudent['email'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ ($profileStudent['status'] ?? '') === 'approved' ? 'bg-emerald-100 text-emerald-700' : ((($profileStudent['status'] ?? '') === 'rejected') ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $profileStudent['status'] }}
                        </span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ ($profileStudent['profile_completed'] ?? false) ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ ($profileStudent['profile_completed'] ?? false) ? 'Profile Complete' : 'Profile Incomplete' }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.4rem] border border-slate-200 bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Identifiers</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-500">Username:</span> {{ $profileStudent['username'] ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Student Code:</span> {{ $profileStudent['student_code'] ?: 'Pending generation' }}</p>
                            <p><span class="font-semibold text-slate-500">Registered:</span> {{ $profileStudent['created_at'] ?: 'Unknown' }}</p>
                            <p><span class="font-semibold text-slate-500">Approved At:</span> {{ $profileStudent['approved_at'] ?: '—' }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.4rem] border border-slate-200 bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Contact</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-500">Contact #:</span> {{ $profileStudent['contact_number'] ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Gender:</span> {{ $profileStudent['gender'] ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Birthdate:</span> {{ $profileStudent['date_of_birth'] ?: 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.4rem] border border-slate-200 bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">School</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-500">School:</span> {{ $profileStudent['school_attended'] ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Course:</span> {{ $profileStudent['course'] ?: 'Not set' }}</p>
                            <p><span class="font-semibold text-slate-500">Required Hours:</span> {{ $profileStudent['number_of_hours'] !== null ? $profileStudent['number_of_hours'] : 'Not set' }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.4rem] border border-slate-200 bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Address</p>
                        @php
                            $address = $profileStudent['address'] ?? [];
                            $addressLabel = trim(collect([
                                $address['street_house_number'] ?? null,
                                $address['barangay'] ?? null,
                                $address['municipality'] ?? null,
                                $address['province'] ?? null,
                            ])->filter()->implode(', '));
                        @endphp
                        <p class="mt-3 text-sm text-slate-600">{{ $addressLabel !== '' ? $addressLabel : 'Not set' }}</p>
                    </div>
                </div>

                @if(!empty($profileStudent['admin_notes']))
                    <div class="rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Latest Admin Notes</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $profileStudent['admin_notes'] }}</p>
                    </div>
                @endif
            </div>

            <x-slot:actions>
                <button
                    type="button"
                    wire:click="closeProfileModal"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                >
                    Close
                </button>
            </x-slot:actions>
        </x-ui.modal>
    @endif
</div>