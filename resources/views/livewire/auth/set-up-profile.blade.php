<div class="min-h-screen bg-transparent flex flex-col items-center justify-center p-4">
    <div class="relative w-full max-w-md mt-16 mb-8">

        <div class="relative bg-white rounded-[2.5rem] shadow-[0px_10px_50px_rgba(31,64,130,0.08)] p-10 pt-16 z-10">
            
            <div class="space-y-6">
                <h1 class="text-center text-xl font-bold text-[#1f4082]">Set Up Your Profile</h1>

                <form wire:submit="openConfirmation" class="space-y-4">
                    
                    <div>
                        <label for="profile-first-name" class="block text-[11px] font-bold text-[#1f4082] ml-1">First Name <span class="text-red-500">*</span></label>
                        <input id="profile-first-name" type="text" wire:model="first_name" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="First Name" />
                        @error('first_name') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-middle-name" class="block text-[11px] font-bold text-[#1f4082] ml-1">Middle Name <span class="text-red-500">*</span></label>
                        <input id="profile-middle-name" type="text" wire:model="middle_name" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="Middle Name" />
                        @error('middle_name') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-last-name" class="block text-[11px] font-bold text-[#1f4082] ml-1">Last Name <span class="text-red-500">*</span></label>
                        <input id="profile-last-name" type="text" wire:model="last_name" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="Last Name" />
                        @error('last_name') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-gender" class="block text-[11px] font-bold text-[#1f4082] ml-1">Gender <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="profile-gender" wire:model="gender" 
                                class="mt-1 w-full appearance-none rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none bg-white">
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('gender') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-date-of-birth" class="block text-[11px] font-bold text-[#1f4082] ml-1">Birthday</label>
                        <input id="profile-date-of-birth" type="text" wire:model="date_of_birth" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="MM/DD/YYYY" />
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="block text-[12px] font-bold text-[#1f4082] ml-1">Address</label>
                        
                        <div class="relative">
                            <select wire:model.live="province" class="w-full appearance-none rounded-2xl border border-[#E2E8F0] py-2.5 px-4 text-slate-700 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none bg-white">
                                <option value="">Select Province</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <span class="block text-[10px] text-slate-400 ml-1 mt-[-8px]">Province</span>
                        @error('province') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror

                        <div class="relative">
                            <select wire:model="municipality" class="w-full appearance-none rounded-2xl border border-[#E2E8F0] py-2.5 px-4 text-slate-700 text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none bg-white">
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <span class="block text-[10px] text-slate-400 ml-1 mt-[-8px]">Municipality</span>
                        @error('municipality') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                        
                        @if(empty($province))
                            <p class="text-[10px] text-orange-500 font-medium ml-1 mt-[-8px]">Select a province first to see municipalities.</p>
                        @endif

                        <div>
                            <input type="text" wire:model="street" class="w-full rounded-2xl border border-[#E2E8F0] py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none" placeholder="e.g. Main Street, Rizal Avenue" />
                            <span class="block text-[10px] text-slate-400 ml-1 mt-1">Street</span>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-1/2">
                                <input type="text" wire:model="house_number" class="w-full rounded-2xl border border-[#E2E8F0] py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none" placeholder="e.g. 123" />
                                <span class="block text-[10px] text-slate-400 ml-1 mt-1">House Number</span>
                            </div>
                            <div class="w-1/2">
                                <input type="text" wire:model="postal_code" class="w-full rounded-2xl border border-[#E2E8F0] py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none" placeholder="e.g. 3000" />
                                <span class="block text-[10px] text-slate-400 ml-1 mt-1">Postal Code</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="profile-required-hours" class="block text-[11px] font-bold text-[#1f4082] ml-1">No. of Hours <span class="text-red-500">*</span></label>
                        <input id="profile-required-hours" type="number" wire:model="required_hours" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="0" />
                        @error('required_hours') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-contact-number" class="block text-[11px] font-bold text-[#1f4082] ml-1">Contact Number <span class="text-red-500">*</span></label>
                        <input id="profile-contact-number" type="text" wire:model="contact_number" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="e.g. 09920003349" />
                        @error('contact_number') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="profile-school-attended" class="block text-[11px] font-bold text-[#1f4082] ml-1">University Attended <span class="text-red-500">*</span></label>
                        <input id="profile-school-attended" type="text" wire:model="school_attended" 
                            class="mt-1 w-full rounded-2xl border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none"
                            placeholder="e.g. National University" />
                        @error('school_attended') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 pb-2">
                        <button type="submit" 
                            class="w-full rounded-2xl bg-[#224891] px-4 py-4 text-sm font-bold text-white shadow-lg shadow-blue-900/20 hover:bg-[#1a366e] transition-all active:scale-[0.98]">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-guest-footer />
</div>