{{-- Circle + OJT LOGS above card; card with welcome banner, terms, I Agree button --}}
<x-ojt-card maxWidth="max-w-2xl">
    {{-- Orange-red welcome banner --}}
    <div class="mb-6 rounded-xl px-4 py-3 text-center text-white" style="background-color: #ea580c;">
        Welcome, Dearest Intern!
    </div>

    <p class="mb-6 text-sm leading-relaxed text-gray-900">
        By accessing, registering or using this Application, you agree to be bound by the following Terms and Conditions.
    </p>

    {{-- Terms content --}}
    <div class="mb-6 max-h-64 overflow-y-auto text-left text-sm leading-relaxed text-gray-900">
        <section class="space-y-4">
            <div>
                <h3 class="font-bold text-gray-900">I. Purpose of the Application</h3>
                <p class="mt-2">
                    This Application is developed to digitally record and monitor the official time-in and time-out entries of students undergoing On-the-Job Training (OJT). It serves as an official attendance monitoring tool for academic verification and institutional coordination.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">II. Accuracy and Finality of Information</h3>
                <p class="mt-2">By proceeding with registration, the user:</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li>Confirms that all personal and academic details provided are accurate and complete.</li>
                    <li>Acknowledges that certain information (e.g., number of hours) may not be editable after submission.</li>
                    <li>Understands that inaccurate information may result in delays, denial of validation, or administrative review.</li>
                </ol>
                <p class="mt-3">Users are responsible for reviewing all entries prior to submission.</p>
                <p class="mt-3">
                    <a href="#" class="font-medium text-[#1f4082] underline hover:no-underline">Acceptance Logging Policy</a>
                </p>
                <p class="mt-1">All time-in and time-out entries must reflect actual attendance. Falsification or misuse of the logging system is prohibited.</p>
            </div>
        </section>
    </div>

    {{-- I Agree button --}}
    <div class="flex justify-center">
        <button
            type="button"
            wire:click="agree"
            style="background-color: #1f4082; color: #ffffff; box-shadow: 0 2px 8px rgba(31,64,130,0.3);"
            class="rounded-xl px-8 py-3 font-bold transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#1f4082] focus:ring-offset-2"
        >
            I Agree
        </button>
    </div>
</x-ojt-card>
