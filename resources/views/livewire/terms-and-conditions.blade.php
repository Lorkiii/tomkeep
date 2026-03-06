{{-- Smooth UI: horizontal card, centered text block, scrollable terms, normal I Agree button --}}
<x-ojt-card maxWidth="max-w-6xl" :spacious="true">
    {{-- Welcome: orange-yellow, centered --}}
    <p class="mb-3 text-center text-lg font-medium" style="color: #F9BD1D;">Welcome, Dearest Intern!</p>

    {{-- Intro: reddish-orange, centered --}}
    <p class="mb-5 text-center text-sm leading-relaxed" style="color: #D15755;">
        By accessing, registering, or using this Application, you agree to be bound by the following Terms and Conditions.
    </p>

    {{-- Scrollable terms block: left-aligned within centered card, dark blue headings, grey body --}}
    <div
        class="mx-auto mb-6 max-h-[320px] overflow-y-auto overflow-x-hidden text-left text-sm leading-relaxed"
        style="scrollbar-width: thin; color: #4A5568;"
    >
        <section class="space-y-4 pr-2">
            <div>
                <h3 class="font-bold" style="color: #1C4DA1;">I. Purpose of the Application</h3>
                <p class="mt-2">
                    This Application is developed to digitally record and monitor the official time-in and time-out entries of students undergoing On-the-Job Training (OJT). It serves as an official attendance monitoring tool for academic verification and institutional coordination.
                </p>
            </div>
            <div>
                <h3 class="font-bold" style="color: #1C4DA1;">II. Accuracy and Finality of Information</h3>
                <p class="mt-2">By proceeding with registration, the user:</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li>Confirms that all personal and academic details provided are accurate and complete.</li>
                    <li>Acknowledges that certain information (e.g., number of hours) may not be editable after submission.</li>
                    <li>Understands that inaccurate information may result in delays, denial of validation, or administrative review.</li>
                </ol>
                <p class="mt-3">Users are responsible for reviewing all entries prior to submission.</p>
                <p class="mt-3">
                    <a href="#" class="font-medium underline hover:no-underline" style="color: #1C4DA1;">Attendance Logging Policy</a>
                </p>
                <p class="mt-1">All time-in and time-out entries must reflect actual attendance. Falsification or misuse of the logging system is prohibited.</p>
            </div>
            {{-- Extra sections for scroll testing --}}
            <div>
                <h3 class="font-bold" style="color: #1C4DA1;">III. Data Privacy and Security</h3>
                <p class="mt-2">
                    The Application collects and stores personal and attendance data necessary for OJT monitoring. By using this Application, you consent to the collection, processing, and storage of your information in accordance with applicable data protection laws. The institution and authorized administrators may access your records for verification, reporting, and academic purposes only. You are responsible for keeping your login credentials confidential and for all activity under your account.
                </p>
            </div>
            <div>
                <h3 class="font-bold" style="color: #1C4DA1;">IV. Use of the System</h3>
                <p class="mt-2">
                    You agree to use the Application only for lawful purposes related to your On-the-Job Training. You must not attempt to circumvent security measures, manipulate time entries, or use the system in any way that could harm the service or other users. The institution reserves the right to suspend or terminate access in case of misuse, and to report any fraudulent activity to the appropriate authorities.
                </p>
            </div>
            <div>
                <h3 class="font-bold" style="color: #1C4DA1;">V. Modifications and Notifications</h3>
                <p class="mt-2">
                    These Terms and Conditions may be updated from time to time. Continued use of the Application after changes constitutes acceptance of the revised terms. Important updates may be communicated via email or in-app notification. It is your responsibility to review the terms periodically and to ensure your contact information is current so you can receive such notifications.
                </p>
            </div>
        </section>
    </div>

    {{-- I Agree: normal size, generous padding, dark blue, smooth shadow --}}
    <div class="flex justify-center">
        <button
            type="button"
            wire:click="agree"
            style="background-color: #285DAB; color: #ffffff; box-shadow: 0 4px 12px rgba(40,93,171,0.25);"
            class="rounded-2xl px-12 py-3.5 text-base font-bold transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#285DAB] focus:ring-offset-2"
        >
            I Agree
        </button>
    </div>
</x-ojt-card>
