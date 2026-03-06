<div>
    <div class="mx-auto w-full max-w-md">
        <div class="flex flex-col items-center">
            <div class="h-20 w-20 shrink-0 rounded-full bg-white sm:h-24 sm:w-24" style="box-shadow: 4px 4px 12px rgba(0,0,0,0.08); margin-bottom: -2.5rem; z-index: 10;"></div>
            <div class="w-full rounded-2xl bg-white pt-14 pb-8 pl-6 pr-6 sm:pt-16 sm:px-8 sm:pb-10" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <h1 class="mb-4 text-center text-xl font-bold uppercase tracking-wide sm:text-2xl" style="color: #1f4082;">OJT LOGS</h1>
                <h2 class="mb-6 text-center text-sm font-normal text-gray-800">Log In to Your Account</h2>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="login-username" class="sr-only">Username</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center text-slate-500 font-mono text-lg font-bold">@</span>
                <input
                    id="login-username"
                    type="text"
                    wire:model="username"
                    placeholder="Username"
                    autocomplete="username"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-12 pr-4 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
            </div>
            @error('username')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <label for="login-password" class="sr-only">Password</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v2.25a3 3 0 003 3h10.5a3 3 0 003-3V12.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/></svg>
                </span>
                <input
                    id="login-password"
                    :type="show ? 'text' : 'password'"
                    wire:model="password"
                    placeholder="Password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-12 pr-12 text-slate-800 placeholder-slate-400 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400"
                />
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                    aria-label="Toggle password visibility"
                >
                    <svg x-show="!show" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    <svg x-show="show" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" x-cloak><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.01c.43-.56.79-1.23 1.08-1.96-.36-.66-.87-1.22-1.49-1.64l-2.76 2.46z"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-center">
            <a href="#" class="text-sm font-medium hover:opacity-90" style="color: #e07c5e;">Forget Your Password?</a>
        </div>

        <div class="space-y-3 pt-2">
            <button
                type="submit"
                style="background-color: #1A53A0; color: white;"
                class="w-full rounded-xl px-4 py-3 font-medium font-semibold shadow transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#1A53A0] focus:ring-offset-2"
            >
                Login
            </button>
            <a
                href="{{ route('signup') }}"
                style="background-color: #F8D04B; color: #1f2937;"
                class="flex w-full items-center justify-center rounded-xl px-4 py-3 font-medium font-semibold shadow transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
            >
                Track Location
            </a>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Don't have an account?
        <a href="{{ route('signup') }}" class="font-medium text-[#1e3a5f] hover:underline" wire:navigate>Sign Up Now!</a>
    </p>
            </div>
        </div>
    </div>
    <footer class="mt-8 text-center text-xs text-gray-400">
        <p class="flex items-center justify-center gap-1.5">
            Copyright &copy; {{ date('Y') }}. Powered By
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-gray-300 text-gray-400" aria-hidden="true">
                <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </span>
        </p>
    </footer>
</div>
