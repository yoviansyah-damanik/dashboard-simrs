<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SirsHelper
{
    const KONEKSI = 'simrs';

    /** Cache in-memory (per request) untuk master kelompok umur dari tabel simrs.kelompok_umur. */
    private static ?array $ageGroupCache = null;

    /**
     * Master kelompok umur (kode => ['kode','nama','umur_min','umur_max','urut']) dari tabel
     * simrs.kelompok_umur, diurutkan sesuai kolom urut. Ini SATU-SATUNYA sumber definisi rentang
     * kelompok umur administratif RS (bukan untuk RL 4.1/RL 5.1 yang punya 19-bracket resmi
     * Kemenkes tersendiri via getAgeGroup()/getAgeGroupLabels() di bawah).
     */
    public static function getAgeGroupCategories(): array
    {
        if (self::$ageGroupCache === null) {
            self::$ageGroupCache = DB::connection(self::KONEKSI)
                ->table('kelompok_umur')
                ->orderBy('urut')
                ->get()
                ->keyBy('kode')
                ->map(fn($row) => (array) $row)
                ->toArray();
        }

        return self::$ageGroupCache;
    }

    /** Label kelompok umur (kode => nama), untuk dropdown filter / legend chart. */
    public static function ageGroupCategoryLabels(): array
    {
        return collect(self::getAgeGroupCategories())->pluck('nama', 'kode')->toArray();
    }

    /**
     * Kondisi SQL (dalam hari, via DATEDIFF terhadap tanggal lahir) untuk satu kode kelompok umur.
     * Dipakai untuk filter WHERE maupun agregasi SUM(CASE WHEN ...).
     */
    public static function ageGroupCategoryWhereRaw(string $kode, string $birthDateColumn, string $referenceDateExpr = 'NOW()'): string
    {
        $groups = self::getAgeGroupCategories();
        if (!isset($groups[$kode])) {
            return '1 = 0';
        }

        $umurHari = "DATEDIFF({$referenceDateExpr}, {$birthDateColumn})";
        $condition = "{$umurHari} >= " . (int) $groups[$kode]['umur_min'];
        if ($groups[$kode]['umur_max'] !== null) {
            $condition .= " AND {$umurHari} <= " . (int) $groups[$kode]['umur_max'];
        }

        return $condition;
    }

    /** CASE SQL lengkap (semua kelompok sekaligus) berdasarkan tanggal lahir, mengembalikan kode kelompok umur. */
    public static function ageGroupCategoryCaseSql(string $birthDateColumn, string $referenceDateExpr = 'NOW()'): string
    {
        $cases = '';
        foreach (array_keys(self::getAgeGroupCategories()) as $kode) {
            $cases .= 'WHEN ' . self::ageGroupCategoryWhereRaw($kode, $birthDateColumn, $referenceDateExpr) . " THEN '{$kode}' ";
        }

        return "CASE {$cases}END";
    }

    /**
     * Kondisi SQL untuk satu kode kelompok umur dari kolom umur yang sudah dihitung SIMRS sendiri
     * (mis. reg_periksa.umurdaftar + sttsumur bernilai 'Th'/'Bl'/'Hr'), dikonversi kasar ke hari
     * (1 Th = 365 hari, 1 Bl = 30 hari, 1 Hr = 1 hari) agar sebanding dengan rentang umur_min/umur_max
     * pada tabel kelompok_umur. Dipakai di tabel yang tidak menyimpan tanggal lahir langsung.
     */
    public static function ageGroupCategoryWhereRawFromUmurDaftar(string $kode, string $umurColumn, string $sttsColumn): string
    {
        $groups = self::getAgeGroupCategories();
        if (!isset($groups[$kode])) {
            return '1 = 0';
        }

        $umurHari = "(CASE {$sttsColumn} WHEN 'Th' THEN {$umurColumn} * 365 WHEN 'Bl' THEN {$umurColumn} * 30 ELSE {$umurColumn} END)";
        $condition = "{$umurHari} >= " . (int) $groups[$kode]['umur_min'];
        if ($groups[$kode]['umur_max'] !== null) {
            $condition .= " AND {$umurHari} <= " . (int) $groups[$kode]['umur_max'];
        }

        return $condition;
    }

    /** CASE SQL lengkap (semua kelompok sekaligus) dari kolom umurdaftar/sttsumur, mengembalikan kode kelompok umur. */
    public static function ageGroupCategoryCaseSqlFromUmurDaftar(string $umurColumn, string $sttsColumn): string
    {
        $cases = '';
        foreach (array_keys(self::getAgeGroupCategories()) as $kode) {
            $cases .= 'WHEN ' . self::ageGroupCategoryWhereRawFromUmurDaftar($kode, $umurColumn, $sttsColumn) . " THEN '{$kode}' ";
        }

        return "CASE {$cases}END";
    }

    /** Cache in-memory (per request) untuk peta kd_bangsal => nama_group dari tabel simrs.bangsal_group. */
    private static ?array $wardGroupCache = null;

    /**
     * Peta kd_bangsal => nama_group, dari simrs.detail_bangsal_group dijoin ke simrs.bangsal_group
     * (hanya grup berstatus aktif). Ini SATU-SATUNYA sumber pengelompokan bangsal secara fisik
     * (mis. beberapa kd_bangsal seperti "HRY-PW1-1", "HRY-PW1-2" yang nm_bangsal-nya berbeda tapi
     * satu gedung/grup "Haruaya") — menggantikan pengelompokan ad-hoc berdasar string nm_bangsal.
     */
    public static function getWardGroupMap(): array
    {
        if (self::$wardGroupCache === null) {
            self::$wardGroupCache = DB::connection(self::KONEKSI)
                ->table('detail_bangsal_group')
                ->join('bangsal_group', 'bangsal_group.id_group', '=', 'detail_bangsal_group.id_group')
                ->where('bangsal_group.status', 1)
                ->pluck('bangsal_group.nama_group', 'detail_bangsal_group.kd_bangsal')
                ->toArray();
        }

        return self::$wardGroupCache;
    }

    /**
     * Nama grup bangsal untuk satu kd_bangsal. Bangsal yang belum terpetakan di bangsal_group
     * (mis. data baru yang belum diinput admin SIMRS) jatuh ke $fallbackName (biasanya nm_bangsal
     * bangsal itu sendiri) supaya tetap tampil sebagai grup tersendiri, bukan hilang dari rekap.
     */
    public static function wardGroupName(string $kdBangsal, string $fallbackName): string
    {
        return self::getWardGroupMap()[$kdBangsal] ?? $fallbackName;
    }

    /** Nama bulan dalam bahasa Indonesia */
    public static function getMonthName(int $bulan): string
    {
        $nama = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];

        return $nama[$bulan] ?? '';
    }

    /** Mendapatkan range tanggal awal-akhir bulan dan jumlah hari */
    public static function getDateRange(int $tahun, int $bulan): array
    {
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $startDate = "{$tahun}-{$bulanFormatted}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        $jumlahHari = (int) date('t', strtotime($startDate));

        return [
            'start' => $startDate,
            'end' => $endDate,
            'jumlah_hari' => $jumlahHari,
        ];
    }

    /** Mengambil profil RS dari tabel setting SIMRS */
    public static function getProfilRS(): array
    {
        $setting = DB::connection(self::KONEKSI)
            ->table('setting')
            ->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi')
            ->first();

        if ($setting) {
            return (array) $setting;
        }

        return [
            'nama_instansi' => config('app.hospital_name', 'Rumah Sakit'),
            'alamat_instansi' => '-',
            'kabupaten' => '-',
            'propinsi' => '-',
        ];
    }

    /** Mapping nama bangsal ke jenis pelayanan RL 3.2 (36 jenis) */
    public static function mapWardToServiceType(string $nmBangsal): int
    {
        $nm = strtoupper($nmBangsal);

        if (str_contains($nm, 'UMUM'))
            return 1;
        if (str_contains($nm, 'PENYAKIT DALAM'))
            return 2;
        if (str_contains($nm, 'ANAK') || str_contains($nm, 'PEDIATRI'))
            return 3;
        if (str_contains($nm, 'REMAJA'))
            return 4;
        if (str_contains($nm, 'OBSTETRI') || str_contains($nm, 'BERSALIN'))
            return 5;
        if (str_contains($nm, 'GINEKOLOGI'))
            return 6;
        if (str_contains($nm, 'BEDAH') && !str_contains($nm, 'ORTHOPEDI') && !str_contains($nm, 'SARAF'))
            return 7;
        if (str_contains($nm, 'ORTHOPEDI'))
            return 8;
        if (str_contains($nm, 'SARAF') && str_contains($nm, 'BEDAH'))
            return 9;
        if (str_contains($nm, 'BAKAR'))
            return 10;
        if (str_contains($nm, 'SARAF'))
            return 11;
        if (str_contains($nm, 'JIWA') || str_contains($nm, 'PSIKIATRI'))
            return 12;
        if (str_contains($nm, 'PSIKOLOGI'))
            return 13;
        if (str_contains($nm, 'NAPZA'))
            return 14;
        if (str_contains($nm, 'THT'))
            return 15;
        if (str_contains($nm, 'MATA'))
            return 16;
        if (str_contains($nm, 'KULIT') || str_contains($nm, 'KELAMIN'))
            return 17;
        if (str_contains($nm, 'KARDIOLOGI') || str_contains($nm, 'JANTUNG'))
            return 18;
        if (str_contains($nm, 'PARU'))
            return 19;
        if (str_contains($nm, 'KANKER') || str_contains($nm, 'ONKOLOGI'))
            return 20;
        if (str_contains($nm, 'UROLOGI') || str_contains($nm, 'NEFROLOGI'))
            return 21;
        if (str_contains($nm, 'GERIATRI'))
            return 22;
        if (str_contains($nm, 'KUSTA'))
            return 23;
        if (str_contains($nm, 'RADIOTERAPI'))
            return 24;
        if (str_contains($nm, 'NUKLIR'))
            return 25;
        if (str_contains($nm, 'REHABILITASI'))
            return 26;
        if (str_contains($nm, 'ICU') && !str_contains($nm, 'NICU') && !str_contains($nm, 'PICU') && !str_contains($nm, 'RICU'))
            return 27;
        if (str_contains($nm, 'ICCU') || str_contains($nm, 'ICVCU'))
            return 28;
        if (str_contains($nm, 'RICU'))
            return 29;
        if (str_contains($nm, 'NICU'))
            return 30;
        if (str_contains($nm, 'PICU'))
            return 31;
        if (str_contains($nm, 'ISOLASI'))
            return 32;
        if (str_contains($nm, 'GIGI') || str_contains($nm, 'MULUT'))
            return 33;
        if (str_contains($nm, 'DARURAT') || str_contains($nm, 'IGD'))
            return 34;
        if (str_contains($nm, 'PERINATOLOGI'))
            return 35;

        return 1; // Default: Umum
    }

    /** Mapping nama bangsal ke 5 kategori RL 3.1 */
    public static function mapWardToRL31Category(string $nmBangsal): int
    {
        $nm = strtoupper($nmBangsal);

        if (str_contains($nm, 'ICU') && !str_contains($nm, 'NICU') && !str_contains($nm, 'PICU') && !str_contains($nm, 'RICU')) {
            return 2; // ICU
        }
        if (str_contains($nm, 'NICU'))
            return 3;
        if (str_contains($nm, 'PICU'))
            return 4;
        if (str_contains($nm, 'HCU') || str_contains($nm, 'ICCU') || str_contains($nm, 'ICVCU') || str_contains($nm, 'RICU')) {
            return 5; // Intensif lainnya
        }

        return 1; // Non Intensif
    }

    /** Label 36 jenis pelayanan RL 3.2 */
    public static function getServiceTypeLabels(): array
    {
        return [
            1 => 'Umum',
            2 => 'Penyakit Dalam',
            3 => 'Kesehatan Anak',
            4 => 'Kesehatan Remaja',
            5 => 'Obstetri',
            6 => 'Ginekologi',
            7 => 'Bedah',
            8 => 'Bedah Orthopedi',
            9 => 'Bedah Saraf',
            10 => 'Luka Bakar',
            11 => 'Saraf',
            12 => 'Jiwa',
            13 => 'Psikologi',
            14 => 'Penatalaksana Penyalahgunaan NAPZA',
            15 => 'THT',
            16 => 'Mata',
            17 => 'Kulit dan Kelamin',
            18 => 'Kardiologi',
            19 => 'Paru',
            20 => 'Kanker',
            21 => 'Uronefrologi',
            22 => 'Geriatri',
            23 => 'Kusta',
            24 => 'Radioterapi',
            25 => 'Kedokteran Nuklir',
            26 => 'Rehabilitasi Medik',
            27 => 'ICU',
            28 => 'ICCU/ICVCU',
            29 => 'RICU',
            30 => 'NICU',
            31 => 'PICU',
            32 => 'Isolasi',
            33 => 'Gigi dan Mulut',
            34 => 'Pelayanan Rawat Darurat',
            35 => 'Perinatologi',
        ];
    }

    /** Label 5 kategori RL 3.1 */
    public static function getKategoriRL31Labels(): array
    {
        return [
            1 => 'Non Intensif',
            2 => 'ICU',
            3 => 'NICU',
            4 => 'PICU',
            5 => 'Intensif lainnya',
        ];
    }

    /** 19 kelompok umur untuk RL 4.x */
    public static function getAgeGroup(string $tglLahir, string $tglKeluar): string
    {
        $lahir = strtotime($tglLahir);
        $keluar = strtotime($tglKeluar);
        if (!$lahir || !$keluar)
            return '0-6 Hari';

        $selisihHari = (int) floor(($keluar - $lahir) / 86400);

        if ($selisihHari <= 6)
            return '0-6 Hari';
        if ($selisihHari <= 28)
            return '7-28 Hari';
        if ($selisihHari <= 365)
            return '29 Hari-<1 Thn';

        $tahun = (int) floor($selisihHari / 365.25);

        if ($tahun < 1)
            return '29 Hari-<1 Thn';
        if ($tahun >= 1 && $tahun <= 4)
            return '1-4 Thn';
        if ($tahun >= 5 && $tahun <= 9)
            return '5-9 Thn';
        if ($tahun >= 10 && $tahun <= 14)
            return '10-14 Thn';
        if ($tahun >= 15 && $tahun <= 19)
            return '15-19 Thn';
        if ($tahun >= 20 && $tahun <= 24)
            return '20-24 Thn';
        if ($tahun >= 25 && $tahun <= 29)
            return '25-29 Thn';
        if ($tahun >= 30 && $tahun <= 34)
            return '30-34 Thn';
        if ($tahun >= 35 && $tahun <= 39)
            return '35-39 Thn';
        if ($tahun >= 40 && $tahun <= 44)
            return '40-44 Thn';
        if ($tahun >= 45 && $tahun <= 49)
            return '45-49 Thn';
        if ($tahun >= 50 && $tahun <= 54)
            return '50-54 Thn';
        if ($tahun >= 55 && $tahun <= 59)
            return '55-59 Thn';
        if ($tahun >= 60 && $tahun <= 64)
            return '60-64 Thn';
        if ($tahun >= 65 && $tahun <= 69)
            return '65-69 Thn';
        if ($tahun >= 70 && $tahun <= 74)
            return '70-74 Thn';

        return '>=75 Thn';
    }

    /** Label 19 kelompok umur */
    public static function getAgeGroupLabels(): array
    {
        return [
            '0-6 Hari',
            '7-28 Hari',
            '29 Hari-<1 Thn',
            '1-4 Thn',
            '5-9 Thn',
            '10-14 Thn',
            '15-19 Thn',
            '20-24 Thn',
            '25-29 Thn',
            '30-34 Thn',
            '35-39 Thn',
            '40-44 Thn',
            '45-49 Thn',
            '50-54 Thn',
            '55-59 Thn',
            '60-64 Thn',
            '65-69 Thn',
            '70-74 Thn',
            '>=75 Thn',
        ];
    }

    /**
     * Mengambil jumlah tempat tidur aktif per bangsal.
     * @param bool $excludeTr Kecualikan bangsal dengan kode TR (dipakai di RL 3.1)
     */
    public static function getBedsPerWard(bool $excludeTr = false): array
    {
        $filterTr = $excludeTr ? "AND b.kd_bangsal <> 'TRANS'" : '';

        return DB::connection(self::KONEKSI)->select("
            SELECT b.nm_bangsal, COUNT(k.kd_kamar) as jumlah_tt
            FROM kamar k
            INNER JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
            WHERE b.status = '1' AND k.statusdata = '1' {$filterTr}
            GROUP BY b.nm_bangsal
        ");
    }

    /** Total tempat tidur aktif di seluruh RS (dipakai untuk BOR/ALOS/BTO/TOI di luar laporan SIRS). */
    public static function getActiveBedCount(bool $excludeTr = true): int
    {
        return array_sum(array_column(self::getBedsPerWard($excludeTr), 'jumlah_tt'));
    }

    /** Konversi nilai jenis kelamin (L/P) ke key array (l/p) */
    public static function jkKey(string $jk): string
    {
        return strtoupper($jk) === 'L' ? 'l' : 'p';
    }

    /** Inisialisasi array kosong dengan kolom l, p, total */
    public static function emptyLPTotal(): array
    {
        return ['l' => 0, 'p' => 0, 'total' => 0];
    }

    /** Mapping nama penjamin ke kategori cara bayar RL 3.19 */
    public static function categorizePayor(string $namaPenjab): string
    {
        $nm = strtoupper($namaPenjab);

        if (str_contains($nm, 'BPJS') || str_contains($nm, 'JKN'))
            return 'JKN';
        if (str_contains($nm, 'JAMKESDA') || str_contains($nm, 'JKDA'))
            return 'JAMKESDA';
        if (str_contains($nm, 'JAMKESMAS'))
            return 'JAMKESMAS';
        if (str_contains($nm, 'ASURANSI') || str_contains($nm, 'JASA RAHARJA') || str_contains($nm, 'INHEALTH'))
            return 'ASURANSI';
        if (str_contains($nm, 'PERUSAHAAN') || str_contains($nm, 'IKS'))
            return 'PERUSAHAAN';
        if (str_contains($nm, 'UMUM') || str_contains($nm, 'PRIBADI') || str_contains($nm, 'TUNAI'))
            return 'UMUM/PRIBADI';

        return 'LAIN-LAIN';
    }

    /** Label kategori jenis tenaga untuk RL 2 (Ketenagaan) */
    public static function getRL2CategoryLabels(): array
    {
        return [
            1 => 'Dokter Spesialis',
            2 => 'Dokter Umum',
            3 => 'Dokter Gigi',
            4 => 'Bidan',
            5 => 'Perawat',
            6 => 'Tenaga Kefarmasian',
            7 => 'Tenaga Gizi',
            8 => 'Tenaga Keteknisian Medis (Analis/Radiografer/Rekam Medis)',
            9 => 'Tenaga Kesehatan Masyarakat',
            10 => 'Tenaga Non Kesehatan',
        ];
    }

    /**
     * Mapping jabatan pegawai (teks bebas dari kolom pegawai.jbtn) ke kategori RL 2.
     * Data jabatan di SIMRS berupa teks bebas hasil input manual (bukan kode baku),
     * jadi dipetakan dengan pencocokan kata kunci — mengikuti pola yang sama dengan
     * mapWardToServiceType()/mapWardToRL31Category() di atas.
     */
    public static function mapJabatanToRL2Category(string $jabatan): int
    {
        $jb = strtoupper($jabatan);

        // Awalan peran (Perawat/Bidan/dll) dicek LEBIH DULU daripada kata kunci lokasi/spesialisasi
        // ("Poli Gigi", "Ruang X"), supaya "Perawat Poli Gigi" tidak salah masuk ke Dokter Gigi
        // hanya karena mengandung kata "GIGI".
        if (str_contains($jb, 'SPESIALIS') || str_contains($jb, 'SP.') || str_contains($jb, 'SP '))
            return 1;
        if (str_contains($jb, 'PERAWAT') || str_contains($jb, 'RANAP') || str_contains($jb, 'RAWAT INAP'))
            return 5;
        if (str_contains($jb, 'BIDAN'))
            return 4;
        if (str_contains($jb, 'APOTEK') || str_contains($jb, 'FARMASI'))
            return 6;
        if (str_contains($jb, 'GIZI'))
            return 7;
        if (str_contains($jb, 'ANALIS') || str_contains($jb, 'LAB') || str_contains($jb, 'RADIOGRAFER') || str_contains($jb, 'RADIOLOGI') || str_contains($jb, 'REKAM MEDIS') || str_contains($jb, 'PETUGAS RM'))
            return 8;
        if (str_contains($jb, 'KESMAS') || str_contains($jb, 'KESEHATAN MASYARAKAT'))
            return 9;
        if ((str_contains($jb, 'DOKTER') && str_contains($jb, 'GIGI')) || str_contains($jb, 'DRG'))
            return 3;
        if (str_contains($jb, 'DOKTER') || str_contains($jb, 'ANASTESI') || str_contains($jb, 'UGD'))
            return 2;

        return 10; // Non Kesehatan (administrasi, IT, manajemen, tata usaha, dll)
    }

    /** Label kategori status kepegawaian untuk RL 2 (PNS vs Non-PNS) */
    public static function categorizeEmploymentStatus(string $sttsKerja): string
    {
        // "Tetap" (T) diperlakukan setara PNS/pegawai tetap; sisanya (kontrak, part time,
        // calon kontrak, pegawai istimewa) dikelompokkan sebagai Non-PNS.
        return strtoupper(trim($sttsKerja)) === 'T' ? 'pns' : 'non_pns';
    }
}
