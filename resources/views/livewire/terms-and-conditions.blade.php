{{-- Circle + OJT LOGS above card; card with welcome banner, terms, I Agree button --}}
<x-ojt-card maxWidth="max-w-2xl">
    {{-- Welcome badge - system blue palette --}}
    <div class="mb-6 rounded-full bg-[#e8f0ff] px-6 py-3 text-center">
        <p class="text-sm font-semibold text-[#1e4fa3]">Welcome, Dearest Intern!</p>
    </div>

    <p class="mb-6 text-sm leading-relaxed text-slate-500">
        By accessing, registering or using this Application, you agree to be bound by the following Terms and
        Conditions.
    </p>

    {{-- Terms content --}}
    <div class="mb-6 max-h-64 overflow-y-auto pr-2 text-left text-sm leading-relaxed text-slate-600">
        <section class="space-y-4 max-h-64 overflow-y-auto pr-2 scroll-smooth border border-slate-200 rounded-lg p-4">
            <div>
                <h3 class="font-bold text-[#1e4fa3]">I. Purpose of the Application</h3>
                <p class="mt-2">
                    This Application is developed to digitally record and monitor the official time-in and time-out
                    entries of students undergoing On-the-Job Training (OJT). It serves as an official attendance
                    monitoring tool for academic verification and institutional coordination.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-[#1e4fa3]">II. Accuracy and Finality of Information</h3>
                <p class="mt-2">By proceeding with registration, the user:</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li>Confirms that all personal and academic details provided are accurate and complete.</li>
                    <li>Acknowledges that certain information (e.g., number of hours) may not be editable after
                        submission.</li>
                    <li>Understands that inaccurate information may result in delays, denial of validation, or
                        administrative review.</li>
                </ol>
                <p class="mt-3">Users are responsible for reviewing all entries prior to submission.</p>
                <p class="mt-3">
                    <a href="#" class="font-medium text-[#1e4fa3] underline hover:no-underline">Acceptance Logging
                        Policy</a>
                </p>
                <p class="mt-1">All time-in and time-out entries must reflect actual attendance. Falsification or misuse
                    of the logging system is prohibited.</p>
            </div>
        </section>
    </div>

    {{-- I Agree button --}}
    <div class="flex justify-center">
        <button type="button" wire:click="agree"
            class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-8 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79] focus:outline-none focus:ring-2 focus:ring-[#1e4fa3] focus:ring-offset-2">
            I Agree
        </button>
    </div>
</x-ojt-card>