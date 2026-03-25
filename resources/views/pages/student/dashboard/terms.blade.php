<x-student.layouts.dashboard title="Terms and Conditions" active="terms">
    <div class="min-h-[calc(100vh-8rem)] rounded-2xl bg-[#f7f9fc] py-8">
        <div class="mx-auto flex max-w-2xl flex-col items-center px-4">
            <div class="mb-4 flex flex-col items-center">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-[#d5e0f0] bg-white shadow-sm sm:h-16 sm:w-16">
                    <svg class="h-7 w-7 text-[#1e4fa3] sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h1 class="mt-3 text-xl font-bold uppercase tracking-wide text-[#1e4fa3] sm:text-2xl">OJT LOGS</h1>
            </div>

            <div class="w-full rounded-[1.8rem] border border-[#d5e0f0] bg-white/90 p-6 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-8">
                <div class="mb-4 rounded-full bg-[#e8f0ff] px-4 py-2 text-center">
                    <p class="text-sm font-semibold text-[#1e4fa3]">Welcome, Dearest Intern!</p>
                </div>
                <p class="mb-6 text-sm italic text-slate-500">
                    By accessing, registering, or using this Application, you agree to be bound by the following Terms and Conditions.
                </p>
                <div class="max-h-[320px] space-y-6 overflow-y-auto pr-2 text-sm text-slate-600">
                    <section>
                        <h3 class="font-bold text-[#1e4fa3]">I. Purpose of the Application</h3>
                        <p class="mt-1">
                            This Application is developed to digitally record and monitor the official time-in and time-out entries of students undergoing On the Job Training (OJT). It serves as an official attendance monitoring tool for academic verification and institutional coordination.
                        </p>
                    </section>
                    <section>
                        <h3 class="font-bold text-[#1e4fa3]">II. Accuracy and Finality of Information</h3>
                        <ol class="mt-1 list-decimal space-y-1 pl-5">
                            <li>You are responsible for ensuring that all personal and academic information you provide is accurate and complete.</li>
                            <li>Certain information (e.g., number of hours) may not be editable after submission. You are responsible for reviewing your entries before confirming.</li>
                            <li>By using this application, you acknowledge these terms. Inaccurate or falsified information may result in consequences in accordance with institutional policies.</li>
                        </ol>
                        <h4 class="mt-4 font-bold text-slate-700">Attendance Logging Policy</h4>
                        <p class="mt-1">
                            All time-in and time-out entries must reflect actual attendance. Falsification or misuse of the logging system is prohibited.
                        </p>
                    </section>
                </div>
                <div class="mt-8 flex justify-center">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-8 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79] focus:outline-none focus:ring-2 focus:ring-[#1e4fa3] focus:ring-offset-2"
                    >
                        I Agree
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400">
        <span>Copyright © {{ date('Y') }}. Powered by</span>
        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-slate-200 text-slate-500">
            <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
    </footer>
</x-student.layouts.dashboard>