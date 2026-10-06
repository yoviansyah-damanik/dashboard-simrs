@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Telah Kedaluwarsa')

@section('icon_class', 'icon-[solar--history-bold-duotone]')
@section('badge_bg', 'bg-sky-500/10 dark:bg-sky-500/15')
@section('badge_text', 'text-sky-600 dark:text-sky-400')
@section('badge_border', 'border border-sky-500/20')
@section('badge_glow', 'bg-sky-500/15')
@section('title_gradient', 'bg-gradient-to-r from-sky-600 via-teal-600 to-emerald-600 dark:from-sky-400 dark:via-teal-400 dark:to-emerald-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-sky-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Sesi kerja Anda telah kedaluwarsa karena tidak ada aktivitas dalam beberapa waktu atau token keamanan formulir telah diperbarui. Silakan muat ulang atau masuk kembali.' }}
@endsection

@section('actions')
    <a href="{{ route('login') }}"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-teal-600 hover:from-sky-700 hover:to-teal-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-sky-600/20 transition-all duration-200 active:scale-95">
        <span class="icon-[solar--login-2-bold-duotone] text-lg"></span>
        <span>Masuk Kembali</span>
    </a>
@endsection
