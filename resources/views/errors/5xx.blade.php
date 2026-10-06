@extends('errors.layout')

@php
    $statusCode = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
@endphp

@section('code', $statusCode)
@section('title', 'Gangguan Layanan Server')

@section('icon_class', 'icon-[solar--server-square-bold-duotone]')
@section('badge_bg', 'bg-rose-500/10 dark:bg-rose-500/15')
@section('badge_text', 'text-rose-600 dark:text-rose-400')
@section('badge_border', 'border border-rose-500/20')
@section('badge_glow', 'bg-rose-500/15')
@section('title_gradient', 'bg-gradient-to-r from-rose-600 via-pink-600 to-purple-600 dark:from-rose-400 dark:via-pink-400 dark:to-purple-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-rose-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Server SIMRS mengalami kendala saat memproses permintaan ini. Sistem telah mencatat aktivitas ini dan tim teknis sedang melakukan penanganan.' }}
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload()"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-rose-600/20 transition-all duration-200 active:scale-95">
        <span class="icon-[solar--restart-bold-duotone] text-lg"></span>
        <span>Coba Muat Ulang</span>
    </button>
@endsection
