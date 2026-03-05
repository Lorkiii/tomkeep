<x-layouts.dashboard title="Terms and Conditions" active="terms">
    <div class="min-h-[calc(100vh-8rem)] rounded-2xl py-8" style="background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);">
        <div class="mx-auto flex max-w-2xl flex-col items-center px-4">
            {{-- Above card: white circle + OJT LOGS --}}
            <div class="mb-4 flex flex-col items-center">
                <div class="h-14 w-14 shrink-0 rounded-full border border-slate-200 bg-white shadow-sm sm:h-16 sm:w-16"></div>
                <h1 class="mt-3 text-xl font-bold uppercase tracking-wide sm:text-2xl" style="color: #134991;">OJT LOGS</h1>
            </div>

            <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8">
                <p class="mb-4 text-lg font-semibold" style="color: #d97706;">Welcome, Dearest Intern!</p>
                <p class="mb-6 italic text-slate-700">
                    By accessing, registering, or using this Application, you agree to be bound by the following Terms and Conditions.
                </p>
                <div class="space-y-6 text-sm text-slate-700">
                    <section>
                        <h3 class="font-bold text-slate-900">I. Purpose of the Application</h3>
                        <p class="mt-1">
                            This Application is developed to digitally record and monitor the official time-in and time-out entries of students undergoing On the Job Training (OJT). It serves as an official attendance monitoring tool for academic verification and institutional coordination.
                        </p>
                    </section>
                    <section>
                        <h3 class="font-bold text-slate-900">II. Accuracy and Finality of Information</h3>
                        <ol class="mt-1 list-decimal space-y-1 pl-5">
                            <li>You are responsible for ensuring that all personal and academic information you provide is accurate and complete.</li>
                            <li>Certain information (e.g., number of hours) may not be editable after submission. You are responsible for reviewing your entries before confirming.</li>
                            <li>By using this application, you acknowledge these terms. Inaccurate or falsified information may result in consequences in accordance with institutional policies.</li>
                        </ol>
                        <h4 class="mt-4 font-bold text-slate-800">Attendance Logging Policy</h4>
                        <p class="mt-1">
                            All time-in and time-out entries must reflect actual attendance. Falsification or misuse of the logging system is prohibited.
                        </p>
                    </section>
                </div>
                <div class="mt-8 flex justify-center">
                    <button
                        type="button"
                        style="background-color: #134991; color: white;"
                        class="rounded-xl px-8 py-3 font-medium shadow transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#134991] focus:ring-offset-2"
                    >
                        I Agree
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-500">
        <span>Copyright © {{ date('Y') }}. Powered by</span>
        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-slate-300 text-slate-500">
            <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
    </footer>
</x-layouts.dashboard>
