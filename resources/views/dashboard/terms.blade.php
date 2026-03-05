<x-layouts.dashboard title="Terms and Conditions" active="terms">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-8 w-8 shrink-0 rounded-full bg-slate-300"></span>
            <h1 class="text-xl font-semibold text-slate-800">OJT LOGS</h1>
        </div>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="text-sm font-medium text-slate-700">Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!</p>
        @endif
    </div>

    <h2 class="mb-4 text-lg font-bold text-slate-900">Terms and Conditions</h2>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <p class="mb-6 italic text-slate-600">
            By accessing, registering, or using this Application, you agree to be bound by the following Terms and Conditions.
        </p>
        <div class="space-y-6 text-sm text-slate-700">
            <section>
                <h3 class="font-semibold text-slate-900">I. Purpose of the Application</h3>
                <p class="mt-1">
                    This application serves as a digital record and monitoring tool for your On-the-Job Training (OJT)
                    time-in and time-out entries. It is designed to support academic verification and ensure accurate
                    tracking of your training hours, and to serve as an official attendance monitoring tool for institutional coordination.
                </p>
            </section>
            <section>
                <h3 class="font-semibold text-slate-900">II. Accuracy and Finality of Information</h3>
                <p class="mt-1">
                    You are responsible for ensuring that all personal and academic information you provide is accurate
                    and complete. Please note that certain information (e.g., number of hours) may not be editable after
                    submission. You are responsible for reviewing your entries before confirming. By using this application,
                    you acknowledge these terms. Users are responsible for reviewing all entries prior to submission.
                </p>
                <h4 class="mt-3 font-medium text-slate-800">Attendance Logging Policy</h4>
                <p class="mt-1">
                    All time-in and time-out entries must reflect actual attendance. Falsification or misuse of the logging system is prohibited.
                </p>
            </section>
            <section>
                <h3 class="font-semibold text-slate-900">III. Users agree that</h3>
                <p class="mt-1">
                    Time-in and time-out records must be accurate and reflect actual attendance. Falsification or misuse of logs is prohibited.
                    The institution reserves the right to audit records. Violations may result in disciplinary action in accordance with institutional policies.
                </p>
            </section>
            <section>
                <h3 class="font-semibold text-slate-900">IV. Data Privacy and Protection</h3>
                <p class="mt-1">
                    Personal data collected for internship monitoring may include: Full Name, Contact Information,
                    Attendance Logs, and Location Data Consent where applicable. This information is used solely for OJT verification and institutional purposes.
                </p>
            </section>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-2">
        <p class="text-xs text-slate-400">Copyright © {{ date('Y') }}. Powered by</p>
        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm hover:bg-slate-50" aria-label="Toggle theme">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </button>
    </div>
</x-layouts.dashboard>
