<x-layouts.app>
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">OJT LOGS</h1>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="mt-2 text-sm text-slate-600">
                Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!
            </p>
            <p class="mt-1 text-xs text-slate-500">
                {{ $currentOjtUser['email'] ?? '' }}
            </p>
            <form action="{{ route('logout') }}" method="POST" class="mt-4 inline">
                @csrf
                <button type="submit" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Log Out
                </button>
            </form>
        @endif

        <div class="mt-4">
            <a href="{{ route('attendance.livewire') }}"
                class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                Open Livewire smoke test
            </a>
        </div>
    </section>
</x-layouts.app>`