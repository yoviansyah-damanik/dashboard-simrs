@extends('errors.layout')

@section('code', '503')
@section('title', 'Layanan Dalam Pemeliharaan')

@section('icon_class', 'icon-[solar--wrench-bold-duotone]')
@section('badge_bg', 'bg-violet-500/10 dark:bg-violet-500/15')
@section('badge_text', 'text-violet-600 dark:text-violet-400')
@section('badge_border', 'border border-violet-500/20')
@section('badge_glow', 'bg-violet-500/15')
@section('title_gradient', 'bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 dark:from-violet-400 dark:via-purple-400 dark:to-indigo-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-violet-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Sistem SIMRS sedang menjalani pemeliharaan berkala atau peningkatan performa server. Harap bersabar dan coba akses kembali dalam beberapa saat.' }}
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload()"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-violet-600/20 transition-all duration-200 active:scale-95">
        <span class="icon-[solar--restart-bold-duotone] text-lg"></span>
        <span>Periksa Kembali</span>
    </button>
@endsection
