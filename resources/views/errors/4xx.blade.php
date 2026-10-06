@extends('errors.layout')

@php
    $statusCode = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400;
@endphp

@section('code', $statusCode)
@section('title', 'Permintaan Klien Tidak Valid')

@section('icon_class', 'icon-[solar--danger-triangle-bold-duotone]')
@section('badge_bg', 'bg-amber-500/10 dark:bg-amber-500/15')
@section('badge_text', 'text-amber-600 dark:text-amber-400')
@section('badge_border', 'border border-amber-500/20')
@section('badge_glow', 'bg-amber-500/15')
@section('title_gradient', 'bg-gradient-to-r from-amber-600 via-orange-600 to-rose-600 dark:from-amber-400 dark:via-orange-400 dark:to-rose-400 bg-clip-text text-transparent')

@section('ambient_glow')
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-amber-500/15 rounded-full blur-[130px]"></div>
@endsection

@section('message')
    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Permintaan yang dikirimkan tidak dapat diproses oleh server SIMRS. Silakan periksa kembali data atau tautan yang Anda akses.' }}
@endsection
