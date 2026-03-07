<div class="min-h-screen w-full flex justify-center bg-Transparent font-sans">

    <div class="w-full max-w-[650px] flex flex-col items-center justify-center p-6 relative" 
         style="background-color: Transparent;">

        <div class="mb-6 flex flex-col items-center">
            {{-- White Floating Circle Logo --}}
            <div class="h-24 w-24 bg-white rounded-full shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] mb-4 flex items-center justify-center">
            </div>
            <h1 class="text-3xl font-bold tracking-[0.2em] uppercase" 
                style="color: #1a4b8c; font-family: 'Orbitron', sans-serif;">
                OJT LOGS
            </h1>
        </div>

        
        <div class="w-full max-w-[420px] bg-Transparent rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-10 flex flex-col">
            
            {{-- Welcome Header: Golden Yellow --}}
            <h2 class="text-center text-[20px] font-extrabold mb-2" style="color: #f5b921;">
                Welcome, Dearest Intern!
            </h2>

            {{-- Intro Text: Alert Red --}}
            <p class="text-center text-[10px] font-semibold leading-tight mb-5 px-6" style="color: #e55353;">
                By accessing, registering, or using this Application, you agree to be bound by the following Terms and Conditions.
            </p>

            <div class="flex-1 overflow-y-auto pr-3 mb-6 custom-scrollbar" style="max-height: 280px;">
                <div class="text-[10.5px] leading-relaxed space-y-4" style="color: #1a4b8c;">
                    <div>
                        <p class="font-black text-[#0b55b0]">I. Purpose of the Application</p>
                        <p class="mt-1 text-[#4a72a8]">This Application is developed to digitally record and monitor the official time-in and time-out entries of students undergoing On-the-Job Training (OJT). It serves as an official attendance monitoring tool for academic verification and institutional coordination.</p>
                    </div>

                    <div>
                        <p class="font-black text-[#0b55b0]">II. Accuracy and Finality of Information</p>
                        <p class="mt-1 text-[#4a72a8]">By proceeding with registration, the user:</p>
                        <ol class="list-decimal ml-4 mt-2 space-y-2 text-[#4a72a8]">
                            <li>Confirms that all personal and academic details provided are accurate and complete.</li>
                            <li>Acknowledges that certain information (e.g., number of hours) may not be editable after submission.</li>
                            <li>Understands that inaccurate information may result in delays, denial of validation, or administrative review.</li>
                        </ol>
                        <p class="mt-4 text-[#4a72a8] text-[9.5px]">Users are responsible for reviewing all entries prior to submission.</p>
                        <p class="mt-3 font-bold underline cursor-pointer hover:text-blue-800 transition-colors">Attendance Logging Policy</p>
                    </div>
                </div>
            </div>

            <button wire:click="agree" 
                    class="w-full py-4 rounded-2xl font-bold text-white transition-all active:scale-95 shadow-lg"
                    style="background-color: #0b55b0; box-shadow: 0 6px 20px rgba(11, 85, 176, 0.25);">
                I Agree
            </button>
        </div>

        <div class="absolute bottom-8 flex items-center space-x-2 text-[10px] text-gray-400 font-medium">
            <span>Copyright © 2026. Powered by</span>
            <div class="h-4 w-4 bg-gray-300 rounded-full opacity-50 flex items-center justify-center">

            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar para sa malinis na look */
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #0b55b0; border-radius: 10px; opacity: 0.5; }
</style>