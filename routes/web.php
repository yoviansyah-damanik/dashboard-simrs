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
                Route::get('/laporan', \App\Livewire\RegisteredPatient\Report::class)
                    ->middleware('permission:registered-patient report')
                    ->name('.report');
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
        Route::prefix('jadwal-operasi')
            ->as('operation-schedule')
            ->group(function () {
                Route::get('/', \App\Livewire\OperationSchedule\Index::class)
                    ->middleware('permission:operation-schedule show');
                Route::get('/rekap', \App\Livewire\OperationSchedule\Recap::class)
                    ->middleware('permission:operation-schedule recap')
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
        Route::prefix('farmasi')
            ->as('pharmacy')
            ->group(function () {
                Route::get('/', \App\Livewire\Pharmacy\Index::class)
                    ->middleware('permission:pharmacy show')
                    ->name('');
                Route::get('/rekap', \App\Livewire\Pharmacy\Recap::class)
                    ->middleware('permission:pharmacy recap')
                    ->name('.recap');
            });

        // LAPORAN KEUANGAN
        Route::get('/laporan-keuangan', \App\Livewire\FinancialReport\Index::class)
            ->middleware('permission:financial-report show')
            ->name('financial-report');
        Route::get('/laporan-pasien', \App\Livewire\PatientReport\Index::class)
            ->middleware('permission:patient-report show')
            ->name('patient-report');

        Route::get('/icd', \App\Livewire\Icd\Recap::class)
            ->middleware('permission:icd recap')
            ->name('icd');
        Route::get('/icd/icd-10', \App\Livewire\Icd\Icd10::class)
            ->middleware('permission:icd icd10 show')
            ->name('icd.icd10');
        Route::get('/icd/icd-9', \App\Livewire\Icd\Icd9::class)
            ->middleware('permission:icd icd9 show')
            ->name('icd.icd9');

        // SIRS ONLINE
        Route::prefix('sirs')
            ->as('sirs.')
            ->group(function () {
                Route::get('/', \App\Livewire\Sirs\Index::class)
                    ->middleware('permission:sirs.dashboard')
                    ->name('index');
                Route::get('/rl11', \App\Livewire\Sirs\Rl11::class)
                    ->middleware('permission:sirs.rl1')
                    ->name('rl11');
                Route::get('/rl12', \App\Livewire\Sirs\Rl12::class)
                    ->middleware('permission:sirs.rl1')
                    ->name('rl12');
                Route::get('/rl13', \App\Livewire\Sirs\Rl13::class)
                    ->middleware('permission:sirs.rl1')
                    ->name('rl13');
                Route::get('/rl2', \App\Livewire\Sirs\Rl2::class)
                    ->middleware('permission:sirs.rl2')
                    ->name('rl2');
                Route::get('/rl31', \App\Livewire\Sirs\Rl31::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl31');
                Route::get('/rl32', \App\Livewire\Sirs\Rl32::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl32');
                Route::get('/rl33', \App\Livewire\Sirs\Rl33::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl33');
                Route::get('/rl34', \App\Livewire\Sirs\Rl34::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl34');
                Route::get('/rl35', \App\Livewire\Sirs\Rl35::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl35');
                Route::get('/rl36', \App\Livewire\Sirs\Rl36::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl36');
                Route::get('/rl37', \App\Livewire\Sirs\Rl37::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl37');
                Route::get('/rl38', \App\Livewire\Sirs\Rl38::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl38');
                Route::get('/rl39', \App\Livewire\Sirs\Rl39::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl39');
                Route::get('/rl310', \App\Livewire\Sirs\Rl310::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl310');
                Route::get('/rl311', \App\Livewire\Sirs\Rl311::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl311');
                Route::get('/rl312', \App\Livewire\Sirs\Rl312::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl312');
                Route::get('/rl313', \App\Livewire\Sirs\Rl313::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl313');
                Route::get('/rl314', \App\Livewire\Sirs\Rl314::class)
                    ->middleware('permission:sirs.rl3_bulanan')
                    ->name('rl314');
                Route::get('/rl315', \App\Livewire\Sirs\Rl315::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl315');
                Route::get('/rl316', \App\Livewire\Sirs\Rl316::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl316');
                Route::get('/rl317', \App\Livewire\Sirs\Rl317::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl317');
                Route::get('/rl318', \App\Livewire\Sirs\Rl318::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl318');
                Route::get('/rl319', \App\Livewire\Sirs\Rl319::class)
                    ->middleware('permission:sirs.rl3_tahunan')
                    ->name('rl319');
                Route::get('/rl41', \App\Livewire\Sirs\Rl41::class)
                    ->middleware('permission:sirs.rl4')
                    ->name('rl41');
                Route::get('/rl42', \App\Livewire\Sirs\Rl42::class)
                    ->middleware('permission:sirs.rl4')
                    ->name('rl42');
                Route::get('/rl43', \App\Livewire\Sirs\Rl43::class)
                    ->middleware('permission:sirs.rl4')
                    ->name('rl43');
                Route::get('/rl51', \App\Livewire\Sirs\Rl51::class)
                    ->middleware('permission:sirs.rl5')
                    ->name('rl51');
                Route::get('/rl52', \App\Livewire\Sirs\Rl52::class)
                    ->middleware('permission:sirs.rl5')
                    ->name('rl52');
                Route::get('/rl53', \App\Livewire\Sirs\Rl53::class)
                    ->middleware('permission:sirs.rl5')
                    ->name('rl53');
            });

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
