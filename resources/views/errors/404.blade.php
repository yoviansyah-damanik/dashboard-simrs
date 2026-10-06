@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')

@section('icon_class', 'icon-[solar--map-point-remove-bold-duotone]')
@section('badge_bg', 'bg-emerald-500/10 dark:bg-emerald-500/15')
@section('badge_text', 'text-emerald-600 dark:text-emerald-400')
@section('badge_border', 'border border-emerald-500/20')
@section('badge_glow', 'bg-emerald-500/15')
@section('title_gradient', 'bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-400 dark:via-teal-400 dark:to-cyan-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-emerald-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Halaman yang Anda cari tidak dapat ditemukan. Tautan mungkin rusak, alamat URL salah ketik, atau halaman telah dipindahkan ke menu lain.' }}
@endsection
