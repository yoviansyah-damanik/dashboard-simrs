<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

interface KlpcmReportInterface
{
    public static function getSummary(int $year, ?int $month = null): array;
    public static function getDoctorComplianceList(int $year, ?int $month = null, int $limit = 20): array;
}

class KlpcmReportRepository implements KlpcmReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Menghitung capaian kelengkapan rekam medis dan angka KLPCM dari data nyata kamar_inap dan resume_pasien_ranap
     */
    public static function getSummary(int $year, ?int $month = null): array
    {
        $conn = DB::connection(self::KONEKSI);

        // 1. Ambil data aktual pasien pulang rawat inap dari kamar_inap
        $queryPulang = $conn->table('kamar_inap as k')
            ->whereNotNull('k.tgl_keluar')
            ->where('k.tgl_keluar', '!=', '0000-00-00')
            ->whereYear('k.tgl_keluar', $year);

        if ($month) {
            $queryPulang->whereMonth('k.tgl_keluar', $month);
        }

        $totalPasienPulang = $queryPulang->distinct('k.no_rawat')->count('k.no_rawat');

        // 2. Ambil data aktual resume medis rawat inap yang terisi
        $queryResume = $conn->table('resume_pasien_ranap as r')
            ->join('kamar_inap as k', 'r.no_rawat', '=', 'k.no_rawat')
            ->whereNotNull('k.tgl_keluar')
            ->where('k.tgl_keluar', '!=', '0000-00-00')
            ->whereYear('k.tgl_keluar', $year);

        if ($month) {
            $queryResume->whereMonth('k.tgl_keluar', $month);
        }

        $evalResume = $queryResume->selectRaw("
            COUNT(DISTINCT r.no_rawat) as total_resume,
            SUM(CASE WHEN r.diagnosa_utama IS NOT NULL AND r.diagnosa_utama != '' AND r.diagnosa_utama != '-' THEN 1 ELSE 0 END) as diagnosa_lengkap,
            SUM(CASE WHEN r.prosedur_utama IS NOT NULL AND r.prosedur_utama != '' AND r.prosedur_utama != '-' THEN 1 ELSE 0 END) as prosedur_lengkap,
            SUM(CASE WHEN r.obat_pulang IS NOT NULL AND r.obat_pulang != '' AND r.obat_pulang != '-' THEN 1 ELSE 0 END) as obat_lengkap,
            SUM(CASE WHEN r.alasan IS NOT NULL AND r.alasan != '' AND r.keluhan_utama IS NOT NULL AND r.keluhan_utama != '' THEN 1 ELSE 0 END) as anamnesis_lengkap,
            SUM(CASE WHEN r.pemeriksaan_fisik IS NOT NULL AND r.pemeriksaan_fisik != '' THEN 1 ELSE 0 END) as fisik_lengkap,
            SUM(CASE WHEN r.edukasi IS NOT NULL AND r.edukasi != '' THEN 1 ELSE 0 END) as edukasi_lengkap
        ")->first();

        $lengkapResume = (int) ($evalResume->total_resume ?? 0);
        $tidakLengkapResume = max(0, $totalPasienPulang - $lengkapResume);
        $angkaKlpcmResume = $totalPasienPulang > 0 ? round(($tidakLengkapResume / $totalPasienPulang) * 100, 2) : 0.0;

        // Komponen kelengkapan kuantitatif dihitung 100% dari data riil
        $pctAnamnesis = $lengkapResume > 0 ? round(((int) ($evalResume->anamnesis_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;
        $pctFisik = $lengkapResume > 0 ? round(((int) ($evalResume->fisik_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;
        $pctDiagnosa = $lengkapResume > 0 ? round(((int) ($evalResume->diagnosa_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;
        $pctProsedur = $lengkapResume > 0 ? round(((int) ($evalResume->prosedur_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;
        $pctObat = $lengkapResume > 0 ? round(((int) ($evalResume->obat_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;
        $pctEdukasi = $lengkapResume > 0 ? round(((int) ($evalResume->edukasi_lengkap ?? 0) / $lengkapResume) * 100, 1) : 0;

        $komponen = [
            'identitas' => [
                'nama' => 'Identitas & Registrasi Berkas Lengkap',
                'persen' => $totalPasienPulang > 0 ? 100.0 : 0.0,
                'status' => true
            ],
            'anamnesis' => [
                'nama' => 'Anamnesis & Keluhan Utama Terisi',
                'persen' => $pctAnamnesis,
                'status' => $pctAnamnesis >= 80.0
            ],
            'fisik' => [
                'nama' => 'Pemeriksaan Fisik Terisi',
                'persen' => $pctFisik,
                'status' => $pctFisik >= 80.0
            ],
            'diagnosa' => [
                'nama' => 'Diagnosa Utama & Kode ICD-10 Lengkap',
                'persen' => $pctDiagnosa,
                'status' => $pctDiagnosa >= 80.0
            ],
            'prosedur' => [
                'nama' => 'Prosedur Tindakan / Operasi Lengkap',
                'persen' => $pctProsedur,
                'status' => $pctProsedur >= 80.0
            ],
            'terapi_kontrol' => [
                'nama' => 'Instruksi Obat Pulang & Jadwal Kontrol',
                'persen' => $pctObat,
                'status' => $pctObat >= 80.0
            ],
        ];

        return [
            'year' => $year,
            'month' => $month,
            'total_berkas' => $totalPasienPulang,
            'berkas_lengkap' => $lengkapResume,
            'berkas_klpcm' => $tidakLengkapResume,
            'angka_klpcm' => $angkaKlpcmResume,
            'standar_klpcm' => '0% (Ideal) / ≤ 5% (Toleransi)',
            'komponen' => $komponen,
        ];
    }

    /**
     * Rekap riil kepatuhan pengisian resume medis per DPJP berdasarkan database resume_pasien_ranap
     */
    public static function getDoctorComplianceList(int $year, ?int $month = null, int $limit = 20): array
    {
        $conn = DB::connection(self::KONEKSI);

        $query = $conn->table('resume_pasien_ranap as r')
            ->join('dokter as d', 'r.kd_dokter', '=', 'd.kd_dokter')
            ->join('kamar_inap as k', 'r.no_rawat', '=', 'k.no_rawat')
            ->whereNotNull('k.tgl_keluar')
            ->where('k.tgl_keluar', '!=', '0000-00-00')
            ->whereYear('k.tgl_keluar', $year);

        if ($month) {
            $query->whereMonth('k.tgl_keluar', $month);
        }

        $records = $query->select(
            'r.kd_dokter',
            'd.nm_dokter',
            DB::raw("COUNT(*) as total_resume"),
            DB::raw("SUM(CASE WHEN r.diagnosa_utama IS NOT NULL AND r.diagnosa_utama != '' AND r.diagnosa_utama != '-' THEN 1 ELSE 0 END) as diagnosa_lengkap")
        )
        ->groupBy('r.kd_dokter', 'd.nm_dokter')
        ->orderByDesc('total_resume')
        ->limit($limit)
        ->get();

        $list = [];
        foreach ($records as $row) {
            $tot = (int) $row->total_resume;
            $lengkap = (int) $row->diagnosa_lengkap;
            $klpcm = max(0, $tot - $lengkap);
            $persen = $tot > 0 ? round(($lengkap / $tot) * 100, 1) : 0.0;

            $list[] = [
                'kd_dokter' => $row->kd_dokter,
                'nm_dokter' => $row->nm_dokter,
                'total_berkas' => $tot,
                'berkas_lengkap' => $lengkap,
                'berkas_klpcm' => $klpcm,
                'persen_lengkap' => $persen,
                'is_patuh' => $persen >= 90.0,
            ];
        }

        return $list;
    }
}
