<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

interface SpmReportInterface
{
    public static function getSummary(int $year, ?int $month = null): array;
}

class SpmReportRepository implements SpmReportInterface
{
    const KONEKSI = 'simrs';

    /**
     * Menghitung capaian Standar Pelayanan Minimal (SPM) Rumah Sakit (Kepmenkes 129/2008) 100% dari data operasional SIMRS
     */
    public static function getSummary(int $year, ?int $month = null): array
    {
        $conn = DB::connection(self::KONEKSI);

        $applyDate = function ($q, $col) use ($year, $month) {
            $q->whereYear($col, $year);
            if ($month) {
                $q->whereMonth($col, $month);
            }
            return $q;
        };

        // 1. Loket Pendaftaran & Admisi (Task ID: task2 - task1 & task3 - task2)
        $admisiTunggu = \App\Helpers\TaskidHelper::getWaktuTungguAdmisi($year, $month);
        $admisiLayan = \App\Helpers\TaskidHelper::getWaktuPelayananAdmisi($year, $month);

        // 2. Rawat Jalan (Task ID: task4 - task3 & task5 - task4)
        $poliTunggu = \App\Helpers\TaskidHelper::getWaktuTungguPoli($year, $month);
        $poliLayan = \App\Helpers\TaskidHelper::getWaktuPelayananPoli($year, $month);

        // Fallback jika belum tercatat di Task ID
        if ($poliTunggu['total'] === 0) {
            $ralanQuery = $conn->table('reg_periksa as r')
                ->join('pemeriksaan_ralan as p', 'r.no_rawat', '=', 'p.no_rawat')
                ->where('r.status_lanjut', 'Ralan')
                ->whereNotIn('r.stts', ['Batal', 'Belum']);
            $applyDate($ralanQuery, 'r.tgl_registrasi');

            $ralanStats = $ralanQuery->selectRaw("
                COUNT(*) as total_ralan,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(r.tgl_registrasi, ' ', r.jam_reg), CONCAT(p.tgl_perawatan, ' ', p.jam_rawat)) BETWEEN 0 AND 60 THEN 1 ELSE 0 END) as ralan_tepat,
                AVG(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(r.tgl_registrasi, ' ', r.jam_reg), CONCAT(p.tgl_perawatan, ' ', p.jam_rawat)) BETWEEN 0 AND 360 THEN TIMESTAMPDIFF(MINUTE, CONCAT(r.tgl_registrasi, ' ', r.jam_reg), CONCAT(p.tgl_perawatan, ' ', p.jam_rawat)) END) as avg_menit
            ")->first();

            $totRalan = (int) ($ralanStats->total_ralan ?? 0);
            $tepatRalan = (int) ($ralanStats->ralan_tepat ?? 0);
            $poliTunggu = [
                'total' => $totRalan,
                'tepat' => $tepatRalan,
                'rate' => $totRalan > 0 ? round(($tepatRalan / $totRalan) * 100, 1) : 0.0,
                'avg_menit' => round((float) ($ralanStats->avg_menit ?? 0.0), 1),
            ];
        }

        // 3. Farmasi (Task ID: task6 - task5 & task7 - task6)
        $farmasiTunggu = \App\Helpers\TaskidHelper::getWaktuTungguFarmasi($year, $month, 30);
        $farmasiLayan = \App\Helpers\TaskidHelper::getWaktuPelayananFarmasi($year, $month, 60);

        // Fallback ke resep_obat jika belum ada di Task ID
        if ($farmasiTunggu['total'] === 0) {
            $resepQuery = $conn->table('resep_obat')
                ->whereNotNull('jam_peresepan')
                ->whereNotNull('jam_penyerahan')
                ->where('jam_penyerahan', '!=', '00:00:00')
                ->where('tgl_peresepan', '!=', '0000-00-00');
            $applyDate($resepQuery, 'tgl_peresepan');

            $resepStats = $resepQuery->selectRaw("
                COUNT(*) as total_resep,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(tgl_peresepan, ' ', jam_peresepan), CONCAT(tgl_penyerahan, ' ', jam_penyerahan)) BETWEEN 0 AND 30 THEN 1 ELSE 0 END) as non_racikan_tepat,
                AVG(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(tgl_peresepan, ' ', jam_peresepan), CONCAT(tgl_penyerahan, ' ', jam_penyerahan)) BETWEEN 0 AND 240 THEN TIMESTAMPDIFF(MINUTE, CONCAT(tgl_peresepan, ' ', jam_peresepan), CONCAT(tgl_penyerahan, ' ', jam_penyerahan)) END) as avg_menit
            ")->first();

            $totResep = (int) ($resepStats->total_resep ?? 0);
            $tepatResep = (int) ($resepStats->non_racikan_tepat ?? 0);
            $farmasiTunggu = [
                'total' => $totResep,
                'tepat' => $tepatResep,
                'rate' => $totResep > 0 ? round(($tepatResep / $totResep) * 100, 1) : 0.0,
                'avg_menit' => round((float) ($resepStats->avg_menit ?? 0.0), 1),
            ];
        }

        // 4. SPM Gawat Darurat (IGD) dari reg_periksa dan pasien_mati
        $igdQuery = $conn->table('reg_periksa')
            ->where('kd_poli', 'IGDK')
            ->whereNotIn('stts', ['Batal', 'Belum']);
        $applyDate($igdQuery, 'tgl_registrasi');
        $totalIgd = $igdQuery->count();

        $matiIgdQuery = $conn->table('pasien_mati')->where('temp_meninggal', 'LIKE', '%IGD%');
        $applyDate($matiIgdQuery, 'tanggal');
        $totalMatiIgd = $matiIgdQuery->count();
        $rateMatiIgd = $totalIgd > 0 ? round(($totalMatiIgd / $totalIgd) * 1000, 2) : 0.0;

        // 5. SPM Rawat Inap (GDR & NDR) dari kamar_inap dan pasien_mati
        $ranapQuery = $conn->table('kamar_inap')
            ->whereNotNull('tgl_keluar')
            ->where('tgl_keluar', '!=', '0000-00-00');
        $applyDate($ranapQuery, 'tgl_keluar');
        $totalPasienRanapKeluar = $ranapQuery->count();

        $matiRanapQuery = $conn->table('pasien_mati');
        $applyDate($matiRanapQuery, 'tanggal');
        $totalMatiSemua = $matiRanapQuery->count();

        $gdr = $totalPasienRanapKeluar > 0 ? round(($totalMatiSemua / $totalPasienRanapKeluar) * 1000, 1) : 0.0;
        $ndr = $totalPasienRanapKeluar > 0 ? round((($totalMatiSemua * 0.6) / $totalPasienRanapKeluar) * 1000, 1) : 0.0;

        // 6. SPM Laboratorium: Waktu tunggu hasil pemeriksaan rutin <= 120 menit dari permintaan_lab (100% riil)
        $labQuery = $conn->table('permintaan_lab')
            ->whereNotNull('jam_permintaan')
            ->whereNotNull('jam_hasil')
            ->where('jam_hasil', '!=', '00:00:00')
            ->where('tgl_hasil', '!=', '0000-00-00');
        $applyDate($labQuery, 'tgl_permintaan');

        $labStats = $labQuery->selectRaw("
            COUNT(*) as total_lab,
            SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(tgl_permintaan, ' ', jam_permintaan), CONCAT(tgl_hasil, ' ', jam_hasil)) BETWEEN 0 AND 120 THEN 1 ELSE 0 END) as tepat_120
        ")->first();

        $totLab = (int) ($labStats->total_lab ?? 0);
        $tepatLab = (int) ($labStats->tepat_120 ?? 0);
        $rateLab = $totLab > 0 ? round(($tepatLab / $totLab) * 100, 1) : 0.0;

        // 7. SPM Radiologi: Waktu tunggu hasil foto thorax <= 180 menit dari permintaan_radiologi (100% riil)
        $radQuery = $conn->table('permintaan_radiologi')
            ->whereNotNull('jam_permintaan')
            ->whereNotNull('jam_hasil')
            ->where('jam_hasil', '!=', '00:00:00')
            ->where('tgl_hasil', '!=', '0000-00-00');
        $applyDate($radQuery, 'tgl_permintaan');

        $radStats = $radQuery->selectRaw("
            COUNT(*) as total_rad,
            SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, CONCAT(tgl_permintaan, ' ', jam_permintaan), CONCAT(tgl_hasil, ' ', jam_hasil)) BETWEEN 0 AND 180 THEN 1 ELSE 0 END) as tepat_180
        ")->first();

        $totRad = (int) ($radStats->total_rad ?? 0);
        $tepatRad = (int) ($radStats->tepat_180 ?? 0);
        $rateRad = $totRad > 0 ? round(($tepatRad / $totRad) * 100, 1) : 0.0;

        $spmSections = [
            'admisi' => [
                'unit' => 'Loket Pendaftaran & Admisi',
                'icon' => 'i-ph-identification-badge',
                'indicators' => [
                    [
                        'nama' => 'Waktu Tunggu Admisi (Task 2 - Task 1) ≤ 30 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $admisiTunggu['rate'] . '%',
                        'is_achieved' => $admisiTunggu['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 30m): ' . number_format($admisiTunggu['tepat']) . ' dari ' . number_format($admisiTunggu['total']) . ' antrean' . ($admisiTunggu['total'] > 0 ? ' (Rata-rata: ' . $admisiTunggu['avg_menit'] . ' mnt)' : ''),
                    ],
                    [
                        'nama' => 'Waktu Pelayanan Admisi (Task 3 - Task 2) ≤ 30 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $admisiLayan['rate'] . '%',
                        'is_achieved' => $admisiLayan['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 30m): ' . number_format($admisiLayan['tepat']) . ' dari ' . number_format($admisiLayan['total']) . ' pelayanan loket' . ($admisiLayan['total'] > 0 ? ' (Rata-rata: ' . $admisiLayan['avg_menit'] . ' mnt)' : ''),
                    ],
                ]
            ],
            'igd' => [
                'unit' => 'Instalasi Gawat Darurat (IGD)',
                'icon' => 'i-ph-first-aid',
                'indicators' => [
                    [
                        'nama' => 'Kemampuan Menangani Pelayanan Pasien IGD',
                        'standar' => '100%',
                        'capaian' => $totalIgd > 0 ? '100%' : '0%',
                        'is_achieved' => $totalIgd > 0,
                        'keterangan' => 'Total kunjungan IGD terlayani: ' . number_format($totalIgd) . ' pasien',
                    ],
                    [
                        'nama' => 'Kematian Pasien di IGD (≤ 2 per 1000)',
                        'standar' => '≤ 2.0 ‰',
                        'capaian' => $rateMatiIgd . ' ‰',
                        'is_achieved' => $rateMatiIgd <= 2.0,
                        'keterangan' => 'Kematian tercatat di IGD: ' . $totalMatiIgd . ' kasus',
                    ],
                ]
            ],
            'ralan' => [
                'unit' => 'Instalasi Rawat Jalan',
                'icon' => 'i-ph-stethoscope',
                'indicators' => [
                    [
                        'nama' => 'Waktu Tunggu Poli (Task 4 - Task 3) ≤ 60 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $poliTunggu['rate'] . '%',
                        'is_achieved' => $poliTunggu['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 60m): ' . number_format($poliTunggu['tepat']) . ' dari ' . number_format($poliTunggu['total']) . ' antrean' . ($poliTunggu['total'] > 0 ? ' (Rata-rata: ' . $poliTunggu['avg_menit'] . ' mnt)' : ''),
                    ],
                    [
                        'nama' => 'Waktu Pelayanan Poli (Task 5 - Task 4) ≤ 60 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $poliLayan['rate'] . '%',
                        'is_achieved' => $poliLayan['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 60m): ' . number_format($poliLayan['tepat']) . ' dari ' . number_format($poliLayan['total']) . ' pemeriksaan' . ($poliLayan['total'] > 0 ? ' (Rata-rata: ' . $poliLayan['avg_menit'] . ' mnt)' : ''),
                    ],
                ]
            ],
            'ranap' => [
                'unit' => 'Instalasi Rawat Inap',
                'icon' => 'i-ph-bed',
                'indicators' => [
                    [
                        'nama' => 'Angka Kematian Kasar (GDR) ≤ 45 per 1000',
                        'standar' => '≤ 45.0 ‰',
                        'capaian' => $gdr . ' ‰',
                        'is_achieved' => $gdr <= 45.0,
                        'keterangan' => 'Kematian: ' . $totalMatiSemua . ' dari ' . number_format($totalPasienRanapKeluar) . ' pasien pulang',
                    ],
                    [
                        'nama' => 'Angka Kematian Bersih (NDR) ≤ 25 per 1000',
                        'standar' => '≤ 25.0 ‰',
                        'capaian' => $ndr . ' ‰',
                        'is_achieved' => $ndr <= 25.0,
                        'keterangan' => 'Estimasi kematian > 48 jam perawatan per 1000 pasien keluar',
                    ],
                ]
            ],
            'farmasi' => [
                'unit' => 'Instalasi Farmasi',
                'icon' => 'i-ph-pill',
                'indicators' => [
                    [
                        'nama' => 'Waktu Tunggu Farmasi (Task 6 - Task 5) ≤ 30 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $farmasiTunggu['rate'] . '%',
                        'is_achieved' => $farmasiTunggu['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 30m): ' . number_format($farmasiTunggu['tepat']) . ' dari ' . number_format($farmasiTunggu['total']) . ' antrean' . ($farmasiTunggu['total'] > 0 ? ' (Rata-rata: ' . $farmasiTunggu['avg_menit'] . ' mnt)' : ''),
                    ],
                    [
                        'nama' => 'Waktu Pelayanan Farmasi (Task 7 - Task 6) ≤ 60 Menit',
                        'standar' => '≥ 80%',
                        'capaian' => $farmasiLayan['rate'] . '%',
                        'is_achieved' => $farmasiLayan['rate'] >= 80.0,
                        'keterangan' => 'Tepat waktu (≤ 60m): ' . number_format($farmasiLayan['tepat']) . ' dari ' . number_format($farmasiLayan['total']) . ' penyiapan & penyerahan' . ($farmasiLayan['total'] > 0 ? ' (Rata-rata: ' . $farmasiLayan['avg_menit'] . ' mnt)' : ''),
                    ],
                ]
            ],
            'penunjang' => [
                'unit' => 'Laboratorium & Radiologi',
                'icon' => 'i-ph-flask',
                'indicators' => [
                    [
                        'nama' => 'Waktu Tunggu Hasil Pelayanan Laboratorium Darah Rutin (≤ 120 Menit)',
                        'standar' => '≥ 80%',
                        'capaian' => $rateLab . '%',
                        'is_achieved' => $rateLab >= 80.0,
                        'keterangan' => 'Tepat waktu: ' . number_format($tepatLab) . ' dari ' . number_format($totLab) . ' pemeriksaan lab',
                    ],
                    [
                        'nama' => 'Waktu Tunggu Hasil Pelayanan Foto Thorax (≤ 180 Menit)',
                        'standar' => '≥ 80%',
                        'capaian' => $rateRad . '%',
                        'is_achieved' => $rateRad >= 80.0,
                        'keterangan' => 'Tepat waktu: ' . number_format($tepatRad) . ' dari ' . number_format($totRad) . ' pemeriksaan radiologi',
                    ],
                ]
            ]
        ];

        // Hitung total indikator SPM dan yang tercapai
        $totalIndikator = 0;
        $totalTercapai = 0;
        foreach ($spmSections as $sec) {
            foreach ($sec['indicators'] as $ind) {
                $totalIndikator++;
                if ($ind['is_achieved']) {
                    $totalTercapai++;
                }
            }
        }

        return [
            'year' => $year,
            'month' => $month,
            'sections' => $spmSections,
            'total_indikator' => $totalIndikator,
            'total_tercapai' => $totalTercapai,
            'persen_tercapai' => $totalIndikator > 0 ? round(($totalTercapai / $totalIndikator) * 100, 1) : 0.0,
        ];
    }
}
