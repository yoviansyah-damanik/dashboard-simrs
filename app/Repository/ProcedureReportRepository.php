<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProcedureReportRepository
{
    /**
     * Nama koneksi basis data SIMRS
     */
    private const KONEKSI = 'simrs';

    /**
     * Kategori umum prosedur ICD-9-CM (Klasifikasi Bab Operasi & Tindakan Medis)
     */
    public const PROCEDURE_CATEGORIES = [
        'injeksi' => [
            'label' => 'Injeksi, Infus & Farmakoterapi (Bab 99)',
            'short_label' => 'Injeksi & Infus',
            'patterns' => ['99%'],
            'description' => 'Injeksi antibiotik, elektrolit, transfusi, kemoterapi, vaksinasi (99.0-99.9)',
        ],
        'gigi' => [
            'label' => 'Tindakan Gigi & Mulut (Bab 23-24, 27)',
            'short_label' => 'Gigi & Mulut',
            'patterns' => ['23%', '24%', '27%'],
            'description' => 'Penambalan, pencabutan, perawatan saluran akar, drainase mulut (23.0-24.9, 27.0-27.9)',
        ],
        'konsultasi' => [
            'label' => 'Wawancara, Konsultasi & Evaluasi (Bab 89)',
            'short_label' => 'Konsultasi & Evaluasi',
            'patterns' => ['89%'],
            'description' => 'Konsultasi klinis, EKG, pemantauan tanda vital, anamnesis (89.0-89.8)',
        ],
        'radiologi' => [
            'label' => 'Pencitraan Radiologi & USG (Bab 87-88)',
            'short_label' => 'Radiologi & USG',
            'patterns' => ['87%', '88%'],
            'description' => 'Rontgen dada, CT Scan, MRI, USG abdomen, mamografi (87.0-88.9)',
        ],
        'laboratorium' => [
            'label' => 'Pemeriksaan Laboratorium & Mikroskopis (Bab 90-91)',
            'short_label' => 'Laboratorium',
            'patterns' => ['90%', '91%'],
            'description' => 'Pemeriksaan darah, urine, sputum, apusan bakteri, feses (90.0-91.9)',
        ],
        'obgyn' => [
            'label' => 'Persalinan & Prosedur Kebidanan (Bab 72-75)',
            'short_label' => 'Kebidanan / Obgyn',
            'patterns' => ['72%', '73%', '74%', '75%'],
            'description' => 'Sectio Caesarea (SC), ekstraksi vakum, induksi persalinan, episiotomi (72.0-75.9)',
        ],
        'pencernaan' => [
            'label' => 'Operasi Sistem Pencernaan (Bab 42-54)',
            'short_label' => 'Pencernaan',
            'patterns' => ['42%', '43%', '44%', '45%', '46%', '47%', '48%', '49%', '50%', '51%', '52%', '53%', '54%'],
            'description' => 'Apendiktomi, laparotomi, endoskopi, hemoroidektomi, kolesistektomi (42.0-54.9)',
        ],
        'muskuloskeletal' => [
            'label' => 'Operasi Sistem Muskuloskeletal & Tulang (Bab 76-84)',
            'short_label' => 'Tulang & Otot',
            'patterns' => ['76%', '77%', '78%', '79%', '80%', '81%', '82%', '83%', '84%'],
            'description' => 'Reduksi fraktur, fiksasi internal/eksternal, artroskopi, debridement tulang (76.0-84.9)',
        ],
        'perawatan' => [
            'label' => 'Perawatan Luka, Fisioterapi & Rehabilitasi (Bab 93-96)',
            'short_label' => 'Rehab & Luka',
            'patterns' => ['93%', '94%', '95%', '96%'],
            'description' => 'Ganti balutan luka (wound dressing), fisioterapi, intubasi, kateterisasi (93.0-96.9)',
        ],
        'mata' => [
            'label' => 'Operasi & Tindakan Mata (Bab 08-16)',
            'short_label' => 'Mata',
            'patterns' => ['08%', '09%', '10%', '11%', '12%', '13%', '14%', '15%', '16%'],
            'description' => 'Ekstraksi katarak, iridektomi, operasi retina, parasentesis (08.0-16.9)',
        ],
        'kardiovaskular' => [
            'label' => 'Operasi Sistem Kardiovaskular (Bab 35-39)',
            'short_label' => 'Kardiovaskular',
            'patterns' => ['35%', '36%', '37%', '38%', '39%'],
            'description' => 'Kateterisasi jantung, PCI / pasang ring, pintas koroner, pungsi perikard (35.0-39.9)',
        ],
        'lainnya' => [
            'label' => 'Prosedur & Intervensi Lainnya (Bab 00)',
            'short_label' => 'Prosedur Lain (00)',
            'patterns' => ['00%'],
            'description' => 'Ultrasound terapeutik, intervensi vaskular, prosedur baru NEC (00.0-00.9)',
        ],
    ];

    /**
     * Mengambil daftar kategori bab prosedur untuk dropdown filter
     */
    public static function getProcedureCategories(): array
    {
        return self::PROCEDURE_CATEGORIES;
    }

    /**
     * Mengambil daftar tahun yang tersedia dari tabel registrasi periksa
     */
    public static function getAvailableYears(): array
    {
        $years = DB::connection(self::KONEKSI)
            ->table('reg_periksa')
            ->selectRaw('YEAR(tgl_registrasi) as yr')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('yr')
            ->toArray();

        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        return array_map('intval', $years);
    }

    /**
     * Membangun kueri dasar prosedur pasien dengan seluruh filter yang dipilih
     */
    private static function buildBaseQuery(array $filters)
    {
        $query = DB::connection(self::KONEKSI)
            ->table('prosedur_pasien as pp')
            ->join('icd9 as i', 'pp.kode', '=', 'i.kode')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis');

        // Filter Status Registrasi: Jika Ralan, stts not in ('Batal', 'Belum')
        $query->where(function ($q) {
            $q->where('rp.status_lanjut', '!=', 'Ralan')
              ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
        });

        // Filter Rentang Tanggal atau Bulan/Tahun
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('rp.tgl_registrasi', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['year'])) {
            $query->whereYear('rp.tgl_registrasi', $filters['year']);
            if (!empty($filters['month'])) {
                $query->whereMonth('rp.tgl_registrasi', $filters['month']);
            }
        }

        // Filter Status Pelayanan (Poli / IGD / Ralan / Ranap)
        if (!empty($filters['service_status']) && $filters['service_status'] !== 'all') {
            if ($filters['service_status'] === 'IGD') {
                $query->where('rp.kd_poli', 'IGDK');
            } elseif ($filters['service_status'] === 'Poli') {
                $query->where('rp.status_lanjut', 'Ralan')
                      ->where('rp.kd_poli', '!=', 'IGDK');
            } elseif ($filters['service_status'] === 'Ralan') {
                $query->where('rp.status_lanjut', 'Ralan');
            } elseif ($filters['service_status'] === 'Ranap') {
                $query->where('rp.status_lanjut', 'Ranap');
            }
        }

        // Filter Prioritas Tindakan (1 = Utama / Primer, >1 = Sekunder)
        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            if ($filters['priority'] === '1') {
                $query->where('pp.prioritas', 1);
            } elseif ($filters['priority'] === '2') {
                $query->where('pp.prioritas', '>', 1);
            }
        }

        // Filter Jenis Kelamin Pasien
        if (!empty($filters['gender']) && $filters['gender'] !== 'all') {
            $query->where('ps.jk', $filters['gender']);
        }

        // Filter Kategori Bab ICD-9
        if (!empty($filters['category']) && $filters['category'] !== 'all' && isset(self::PROCEDURE_CATEGORIES[$filters['category']])) {
            $patterns = self::PROCEDURE_CATEGORIES[$filters['category']]['patterns'];
            $query->where(function ($q) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $q->orWhere('pp.kode', 'like', $pattern);
                }
            });
        }

        // Filter Pencarian Teks (Kode atau Deskripsi)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('pp.kode', 'like', "%{$search}%")
                  ->orWhere('i.deskripsi_panjang', 'like', "%{$search}%")
                  ->orWhere('i.deskripsi_pendek', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Mengambil ringkasan metrik statistik rekapitulasi tindakan medis
     */
    public static function getSummary(array $filters): array
    {
        $query = self::buildBaseQuery($filters);

        $stats = $query->selectRaw("
            COUNT(*) as total_tindakan,
            COUNT(DISTINCT pp.kode) as total_prosedur_unik,
            COUNT(DISTINCT rp.no_rkm_medis) as total_pasien_unik,
            SUM(CASE WHEN pp.prioritas = 1 THEN 1 ELSE 0 END) as utama,
            SUM(CASE WHEN pp.prioritas > 1 THEN 1 ELSE 0 END) as sekunder,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as wanita,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap
        ")->first();

        $totalTindakan = (int) ($stats->total_tindakan ?? 0);

        return [
            'total_tindakan' => $totalTindakan,
            'total_prosedur_unik' => (int) ($stats->total_prosedur_unik ?? 0),
            'total_pasien_unik' => (int) ($stats->total_pasien_unik ?? 0),
            'utama' => (int) ($stats->utama ?? 0),
            'sekunder' => (int) ($stats->sekunder ?? 0),
            'pria' => (int) ($stats->pria ?? 0),
            'wanita' => (int) ($stats->wanita ?? 0),
            'poli' => (int) ($stats->poli ?? 0),
            'igd' => (int) ($stats->igd ?? 0),
            'ralan' => (int) ($stats->ralan ?? 0),
            'ranap' => (int) ($stats->ranap ?? 0),
        ];
    }

    /**
     * Mengambil Top N Prosedur Terbanyak untuk grafik dan list unggulan
     */
    public static function getTopProcedures(array $filters, int $limit = 10): array
    {
        $query = self::buildBaseQuery($filters);

        $results = $query->selectRaw("
            pp.kode,
            i.deskripsi_panjang,
            i.deskripsi_pendek,
            COUNT(*) as total_tindakan,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN pp.prioritas = 1 THEN 1 ELSE 0 END) as total_utama,
            SUM(CASE WHEN pp.prioritas > 1 THEN 1 ELSE 0 END) as total_sekunder,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('pp.kode', 'i.deskripsi_panjang', 'i.deskripsi_pendek')
        ->orderByDesc('total_tindakan')
        ->limit($limit)
        ->get();

        $labels = [];
        $dataTotal = [];
        $dataPria = [];
        $dataWanita = [];
        $items = [];

        foreach ($results as $idx => $r) {
            $desc = !empty($r->deskripsi_pendek) ? $r->deskripsi_pendek : $r->deskripsi_panjang;
            $shortName = mb_strimwidth($desc, 0, 25, '...');
            $labels[] = "{$r->kode} ({$shortName})";
            $dataTotal[] = (int) $r->total_tindakan;
            $dataPria[] = (int) $r->total_pria;
            $dataWanita[] = (int) $r->total_wanita;

            $items[] = [
                'rank' => $idx + 1,
                'code' => $r->kode,
                'name' => $r->deskripsi_panjang,
                'short_name' => $desc,
                'total' => (int) $r->total_tindakan,
                'pria' => (int) $r->total_pria,
                'wanita' => (int) $r->total_wanita,
                'utama' => (int) $r->total_utama,
                'sekunder' => (int) $r->total_sekunder,
                'poli' => (int) $r->total_poli,
                'igd' => (int) $r->total_igd,
                'ranap' => (int) $r->total_ranap,
            ];
        }

        return [
            'labels' => $labels,
            'data_total' => $dataTotal,
            'data_pria' => $dataPria,
            'data_wanita' => $dataWanita,
            'items' => $items,
        ];
    }

    /**
     * Mengambil data prosedur secara berpaginasi untuk tabel utama
     */
    public static function getProceduresPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $baseQuery = self::buildBaseQuery($filters);

        $aggregateQuery = $baseQuery->selectRaw("
            pp.kode,
            i.deskripsi_panjang,
            i.deskripsi_pendek,
            COUNT(*) as total_tindakan,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN pp.prioritas = 1 THEN 1 ELSE 0 END) as total_utama,
            SUM(CASE WHEN pp.prioritas > 1 THEN 1 ELSE 0 END) as total_sekunder,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as total_ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('pp.kode', 'i.deskripsi_panjang', 'i.deskripsi_pendek')
        ->orderByDesc('total_tindakan');

        return $aggregateQuery->paginate($perPage);
    }

    /**
     * Mengambil daftar seluruh prosedur untuk diekspor ke PDF (hingga limit tertentu)
     */
    public static function getAllProceduresForExport(array $filters, int $limit = 150): array
    {
        $baseQuery = self::buildBaseQuery($filters);

        $rows = $baseQuery->selectRaw("
            pp.kode,
            i.deskripsi_panjang,
            i.deskripsi_pendek,
            COUNT(*) as total_tindakan,
            SUM(CASE WHEN ps.jk = 'L' THEN 1 ELSE 0 END) as total_pria,
            SUM(CASE WHEN ps.jk = 'P' THEN 1 ELSE 0 END) as total_wanita,
            SUM(CASE WHEN pp.prioritas = 1 THEN 1 ELSE 0 END) as total_utama,
            SUM(CASE WHEN pp.prioritas > 1 THEN 1 ELSE 0 END) as total_sekunder,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' THEN 1 ELSE 0 END) as total_poli,
            SUM(CASE WHEN rp.kd_poli = 'IGDK' THEN 1 ELSE 0 END) as total_igd,
            SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as total_ralan,
            SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap
        ")
        ->groupBy('pp.kode', 'i.deskripsi_panjang', 'i.deskripsi_pendek')
        ->orderByDesc('total_tindakan')
        ->limit($limit)
        ->get();

        $totalAll = $rows->sum('total_tindakan') ?: 1;

        $items = [];
        foreach ($rows as $idx => $r) {
            $desc = !empty($r->deskripsi_pendek) ? $r->deskripsi_pendek : $r->deskripsi_panjang;
            $items[] = [
                'rank' => $idx + 1,
                'kode' => $r->kode,
                'deskripsi' => $r->deskripsi_panjang,
                'deskripsi_pendek' => $desc,
                'total_pria' => (int) $r->total_pria,
                'total_wanita' => (int) $r->total_wanita,
                'total_utama' => (int) $r->total_utama,
                'total_sekunder' => (int) $r->total_sekunder,
                'total_poli' => (int) $r->total_poli,
                'total_igd' => (int) $r->total_igd,
                'total_ralan' => (int) $r->total_ralan,
                'total_ranap' => (int) $r->total_ranap,
                'total_tindakan' => (int) $r->total_tindakan,
                'persentase' => round(((int) $r->total_tindakan / $totalAll) * 100, 2),
            ];
        }

        return [
            'items' => $items,
            'total_all' => $totalAll,
        ];
    }

    /**
     * Mengambil riwayat kunjungan pasien untuk drilldown modal suatu tindakan ICD-9
     */
    public static function getPatientDrilldown(string $code, array $filters, int $limit = 50): array
    {
        $baseQuery = self::buildBaseQuery($filters);

        $rows = $baseQuery
            ->where('pp.kode', $code)
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('kamar_inap as ki', function ($join) {
                $join->on('rp.no_rawat', '=', 'ki.no_rawat')
                     ->where('ki.stts_pulang', '!=', 'Pindah Kamar');
            })
            ->leftJoin('kamar as km', 'ki.kd_kamar', '=', 'km.kd_kamar')
            ->leftJoin('bangsal as bg', 'km.kd_bangsal', '=', 'bg.kd_bangsal')
            ->selectRaw("
                rp.no_rawat,
                rp.no_rkm_medis,
                ps.nm_pasien,
                ps.jk,
                rp.tgl_registrasi,
                rp.status_lanjut,
                rp.kd_poli,
                pl.nm_poli,
                bg.nm_bangsal,
                d.nm_dokter,
                pj.png_jawab,
                pp.prioritas,
                pp.status as status_prosedur,
                TIMESTAMPDIFF(YEAR, ps.tgl_lahir, rp.tgl_registrasi) as umur_tahun
            ")
            ->orderByDesc('rp.tgl_registrasi')
            ->limit($limit)
            ->get();

        $patients = [];
        foreach ($rows as $r) {
            $unit = $r->status_lanjut === 'Ranap'
                ? ($r->nm_bangsal ?? 'Rawat Inap')
                : ($r->kd_poli === 'IGDK' ? 'Instalasi Gawat Darurat (IGD)' : ($r->nm_poli ?? 'Poliklinik'));

            $patients[] = [
                'no_rawat' => $r->no_rawat,
                'no_rkm_medis' => $r->no_rkm_medis,
                'nm_pasien' => $r->nm_pasien,
                'jk' => $r->jk,
                'umur' => $r->umur_tahun ?? '-',
                'tgl_registrasi' => $r->tgl_registrasi,
                'status_lanjut' => $r->status_lanjut,
                'kd_poli' => $r->kd_poli,
                'unit' => $unit,
                'dokter' => $r->nm_dokter ?? '-',
                'penjamin' => $r->png_jawab ?? 'Umum',
                'prioritas' => (int) $r->prioritas === 1 ? 'Utama' : 'Sekunder',
            ];
        }

        return $patients;
    }
}
