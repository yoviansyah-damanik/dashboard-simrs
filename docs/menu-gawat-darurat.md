# Menu dan Pemisahan Gawat Darurat (IGD)

## Ringkasan
Pemisahan layanan Gawat Darurat (IGD) dari Rawat Jalan (Ralan/Poliklinik) serta penambahan grup menu Gawat Darurat pada navigasi sidebar.

## Pemisahan IGD dari Rawat Jalan
- **Rawat Jalan**: Menampilkan kunjungan poliklinik reguler dengan `kd_poli != 'IGDK'` dan `status_lanjut = 'Ralan'`. Dropdown filter poliklinik mengecualikan IGD (`excludeIgd: true`).
- **Gawat Darurat**: Menangani seluruh registrasi IGD (`kd_poli = 'IGDK'`), mencakup pasien yang pulang/ralan maupun yang ditransfer/dirawat ke Rawat Inap (`status_lanjut = 'Ranap'`).

## Menu Gawat Darurat
1. **Data Pasien** (`/igd`, route: `emergency`):
   - Daftar pasien masuk IGD dengan filter status pelayanan, jenis pasien (TNI/Polri/Umum), cara bayar, dan dokter jaga.
2. **Rekap** (`/igd/rekap`, route: `emergency.recap`):
   - Rekapitulasi volume kunjungan IGD, rasio transfer ke Ranap vs Ralan, cara bayar, demografi kelompok umur, dan rekap dokter jaga.
3. **Laporan** (`/igd/laporan`, route: `emergency.report`):
   - Laporan analitik berkala IGD, evaluasi tindak lanjut, rekapitulasi dokter jaga, rekap cara bayar, data tabular pasien, serta ekspor Excel.

## File Terkait
- Sidebar: `app/View/Components/Sidebar.php`
- Routes: `routes/web.php` (`Route::prefix('igd')->as('emergency')`)
- Repositories: `app/Repository/EmergencyPatientsRepository.php`, `app/Repository/EmergencyReportRepository.php`
- Livewire: `app/Livewire/Emergency/Index.php`, `app/Livewire/Emergency/Recap.php`, `app/Livewire/Emergency/Report.php`
- Views: `resources/views/pages/emergency/index.blade.php`, `resources/views/pages/emergency/recap.blade.php`, `resources/views/pages/emergency/report.blade.php`
- Testing: `tests/Feature/EmergencyTest.php`
