<div wire:loading.flex {{ $attributes }}
    class="!mt-0 fixed top-0 left-0 w-screen h-screen z-[999999] flex items-center justify-center bg-slate-950/80 backdrop-blur-xl cursor-wait overflow-hidden"
    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100">

    <div
        class="relative flex flex-col items-center gap-10 p-12 bg-white rounded-[3.5rem] shadow-[0_0_120px_rgba(0,0,0,0.6)] border border-white/20 max-w-md w-full mx-4">

        <!-- High-Contrast Animated Loader -->
        <div class="relative flex items-center justify-center w-36 h-36">
            <!-- Animated vibrant green rings -->
            <div
                class="absolute inset-0 rounded-full border-[8px] border-slate-100 border-t-emerald-500 animate-[spin_2.5s_linear_infinite]">
            </div>
            <div
                class="absolute inset-5 rounded-full border-[6px] border-slate-100 border-b-emerald-400 animate-[spin_1.8s_linear_infinite_reverse]">
            </div>
            <div
                class="absolute inset-10 rounded-full border-[4px] border-slate-100 border-l-emerald-300 animate-[spin_1.2s_linear_infinite]">
            </div>

            <!-- Dynamic Center Element -->
            <div
                class="relative w-14 h-14 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl rotate-45 flex items-center justify-center shadow-[0_0_40px_rgba(16,185,129,0.5)] animate-[pulse_2s_ease-in-out_infinite]">
                <i class="ph-bold ph-file-pdf text-white text-3xl -rotate-45"></i>
            </div>
        </div>

        <div class="text-center space-y-4">
            <h2 class="text-4xl font-[900] text-slate-950 tracking-tighter uppercase italic leading-none">
                Processing
            </h2>
            <div class="flex items-center justify-center gap-1.5">
                <div class="h-2 w-16 bg-emerald-500 rounded-full"></div>
                <div class="h-2 w-2 bg-emerald-400 rounded-full animate-ping"></div>
            </div>
            <p class="text-slate-500 font-bold text-lg tracking-tight px-4 leading-relaxed">
                Sedang merangkai data laporan <br /> <span class="text-emerald-600">spesifik untuk Anda</span>
            </p>
        </div>

        <!-- Footer Branding -->
        <div class="flex flex-col items-center gap-4">
            <div class="flex gap-2">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-bounce"
                    style="animation-delay: 0.1s"></span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-bounce"
                    style="animation-delay: 0.2s"></span>
            </div>
            <p class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 text-center leading-relaxed">
                {{ config('app.name') }} <br /> {{ config('app.hospital_name') }}
            </p>
        </div>
    </div>

    <!-- Decorative floating elements -->
    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl animate-pulse"
        style="animation-delay: 1s"></div>
</div>
