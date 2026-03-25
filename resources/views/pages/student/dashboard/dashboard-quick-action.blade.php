@php
    // Visual styles are grouped by tone so the markup stays readable below.
    $toneStyles = [
        'primary' => [
            'button' => 'bg-[#1e4fa3] text-white shadow-[0_20px_40px_-20px_rgba(30,79,163,0.85)] hover:bg-[#173f84]',
            'badge' => 'border border-[#c8d7f4] bg-white/85 text-[#1e4fa3]',
            'panel' => 'border-[#d7e3f8] bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(223,234,249,0.95))]',
        ],
        'danger' => [
            'button' => 'bg-[#c64343] text-white shadow-[0_20px_40px_-20px_rgba(198,67,67,0.85)] hover:bg-[#a93333]',
            'badge' => 'border border-[#f2c6c6] bg-white/85 text-[#a93333]',
            'panel' => 'border-[#f1d6d6] bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(252,235,235,0.95))]',
        ],
        'warning' => [
            'button' => 'bg-[#d79a24] text-white shadow-[0_20px_40px_-20px_rgba(215,154,36,0.85)] hover:bg-[#b88318]',
            'badge' => 'border border-[#f4dfaf] bg-white/85 text-[#9b680d]',
            'panel' => 'border-[#f3e2bd] bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(253,245,223,0.95))]',
        ],
        'slate' => [
            'button' => 'bg-[#24435f] text-white shadow-[0_20px_40px_-20px_rgba(36,67,95,0.9)] hover:bg-[#1c3449]',
            'badge' => 'border border-[#c8d5df] bg-white/85 text-[#24435f]',
            'panel' => 'border-[#d5dee5] bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(229,236,242,0.95))]',
        ],
        'success' => [
            'button' => 'bg-[#2c7a57] text-white shadow-[0_20px_40px_-20px_rgba(44,122,87,0.85)] hover:bg-[#225f44]',
            'badge' => 'border border-[#cce7d9] bg-white/85 text-[#225f44]',
            'panel' => 'border-[#d7eadf] bg-[linear-gradient(135deg,rgba(255,255,255,0.98),rgba(229,244,236,0.96))]',
        ],
    ];

    // Pick the correct style set based on the state produced in PHP.
    $styles = $toneStyles[$state['tone'] ?? 'primary'] ?? $toneStyles['primary'];
@endphp

<div>
    {{--
        Floating attendance widget.

        Desktop:
            shows an explanation panel above the round button

        Mobile:
            shows only the round action button to save space
    --}}
    <div
        class="fixed bottom-5 right-5 z-40 sm:bottom-8 sm:right-8 lg:bottom-10 lg:right-10"
        x-data="{
            locating: false,
            captureCurrentLocation() {
                if (this.locating) {
                    return;
                }

                if (! navigator.geolocation) {
                    this.reportFailure('This device does not support GPS location. Please use a device with location access enabled.');

                    return;
                }

                this.locating = true;

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        $wire.captureLocation(position.coords.latitude, position.coords.longitude)
                            .finally(() => {
                                this.locating = false;
                            });
                    },
                    (error) => {
                        this.reportFailure(this.locationMessage(error));
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    }
                );
            },
            reportFailure(message) {
                $wire.locationFailed(message)
                    .finally(() => {
                        this.locating = false;
                    });
            },
            locationMessage(error) {
                if (error?.code === 1) {
                    return 'Location access was denied. Please allow GPS access and try again.';
                }

                if (error?.code === 2) {
                    return 'Your location is currently unavailable. Move to an area with a stronger signal and try again.';
                }

                if (error?.code === 3) {
                    return 'Reading your current location took too long. Please try again.';
                }

                return 'We could not read your current location. Please allow location access and try again.';
            }
        }"
    >
        {{-- The helper panel keeps the new two-step flow readable on both mobile and desktop. --}}
        <div class="mb-3 w-[min(20rem,calc(100vw-2.5rem))] rounded-3xl border px-4 py-4 shadow-[0_28px_55px_-35px_rgba(15,23,42,0.7)] {{ $styles['panel'] }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Attendance Action</p>
                    <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $state['label'] ?? 'Attendance' }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $state['description'] ?? 'Record your attendance update.' }}</p>
                </div>
                <span class="inline-flex min-w-24 justify-center rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $styles['badge'] }}">
                    {{ ($state['isComplete'] ?? false) ? 'Complete' : 'Ready' }}
                </span>
            </div>

            @if(! ($state['isComplete'] ?? false))
                <div class="mt-4 rounded-2xl border border-[#d7e2f5] bg-white/70 px-3 py-3 text-sm text-slate-600">
                    <p class="font-semibold text-[#1e4fa3]">How to log attendance</p>
                    <p class="mt-2">Step 1: Read your current GPS location.</p>
                    <p class="mt-1">Step 2: Use {{ strtolower($state['label'] ?? 'attendance') }} after GPS is ready.</p>
                </div>

                <div class="mt-3 rounded-2xl border border-[#d7e2f5] bg-[#f7f9fc] px-3 py-3 text-sm text-slate-600">
                    <p class="font-semibold text-[#1e4fa3]">WFH timeout rule</p>
                    <p class="mt-2">If your day is classified as WFH, time out must stay within {{ $wfhAnchorLimitMeters }} meters of your original time-in location.</p>
                </div>

                @if($capturedLatitude !== null && $capturedLongitude !== null)
                    <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-sm text-emerald-700">
                        <p class="font-semibold">GPS is ready for this attendance action.</p>
                        <p class="mt-1">Captured at {{ $capturedLocationLabel ?? 'just now' }}. You can now continue with {{ strtolower($state['label'] ?? 'attendance') }}.</p>
                    </div>
                @else
                    <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-3 py-3 text-sm text-amber-700">
                        <p class="font-semibold">GPS has not been read yet.</p>
                        <p class="mt-1">Read your location first before the attendance action becomes available.</p>
                    </div>
                @endif
            @endif

            {{-- Livewire validation or business-rule error. --}}
            @error('attendance')
                <p class="mt-3 rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </div>

        {{-- When the day is complete, replace the action button with a status icon. --}}
        @if($state['isComplete'] ?? false)
            <div class="flex h-18 w-18 items-center justify-center rounded-full border border-white/70 bg-white text-[#2c7a57] shadow-[0_22px_45px_-20px_rgba(15,23,42,0.55)] sm:h-20 sm:w-20">
                <svg class="h-9 w-9 sm:h-10 sm:w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3" />
                </svg>
            </div>
        @else
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-full border border-[#d5e0f0] bg-white px-4 py-3 text-sm font-semibold text-[#1e4fa3] shadow-[0_18px_35px_-24px_rgba(15,23,42,0.55)] transition hover:border-[#1e4fa3] hover:bg-[#f7f9fc]"
                    x-on:click="captureCurrentLocation()"
                    x-bind:disabled="locating"
                    wire:loading.attr="disabled"
                    wire:target="captureLocation,locationFailed"
                >
                    <span x-show="!locating" wire:loading.remove wire:target="captureLocation,locationFailed">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 12h3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                        </svg>
                    </span>
                    <svg x-cloak x-show="locating" class="h-5 w-5 animate-spin text-current" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                    </svg>
                    <span x-text="locating ? 'Reading GPS...' : 'Read Current GPS'"></span>
                </button>

                <button
                    type="button"
                    class="group inline-flex items-center justify-center gap-2 rounded-full border border-white/70 px-4 py-3 text-sm font-semibold transition duration-200 {{ $styles['button'] }} {{ $capturedLatitude === null || $capturedLongitude === null ? 'cursor-not-allowed opacity-50' : '' }}"
                    wire:click="openConfirmation"
                    @disabled($capturedLatitude === null || $capturedLongitude === null)
                    wire:loading.attr="disabled"
                    wire:target="openConfirmation,mark"
                    aria-label="{{ $state['label'] ?? 'Record attendance' }}"
                    title="{{ $state['label'] ?? 'Record attendance' }}"
                >
                    @switch($state['icon'] ?? 'sunrise')
                        @case('lunch-out')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 4v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 12v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 4c1.657 0 3 1.79 3 4s-1.343 4-3 4V4Z" />
                            </svg>
                            @break
                        @case('lunch-in')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 4v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 12v8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 4c1.657 0 3 1.79 3 4s-1.343 4-3 4V4Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 9l3 3-3 3" />
                            </svg>
                            @break
                        @case('sunset')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16h16" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a5 5 0 0 1 10 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20h14" />
                            </svg>
                            @break
                        @default
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16h16" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a5 5 0 0 1 10 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 8l4-4 4 4" />
                            </svg>
                    @endswitch

                    <span wire:loading.remove wire:target="openConfirmation,mark">{{ $state['label'] ?? 'Log Attendance' }}</span>
                    <span wire:loading wire:target="openConfirmation,mark">Please wait...</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Confirmation modal for attendance actions. --}}
    @if($showConfirmationModal)
        <x-ui.modal :title="$state['confirmationTitle'] ?? 'Attendance Confirmation'" max-width="max-w-md">
        <x-slot:icon>
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-[#d5e0f2] bg-[#eef4ff] text-[#1e4fa3]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8h.01M11 12h1v4h1" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </x-slot:icon>

        <p class="font-medium text-[#234880]">{{ $state['confirmText'] ?? 'Confirm this attendance action?' }}</p>
        <p class="mt-2 text-slate-600">Kindly press confirm to record your {{ strtolower($state['label'] ?? 'attendance') }} entry.</p>
        @if($pendingLatitude !== null && $pendingLongitude !== null)
            <p class="mt-3 rounded-2xl border border-[#d7e2f5] bg-[#f7f9fc] px-3 py-2 text-sm text-[#1e4fa3]">Current GPS location was captured successfully and will be attached to this attendance log.</p>
        @endif
        @if($actionTimeLabel)
            <p class="mt-3 border-t border-slate-200 pt-3 text-sm font-semibold text-slate-500">Recorded time: {{ $actionTimeLabel }}</p>
        @endif

        @error('attendance')
            <p class="mt-3 rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <x-slot:actions>
            <button
                type="button"
                wire:click="closeConfirmation"
                class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="mark"
                wire:loading.attr="disabled"
                wire:target="mark"
                class="flex-1 rounded-xl bg-[#1e4fa3] px-4 py-2.5 font-medium text-white shadow transition hover:bg-[#173f84]"
            >
                <span wire:loading.remove wire:target="mark">Confirm</span>
                <span wire:loading wire:target="mark">Saving...</span>
            </button>
        </x-slot:actions>
        </x-ui.modal>
    @endif

    {{-- Friendly modal shown when browser location cannot be captured. --}}
    @if($showLocationErrorModal)
        <x-ui.modal title="Location Required" max-width="max-w-md">
        <x-slot:icon>
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-amber-200 bg-amber-50 text-amber-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4Z" />
                </svg>
            </div>
        </x-slot:icon>

        <p class="font-medium text-[#234880]">Attendance needs your current GPS location first.</p>
        <p class="mt-2 text-slate-600">{{ $locationErrorMessage }}</p>

        <x-slot:actions>
            <button
                type="button"
                wire:click="closeLocationError"
                class="w-full rounded-xl bg-[#1e4fa3] px-4 py-2.5 font-medium text-white shadow transition hover:bg-[#173f84]"
            >
                Try Again
            </button>
        </x-slot:actions>
        </x-ui.modal>
    @endif

    {{-- Success modal shown after a successful attendance action. --}}
    @if($showSuccessModal)
        <x-ui.modal :title="$successState['title'] ?? 'Attendance Saved'" max-width="max-w-md">
        <x-slot:icon>
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </x-slot:icon>

        <p class="font-medium text-[#234880]">{{ $successState['description'] ?? 'Your attendance record has been saved successfully.' }}</p>
        @if($actionTimeLabel)
            <p class="mt-3 border-t border-slate-200 pt-3 text-sm font-semibold text-slate-500">Logged at {{ $actionTimeLabel }}</p>
        @endif

        <x-slot:actions>
            <button
                type="button"
                wire:click="closeSuccess"
                class="w-full rounded-xl bg-[#1e4fa3] px-4 py-2.5 font-medium text-white shadow transition hover:bg-[#173f84]"
            >
                Alright
            </button>
        </x-slot:actions>
        </x-ui.modal>
    @endif
</div>