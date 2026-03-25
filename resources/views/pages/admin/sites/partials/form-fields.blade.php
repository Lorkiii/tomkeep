@php
    $siteAddress = $managedSite?->address ?? [];
    $latitude = old('latitude', $siteCoordinates['latitude'] ?? '');
    $longitude = old('longitude', $siteCoordinates['longitude'] ?? '');
    $isActiveValue = old('is_active', isset($managedSite) ? (int) $managedSite->is_active : 1);
    $enforceGeofenceValue = old('enforce_geofence', isset($managedSite) ? (int) $managedSite->enforce_geofence : 1);
    $wfhAnchorEnforcedValue = old('wfh_anchor_enforced', isset($managedSite) ? (int) ($managedSite->wfh_anchor_enforced ?? true) : 1);
    $wfhAnchorLimitValue = old('wfh_anchor_limit_m', isset($managedSite) ? ($managedSite->wfh_anchor_limit_m ?? 20) : 20);
@endphp

@if($errors->any())
    <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
        <p class="font-semibold">Please review the highlighted site details before saving.</p>
    </div>
@endif

<div
    x-data="{
        latitude: @js((string) $latitude),
        longitude: @js((string) $longitude),
        streetAddress: @js((string) old('street_address', $siteAddress['street_address'] ?? '')),
        barangay: @js((string) old('barangay', $siteAddress['barangay'] ?? '')),
        municipality: @js((string) old('municipality', $siteAddress['municipality'] ?? '')),
        province: @js((string) old('province', $siteAddress['province'] ?? '')),
        geofenceEnabled: @js((string) $enforceGeofenceValue === '1'),
        wfhAnchorEnforced: @js((string) $wfhAnchorEnforcedValue === '1'),
        wfhAnchorLimit: @js((string) $wfhAnchorLimitValue),
        helperBusy: false,
        helperMessage: '',
        async useCurrentLocation() {
            if (this.helperBusy) {
                return;
            }

            if (! navigator.geolocation) {
                this.helperMessage = 'This browser cannot read your current location.';

                return;
            }

            this.helperBusy = true;
            this.helperMessage = 'Reading the current GPS location...';

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    this.latitude = position.coords.latitude.toFixed(7);
                    this.longitude = position.coords.longitude.toFixed(7);
                    this.helperMessage = 'Coordinates captured. Loading address suggestions...';
                    await this.fillAddressFromCoordinates();
                },
                (error) => {
                    this.helperBusy = false;
                    this.helperMessage = this.locationErrorMessage(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        },
        async fillAddressFromCoordinates() {
            if (this.latitude === '' || this.longitude === '') {
                this.helperMessage = 'Set the latitude and longitude first, then auto-fill the address.';

                return;
            }

            this.helperBusy = true;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(this.latitude)}&lon=${encodeURIComponent(this.longitude)}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (! response.ok) {
                    throw new Error('reverse lookup failed');
                }

                const payload = await response.json();
                const address = payload.address || {};
                const houseNumber = address.house_number || '';
                const road = address.road || address.pedestrian || address.residential || address.neighbourhood || '';

                this.streetAddress = [houseNumber, road].filter(Boolean).join(' ').trim() || this.streetAddress;
                this.barangay = address.suburb || address.village || address.hamlet || address.neighbourhood || this.barangay;
                this.municipality = address.city || address.town || address.municipality || address.county || this.municipality;
                this.province = address.state || address.region || this.province;
                this.helperMessage = 'Coordinates and address suggestions were loaded. Review the fields before saving.';
            } catch (error) {
                this.helperMessage = 'Coordinates were captured, but the address lookup could not be completed. You can still edit the address manually.';
            } finally {
                this.helperBusy = false;
            }
        },
        locationErrorMessage(error) {
            if (error?.code === 1) {
                return 'Location access was denied. Allow GPS access and try again.';
            }

            if (error?.code === 2) {
                return 'The current location is unavailable. Move to an open area and try again.';
            }

            if (error?.code === 3) {
                return 'Reading the current location took too long. Please try again.';
            }

            return 'The current location could not be read. Please try again.';
        }
    }"
    class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.85fr)]"
>
    {{-- General site identity and address live together because admins usually edit them at the same time. --}}
    <section
        class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="border-b border-[#f1c74a] pb-4">
            <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Site Information</h2>
            <p class="mt-1 text-sm text-slate-500">Store the company label and human-readable address the admin team
                will recognize immediately.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <button type="button" x-on:click="useCurrentLocation()"
                    class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] disabled:cursor-not-allowed disabled:opacity-60"
                    x-bind:disabled="helperBusy">
                    Use Current Location
                </button>
                <button type="button" x-on:click="fillAddressFromCoordinates()"
                    class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-white disabled:cursor-not-allowed disabled:opacity-60"
                    x-bind:disabled="helperBusy">
                    Auto-Fill Address
                </button>
            </div>
            <p x-cloak x-show="helperMessage !== ''" x-text="helperMessage" class="mt-3 text-sm text-slate-500"></p>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <label class="block sm:col-span-2">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Company
                    Name</span>
                <input name="company_name" type="text" value="{{ old('company_name', $managedSite?->company_name) }}"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('company_name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block sm:col-span-2">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Street
                    Address</span>
                <input name="street_address" type="text" x-model="streetAddress"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('street_address') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Barangay</span>
                <input name="barangay" type="text" x-model="barangay"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('barangay') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Municipality /
                    City</span>
                <input name="municipality" type="text" x-model="municipality"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('municipality') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block sm:col-span-2">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Province</span>
                <input name="province" type="text" x-model="province"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('province') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>
        </div>
    </section>

    {{-- Geofence fields stay separate so the admin understands what changes attendance validation. --}}
    <section
        class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="border-b border-[#f1c74a] pb-4">
            <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Geofence Settings</h2>
            <p class="mt-1 text-sm text-slate-500">These values control the center point and radius used by future
                attendance rules.</p>
        </div>

        <div class="mt-5 space-y-4">
            <label
                class="flex items-start gap-3 rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4 text-sm text-slate-600">
                <input type="hidden" name="enforce_geofence" x-bind:value="geofenceEnabled ? 1 : 0">
                <input type="checkbox" x-model="geofenceEnabled"
                    class="mt-1 h-4 w-4 rounded border-slate-300 text-[#1e4fa3] focus:ring-[#1e4fa3]">
                <span>
                    <span class="block font-semibold text-[#1e4fa3]">Enforce geofence radius</span>
                    <span class="mt-1 block text-slate-500">Enable this when logs inside the configured radius should count as on-site. Turn it off when attendance should be treated as WFH instead.</span>
                </span>
            </label>

            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Allowed Radius
                    (Meters)</span>
                <input name="allowed_radius_m" type="number" min="1" max="5000"
                    value="{{ old('allowed_radius_m', $managedSite?->allowed_radius_m ?? 100) }}"
                    class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                @error('allowed_radius_m') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span
                        class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Latitude</span>
                    <input name="latitude" type="number" step="0.0000001" min="-90" max="90" x-model="latitude"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                    @error('latitude') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span
                        class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Longitude</span>
                    <input name="longitude" type="number" step="0.0000001" min="-180" max="180" x-model="longitude"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                    @error('longitude') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </label>
            </div>

            <div class="rounded-[1.4rem] border border-[#d7e2f5] bg-[#f7f9fc] px-4 py-4 text-sm text-slate-600">
                <p class="font-semibold text-[#1e4fa3]">Geofence Preview</p>
                <p class="mt-2">
                    <span x-show="geofenceEnabled">Students inside this radius will be classified as on-site.</span>
                    <span x-show="!geofenceEnabled">Attendance will be treated as WFH. Location is still captured and stored on each attendance log.</span>
                </p>
                <p class="mt-2">Latitude <span x-text="latitude !== '' ? latitude : 'not set'"></span>, Longitude
                    <span x-text="longitude !== '' ? longitude : 'not set'"></span>, Radius
                    {{ old('allowed_radius_m', $managedSite?->allowed_radius_m ?? 100) }} meters.
                </p>
            </div>

            {{-- Small hint so admins understand why WFH policy is hidden --}}
            <div x-cloak x-show="geofenceEnabled" class="rounded-[1.4rem] border border-slate-200 bg-white/80 px-4 py-4 text-sm text-slate-600">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">WFH Rules (This Site)</p>
                        <p class="mt-2 font-semibold text-[#1e4fa3]">Hidden while geofence is enabled</p>
                        <p class="mt-2">Turn off geofence to review or change this site’s WFH anchor settings.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-[#d7e2f5] bg-[#f7f9fc] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">
                        <span x-text="wfhAnchorEnforced ? `On · ${wfhAnchorLimit}m` : 'Off · Unlimited'"></span>
                    </span>
                </div>
                <p class="mt-3 rounded-2xl border border-[#d7e2f5] bg-[#f7f9fc] px-3 py-2 text-sm text-[#1e4fa3]">
                    Location is still tracked and saved on each attendance log.
                </p>
            </div>

            {{-- WFH Anchor Policy (per-site) - shown only when geofence is disabled --}}
            <div x-cloak x-show="!geofenceEnabled" class="rounded-[1.8rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] px-5 py-5 text-sm text-slate-700 shadow-[0_22px_55px_-38px_rgba(30,79,163,0.35)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">WFH Rules (This Site)</p>
                        <p class="mt-2 text-base font-bold text-[#1e4fa3]">WFH Anchor Limit</p>
                        <p class="mt-2 text-sm text-slate-600">
                            When enabled, a student&apos;s WFH time-out must be recorded within the allowed meters of their original time-in location.
                        </p>
                        <p class="mt-2 text-sm text-slate-600">
                            <span class="font-semibold text-[#1e4fa3]">Note:</span> Location is still tracked and saved even when this rule is turned off.
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    <label class="flex items-start gap-3 rounded-[1.4rem] border border-slate-200 bg-white/70 px-4 py-4 text-sm text-slate-700">
                        <input type="hidden" name="wfh_anchor_enforced" x-bind:value="wfhAnchorEnforced ? 1 : 0">
                        <input type="checkbox" x-model="wfhAnchorEnforced" class="mt-1 h-4 w-4 rounded border-slate-300 text-[#1e4fa3] focus:ring-[#1e4fa3]">
                        <span>
                            <span class="block font-semibold text-[#1e4fa3]">Enforce WFH anchor limit</span>
                            <span class="mt-1 block text-slate-500">Turn this off to allow WFH time-out from any location (no distance restriction).</span>
                        </span>
                    </label>

                    <label class="block" x-bind:class="wfhAnchorEnforced ? '' : 'opacity-60'">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Allowed Movement From Time-In Point (Meters)</span>
                        <input
                            name="wfh_anchor_limit_m"
                            type="number"
                            min="1"
                            max="5000"
                            x-model="wfhAnchorLimit"
                            x-bind:disabled="!wfhAnchorEnforced"
                            class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white disabled:cursor-not-allowed"
                        >
                        @error('wfh_anchor_limit_m') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </label>
                </div>
            </div>

            <label
                class="flex items-start gap-3 rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4 text-sm text-slate-600">
                <input type="hidden" name="is_active" value="0">
                <input name="is_active" type="checkbox" value="1" {{ (string) $isActiveValue === '1' ? 'checked' : '' }}
                    class="mt-1 h-4 w-4 rounded border-slate-300 text-[#1e4fa3] focus:ring-[#1e4fa3]">
                <span>
                    <span class="block font-semibold text-[#1e4fa3]">Site is active</span>
                    <span class="mt-1 block text-slate-500">Leave this enabled when students are allowed to use this
                        location for attendance workflows.</span>
                </span>
            </label>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('admin.sites.index') }}"
        class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-5 py-3 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">Back
        to Sites</a>
    <button type="submit"
        class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79]">
        {{ $submitLabel }}
    </button>
</div>