<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportHelper
{
    /**
     * Mengambil profil instansi rumah sakit untuk kop surat laporan PDF resmi
     */
    public static function getHospitalProfile(): array
    {
        return Cache::remember('hospital_report_profile', 3600, function () {
            try {
                $setting = DB::connection('simrs')->table('setting')->first();
            } catch (\Throwable $e) {
                $setting = null;
            }

            $nama = $setting->nama_instansi ?? config('app.hospital_name', 'RUMAH SAKIT');

            $alamatParts = array_filter([
                $setting->alamat_instansi ?? '',
                $setting->kabupaten ?? '',
                $setting->propinsi ?? '',
            ]);
            $alamat = !empty($alamatParts) ? implode(', ', $alamatParts) : 'Alamat Rumah Sakit';
            $kontak = $setting->kontak ?? '-';
            $email = $setting->email ?? '-';

            // Logo: prioritaskan dari blob setting jika ada dan valid, jika tidak gunakan file logo lokal
            $logoBase64 = null;
            if (!empty($setting->logo)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode($setting->logo);
            } else {
                $logoPath = resource_path('images/logo.png');
                if (file_exists($logoPath)) {
                    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                }
            }

            return [
                'nama' => $nama,
                'alamat' => $alamat,
                'telepon' => $kontak,
                'email' => $email,
                'logo' => $logoBase64,
            ];
        });
    }
}
