@extends('errors.layout')

@section('code', '429')
@section('title', 'Terlalu Banyak Permintaan')

@section('icon_class', 'icon-[solar--stop-circle-bold-duotone]')
@section('badge_bg', 'bg-orange-500/10 dark:bg-orange-500/15')
@section('badge_text', 'text-orange-600 dark:text-orange-400')
@section('badge_border', 'border border-orange-500/20')
@section('badge_glow', 'bg-orange-500/15')
@section('title_gradient', 'bg-gradient-to-r from-orange-600 via-amber-600 to-yellow-600 dark:from-orange-400 dark:via-amber-400 dark:to-yellow-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-orange-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Sistem mendeteksi terlalu banyak permintaan dari perangkat Anda dalam rentang waktu yang sangat singkat. Harap tunggu beberapa saat sebelum mencoba kembali.' }}
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload()"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-orange-600/20 transition-all duration-200 active:scale-95">
        <span class="icon-[solar--restart-bold-duotone] text-lg"></span>
        <span>Coba Lagi Nanti</span>
    </button>
@endsection
