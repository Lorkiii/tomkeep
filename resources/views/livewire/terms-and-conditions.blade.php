<div>
    <x-ojt-logo-header class="mb-6" />

    <p class="text-center text-lg font-medium text-amber-600">Welcome, Dearest Intern!</p>
    <p class="mt-3 text-sm text-slate-600">
        By accessing or registering on this application, you agree to the following
        <strong>Terms and Conditions</strong>. Please read them carefully.
    </p>

    <div class="mt-4 space-y-4 text-sm text-slate-700">
        <section>
            <h2 class="font-semibold text-slate-900">I. Purpose of the Application</h2>
            <p class="mt-1">
                This application serves as a digital record and monitoring tool for your On-the-Job Training (OJT)
                time-in and time-out entries. It is designed to support academic verification and ensure accurate
                tracking of your training hours.
            </p>
        </section>
        <section>
            <h2 class="font-semibold text-slate-900">II. Accuracy and Finality of Information</h2>
            <p class="mt-1">
                You are responsible for ensuring that all personal and academic information you provide is accurate
                and complete. Please note that certain information (e.g., number of hours) may not be editable after
                submission. You are responsible for reviewing your entries before confirming. By using this application,
                you acknowledge these terms.
            </p>
        </section>
    </div>

    <p class="mt-4 text-sm">
        <a href="#" class="font-medium text-[#1e3a5f] underline hover:no-underline">Attendance Logging Policy</a>
    </p>

    <div class="mt-6">
        <button
            type="button"
            wire:click="agree"
            class="w-full rounded-xl bg-[#1e3a5f] px-4 py-3 font-medium text-white shadow transition hover:bg-[#152a47] focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:ring-offset-2"
        >
            I Agree
        </button>
    </div>

    <x-guest-footer />
</div>
