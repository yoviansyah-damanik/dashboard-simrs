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
                Route::get('/', \App\Livewire\Patient\Index::class)
                    ->middleware('permission:patient show');
                Route::get('/rekap', \App\Livewire\Patient\Recap::class)
                    ->middleware('permission:patient recap')
                    ->name('.recap');
            });

        Route::get('/kamar', \App\Livewire\Room\Index::class)
            ->middleware('permission:room show')
            ->name('room');
        Route::get('/poliklinik', \App\Livewire\Polyclinic\Index::class)
            ->middleware('permission:polyclinic show')
            ->name('polyclinic');

        // SDM
        Route::get('/tenaga-medis', \App\Livewire\MedicalPersonnel\Index::class)
            ->middleware('permission:medical-personnel show')
            ->name('medical-personnel');
        Route::get('/tenaga-non-medis', \App\Livewire\NonmedicalPersonnel\Index::class)
            ->middleware('permission:medical-non-personnel show')
            ->name('medical-non-personnel');

        // LAYANAN MEDIS
        Route::prefix('pendaftaran')
            ->as('registered-patient')

            ->group(function () {
                Route::get('/', \App\Livewire\RegisteredPatient\Index::class)
                    ->middleware('permission:registered-patient show');
                Route::get('/rekap', \App\Livewire\RegisteredPatient\Recap::class)
                    ->middleware('permission:registered-patient recap')
                    ->name('.recap');
            });
        Route::prefix('ranap')
            ->as('inpatient')
            ->group(function () {
                Route::get('/', \App\Livewire\Inpatient\Index::class)
                    ->middleware('permission:inpatient show');
                Route::get('/rekap', \App\Livewire\Inpatient\Recap::class)
                    ->middleware('permission:inpatient recap')
                    ->name('.recap');
            });
        Route::prefix('ralan')
            ->as('outpatient')

            ->group(function () {
                Route::get('/', \App\Livewire\Outpatient\Index::class)
                    ->middleware('permission:outpatient show');
                Route::get('/rekap', \App\Livewire\Outpatient\Recap::class)
                    ->middleware('permission:outpatient recap')
                    ->name('.recap');
            });
        Route::prefix('igd')
            ->as('emergency')
            ->group(function () {
                Route::get('/', \App\Livewire\Emergency\Index::class)
                    ->middleware('permission:emergency show');
                Route::get('/rekap', \App\Livewire\Emergency\Recap::class)
                    ->middleware('permission:emergency recap')
                    ->name('.recap');
            });

        // LAYANAN PENUNJANG MEDIS
        Route::get('/gizi', \App\Livewire\Nutrition\Index::class)
            ->middleware('permission:nutrition show')
            ->name('nutrition');

        Route::get('/laboratorium', \App\Livewire\Laboratory\Index::class)
            ->middleware('permission:laboratory show')
            ->name('laboratory');
        Route::get('/radiologi', \App\Livewire\Radiology\Index::class)
            ->middleware('permission:radiology show')
            ->name('radiology');
        Route::get('/farmasi', \App\Livewire\Pharmacy\Index::class)
            ->middleware('permission:pharmacy show')
            ->name('pharmacy');

        // LAPORAN KEUANGAN
        Route::get('/laporan-keuangan', \App\Livewire\FinancialReport\Index::class)
            ->middleware('permission:financial-report show')
            ->name('financial-report');

        Route::get('/icd', \App\Livewire\Icd\Recap::class)
            ->middleware('permission:icd recap')
            ->name('icd');
        Route::get('/icd/icd-10', \App\Livewire\Icd\Icd10::class)
            ->middleware('permission:icd icd10 recap')
            ->name('icd.icd10');
        Route::get('/icd/icd-9', \App\Livewire\Icd\Icd9::class)
            ->middleware('permission:icd icd9 recap')
            ->name('icd.icd9');

        Route::get('/kelahiran', \App\Livewire\Birth\Index::class)
            ->middleware('permission:birth show')
            ->name('birth');
        Route::get('/kematian', \App\Livewire\Death\Index::class)
            ->middleware('permission:death show')
            ->name('death');

        Route::get('/manajemen-pengguna', \App\Livewire\Users\Index::class)
            ->middleware('permission:users')
            ->name('users');
        Route::get('/hak-akses', \App\Livewire\RoleAndPermissions\Index::class)
            ->middleware('permission:role_and_permissions')
            ->name('role-and-permissions');
        Route::get('/akun', \App\Livewire\Account::class)
            ->name('account');
        Route::get('/api', \App\Livewire\Api::class)
            ->name('api');
        Route::get('/pengaturan', \App\Livewire\Configuration::class)
            ->middleware('permission:configuration')
            ->name('configuration');
    });
