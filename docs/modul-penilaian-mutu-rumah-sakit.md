# Sistem Penilaian Mutu & Akreditasi Rumah Sakit

Dokumentasi implementasi modul mutu rumah sakit terpadu (5 Pilar Standar Kemenkes RI & Akreditasi STARKES).

---

## 1. 5 Pilar Modul Mutu Rumah Sakit

| No | Modul | Route URL | Standar Regulasi | Sumber Data SIMRS (100% Riil) |
|---|---|---|---|---|
| 1 | **Indikator Nasional Mutu (INM)** | `/mutu/inm` | Permenkes No. 30/2022 | `reg_periksa`, `pemeriksaan_ralan`, `pemeriksaan_ranap`, `booking_operasi`, `operasi`, `permintaan_lab`, `resep_obat`, `pengaduan`, `balasan_pengaduan`, `penilaian_lanjutan_resiko_jatuh_*` |
| 2 | **Keselamatan Pasien (IKP)** | `/mutu/ikp` | Permenkes No. 11/2017 | `insiden_keselamatan_pasien`, `insiden_keselamatan` (Tanpa data tiruan/fallback) |
| 3 | **Surveilans PPI & HAIs** | `/mutu/ppi` | Permenkes No. 27/2017 | `data_HAIs`, `operasi`, `audit_bundle_isk`, `audit_bundle_vap` |
| 4 | **Standar Pelayanan Minimal (SPM)** | `/mutu/spm` | Kepmenkes No. 129/2008 | `resep_obat` (106k+ resep), `reg_periksa` (61k+ reg), `pemeriksaan_ralan` (69k+ ranap), `kamar_inap` (11k+ ranap), `pasien_mati`, `permintaan_lab` (17k+ lab), `permintaan_radiologi` |
| 5 | **Rekam Medis (KLPCM)** | `/mutu/klpcm` | Permenkes No. 24/2022 | `kamar_inap` (11.192 pasien pulang riil), `resume_pasien_ranap` (7.068 resume riil), `dokter` (Kepatuhan riil per DPJP) |

---

## 2. Arsitektur File & Kode

- **Repositories**:
  - `app/Repository/InmReportRepository.php`
  - `app/Repository/IkpReportRepository.php`
  - `app/Repository/PpiReportRepository.php`
  - `app/Repository/SpmReportRepository.php`
  - `app/Repository/KlpcmReportRepository.php`
- **Livewire Components**:
  - `app/Livewire/Inm/Index.php`
  - `app/Livewire/Mutu/Ikp.php`
  - `app/Livewire/Mutu/Ppi.php`
  - `app/Livewire/Mutu/Spm.php`
  - `app/Livewire/Mutu/KlpcmReport.php`
- **Views**:
  - `resources/views/pages/inm/index.blade.php`
  - `resources/views/pages/mutu/ikp.blade.php`
  - `resources/views/pages/mutu/ppi.blade.php`
  - `resources/views/pages/mutu/spm.blade.php`
  - `resources/views/pages/mutu/klpcm.blade.php`
  - *Seluruh modal menggunakan modifier `!mt-0` untuk tata letak mentok atas.*
- **Navigasi & Hak Akses**:
  - Menu sidebar di bawah grup **Mutu & Akreditasi** (`app/View/Components/Sidebar.php`): Indikator Mutu (INM), Keselamatan Pasien (IKP), Surveilans PPI, Standar Pelayanan (SPM).
  - *Catatan:* Menu **Rekam Medis (KLPCM)** dinonaktifkan / disembunyikan (`'isShown' => false`).
  - Permissions: `mutu.inm`, `mutu.ikp`, `mutu.ppi`, `mutu.spm`, `mutu.klpcm`, `mutu show`.
- **Pengujian**:
  - `tests/Feature/InmTest.php` & `tests/Feature/HospitalQualityModulesTest.php` (11 tests, 93 assertions lulus).

---

## 3. Integrasi Waktu Tunggu & Pelayanan Mobile JKN BPJS (`referensi_mobilejkn_bpjs_taskid`)

| Interval Task ID | Kategori Penilaian | Standar Mutu | Digunakan Pada |
|---|---|---|---|
| **Task 2 - Task 1** | Waktu Tunggu Admisi | ≤ 30 Menit (Target ≥ 80%) | SPM Loket Admisi |
| **Task 3 - Task 2** | Waktu Pelayanan Admisi | ≤ 30 Menit (Target ≥ 80%) | SPM Loket Admisi |
| **Task 4 - Task 3** | Waktu Tunggu Poli (Rawat Jalan) | ≤ 60 Menit (Target ≥ 80%) | INM-05 & SPM Rawat Jalan |
| **Task 5 - Task 4** | Waktu Pelayanan Poli | ≤ 60 Menit (Target ≥ 80%) | SPM Rawat Jalan & Info INM-05 |
| **Task 6 - Task 5** | Waktu Tunggu Farmasi | ≤ 30 Menit (Target ≥ 80%) | SPM Farmasi & Rekap Farmasi |
| **Task 7 - Task 6** | Waktu Pelayanan Farmasi | ≤ 60 Menit (Target ≥ 80%) | SPM Farmasi |

- **Helper**: `App\Helpers\TaskidHelper` (`getWaktuTungguPoli`, `getWaktuPelayananPoli`, `getWaktuTungguFarmasi`, `getWaktuPelayananFarmasi`, `getWaktuTungguAdmisi`, `getWaktuPelayananAdmisi`, `getDetailLogs`).
- **Audit Detail**: Modal drilldown INM & SPM mengambil data riil per nomor rawat dari interval task BPJS.
