<?php

// Jika butuh bantuan dalam pengembangan ataupun ingin mentraktir kopi, silahkan hubungi saya.
// Yoviansyah Rizki Pratama
// +62 812 2277 8197
// yoviansyahrizkypratama@gmail.com

use Illuminate\Support\Facades\Route;

Route::middleware('guest')
    ->group(function () {
        Route::get('/login', \App\Livewire\Auth\Login::class)
            ->name('login');
    });

Route::middleware('auth')
    ->group(function () {
        Route::get('/logout', \App\Livewire\Auth\Logout::class)
            ->name('logout');

        // DATA MASTER
        Route::get('/', \App\Livewire\Home::class)
            ->name('home');

        Route::prefix('pasien')
            ->as('patient')
            ->group(function () {
                Route::get('/', \App\Livewire\Patient\Index::class);
                Route::get('/rekap', \App\Livewire\Patient\Recap::class)
                    ->name('.recap');
            });

        Route::get('/kamar', \App\Livewire\Room::class)
            ->name('room');
        Route::get('/poliklinik', \App\Livewire\Polyclinic::class)
            ->name('polyclinic');

        // LAYANAN MEDIS
        Route::prefix('pendaftaran')
            ->as('registered-patient')
            ->group(function () {
                Route::get('/', \App\Livewire\RegisteredPatient\Index::class);
                Route::get('/rekap', \App\Livewire\RegisteredPatient\Recap::class)
                    ->name('.recap');
            });
        Route::prefix('ranap')
            ->as('inpatient')
            ->group(function () {
                Route::get('/', \App\Livewire\Inpatient\Index::class);
                Route::get('/rekap', \App\Livewire\Inpatient\Recap::class)
                    ->name('.recap');
            });
        Route::prefix('ralan')
            ->as('outpatient')
            ->group(function () {
                Route::get('/', \App\Livewire\Outpatient\Index::class);
                Route::get('/rekap', \App\Livewire\Outpatient\Recap::class)
                    ->name('.recap');
            });
        Route::prefix('igd')
            ->as('emergency')
            ->group(function () {
                Route::get('/', \App\Livewire\Emergency\Index::class);
                Route::get('/rekap', \App\Livewire\Emergency\Recap::class)
                    ->name('.recap');
            });

        // LAYANAN PENUNJANG MEDIS
        Route::get('/gizi', \App\Livewire\Nutrition::class)
            ->name('nutrition');

        Route::get('/akun', \App\Livewire\Account::class)
            ->name('account');
        Route::get('/akses-api', \App\Livewire\Api::class)
            ->name('api');
        Route::get('/konfigurasi', \App\Livewire\Configuration::class)
            ->name('configuration');
    });
