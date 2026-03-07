<div class="min-h-screen w-full flex justify-center font-sans">
    
    <div class="w-full max-w-[650px] flex flex-col items-center justify-center p-6 relative" 
         style="background-color: transparent;">

        <div class="mb-4 flex flex-col items-center">
            <div class="h-24 w-24 bg-white rounded-full shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] mb-4 flex items-center justify-center">
                </div>
            
            <h1 class="text-4xl font-bold tracking-[0.2em] uppercase" 
                style="color: #1a4b8c; font-family: 'Orbitron', sans-serif;">
                OJT LOGS
            </h1>
        </div>

        <div class="w-full max-w-[420px] bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-10 flex flex-col">
            
            <h2 class="text-center text-[20px] font-semibold mb-8 text-gray-600">
                Log In to Your Account
            </h2>

            <form wire:submit="login" class="space-y-4">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-900/40">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        placeholder="Username"
                        style="width: 100%; 
                            padding: 14px 16px 14px 48px; 
                            font-size: 14px; 
                            border-radius: 16px; 
                            background-color: #ffffff; 
                            border: 1.5px solid #0b55b0; 
                            box-shadow: 0 4px 10px rgba(11, 85, 176, 0.15); 
                            outline: none; 
                            transition: all 0.2s ease-in-out;
                            color: #333;"
                    />
                </div>

                <div class="relative" x-data="{ show: false }">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-900/40">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        placeholder="Password"
                        style="width: 100%; 
                            padding: 14px 16px 14px 48px; 
                            font-size: 14px; 
                            border-radius: 16px; 
                            background-color: #ffffff; 
                            border: 1.5px solid #0b55b0; 
                            box-shadow: 0 4px 10px rgba(11, 85, 176, 0.15); 
                            outline: none; 
                            transition: all 0.2s ease-in-out;
                            color: #333;"
                    />
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <div class="text-center">
                    <a href="#" class="text-[11px] font-bold text-red-400 hover:text-red-500 uppercase tracking-tight">
                        Forgot Your Password?
                    </a>
                </div>

                <div class="space-y-3 pt-2">
                    <button type="submit" 
                        class="w-full py-4 rounded-2xl font-bold text-white shadow-lg active:scale-[0.98] transition-all"
                        style="background-color: #0b55b0;">
                        Login
                    </button>
                    
                    <button type="button"
                        class="w-full py-4 rounded-2xl font-bold text-gray-700 shadow-md active:scale-[0.98] transition-all"
                        style="background-color: #ffc107;">
                        Track Location
                    </button>
                </div>
            </form>

            <p class="mt-8 text-center text-[10px] text-gray-500">
                Don't have an account? 
                <a href="{{ route('signup') }}" class="font-bold text-[#1a4b8c] hover:underline">Sign Up Now!</a>
            </p>
        </div>

        <div class="absolute bottom-8 flex items-center space-x-2 text-[10px] text-gray-400 font-medium">
            <span>Copyright © 2026. Powered by</span>
            <div class="h-4 w-4 bg-gray-300 rounded-full opacity-50 flex items-center justify-center">
                </div>
        </div>
    </div>
</div>