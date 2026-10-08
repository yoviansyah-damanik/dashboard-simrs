# Modul Indikator Nasional Mutu (INM)

Dokumentasi implementasi Tahap 1: Modul Indikator Nasional Mutu (INM Kemenkes RI) berdasarkan **Permenkes No. 30 Tahun 2022** dan Standar Akreditasi Rumah Sakit (STARKES).

---

## 1. 13 Indikator Nasional Mutu & Sumber Data SIMRS

| No | Kode | Nama Indikator | Standar Target | Sumber Tabel SIMRS |
|---|---|---|---|---|
| 1 | INM-01 | Kepatuhan Kebersihan Tangan (KKT) | $\ge 85\%$ | `audit_cuci_tangan_medis` |
| 2 | INM-02 | Kepatuhan Penggunaan APD | $100\%$ | `audit_kepatuhan_apd` |
| 3 | INM-03 | Kepatuhan Identifikasi Pasien | $100\%$ | `reg_periksa`, `pasien` |
| 4 | INM-04 | Waktu Tanggap Seksio Sesarea Emergensi | $\ge 80\%$ ($\le 30$ mnt) | `operasi` |
| 5 | INM-05 | Waktu Tunggu Rawat Jalan | $\ge 80\%$ ($\le 60$ mnt) | `reg_periksa`, `pemeriksaan_ralan` |
| 6 | INM-06 | Penundaan Operasi Elektif | $< 5\%$ | `booking_operasi` |
| 7 | INM-07 | Kepatuhan Waktu Visite Dokter Spesialis | $\ge 80\%$ (06.00-14.00) | `pemeriksaan_ranap` |
| 8 | INM-08 | Pelaporan Hasil Kritis Laboratorium | $100\%$ ($\le 30$ mnt) | `permintaan_lab` |
| 9 | INM-09 | Kepatuhan Penggunaan Formularium Obat | $\ge 80\%$ | `resep_obat`, `databarang` |
| 10 | INM-10 | Kepatuhan Alur Klinis (Clinical Pathway) | $\ge 80\%$ | `audit_clinical_pathway` |
| 11 | INM-11 | Kepatuhan Pencegahan Risiko Jatuh | $100\%$ | `penilaian_lanjutan_resiko_jatuh_*` |
| 12 | INM-12 | Kecepatan Waktu Tanggap Komplain | $> 80\%$ | `pengaduan`, `balasan_pengaduan` |
| 13 | INM-13 | Kepuasan Pasien dan Pengguna Layanan | $\ge 76.61\%$ | `survei_kepuasan_pelanggan` |

---

## 2. Struktur File & Arsitektur

1. **Repository**: `app/Repository/InmReportRepository.php`
   - Mengkalkulasi nilai agregat numerator, denominator, dan rasio persen.
   - Menyediakan tren bulanan 12 bulan (`getMonthlyTrends()`) untuk visualisasi Chart.js.
   - Menyediakan data drilldown log audit per indikator (`getIndicatorAuditDetails()`).
2. **Livewire Component**: `app/Livewire/Inm/Index.php`
   - Filter tahun, bulan/periode kumulatif, dan kategori indikator.
   - Kontrol interaktif modal audit log per indikator.
3. **Blade View**: `resources/views/pages/inm/index.blade.php`
   - KPI Banner ringkasan: Total Indikator, Tercapai, Belum Tercapai, Rata-rata Skor Mutu.
   - Grafik garis interaktif (Chart.js) dengan garis batas standar target Kemenkes.
   - 13 Kartu Indikator dengan status badge, capaian vs target, progress bar, dan tombol drilldown audit.
   - Modal audit log terintegrasi dengan kelas `!mt-0` agar mentok ke atas.
4. **Routing**: `routes/web.php`
   - Route `GET /mutu/inm` (`mutu.inm`) dan redirect `GET /inm`.
5. **Navigasi Sidebar**: `app/View/Components/Sidebar.php`
   - Grup menu **Mutu & Akreditasi** > **Indikator Mutu (INM)**.
6. **Hak Akses**: `database/seeders/RoleAndPermissionsSeeder.php`
   - Permission: `mutu.inm`, `mutu show`.
7. **Pengujian Unit**: `tests/Feature/InmTest.php`
   - Seluruh 4 pengujian lolos (37 assertions).
