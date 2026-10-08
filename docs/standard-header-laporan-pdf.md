# Standard Header & Format Cetak Laporan PDF

## 1. Format Header Resmi (Kop Surat)
Setiap berkas laporan PDF menggunakan komponen terpusat [`reports.partials.header`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/partials/header.blade.php):
- **Logo RS**: Resolusi otomatis dari tabel SIMRS `setting.logo` (Base64) dengan fallback ke file lokal `resources/images/logo.png`.
- **Nama RS**: Diambil dari `setting.nama_instansi` atau konfigurasi aplikasi.
- **Alamat RS**: Alamat lengkap, kabupaten, dan provinsi dari tabel `setting`.
- **Kontak & Email**: Nomor telepon instansi dan alamat email resmi RS.
- **Garis 2 (Double Border)**: Garis batas formal atas (2px solid) dan garis tipis bawah (1px solid).

## 2. Penghapusan Blok Tanda Tangan / Paraf
Sesuai arahan, seluruh blok paraf/tanda tangan (`Mengetahui`, NIP/SIP/STRA) di bagian bawah seluruh 18 template laporan PDF telah dihapus total. Laporan murni menyajikan data analitik dan operasional secara bersih.

## 3. Daftar Template Laporan yang Disesuaikan (18 Berkas)
1. [`inm-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/inm-report-pdf.blade.php) - Indikator Nasional Mutu (INM)
2. [`spm-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/spm-report-pdf.blade.php) - Standar Pelayanan Minimal (SPM)
3. [`ppi-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/ppi-report-pdf.blade.php) - Surveilans HAIs & PPI
4. [`ikp-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/ikp-report-pdf.blade.php) - Insiden Keselamatan Pasien (IKP)
5. [`klpcm-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/klpcm-report-pdf.blade.php) - Rekam Medis & KLPCM
6. [`medical-services-summary-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/medical-services-summary-pdf.blade.php) - Ringkasan Layanan Medis
7. [`ancillary-summary-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/ancillary-summary-pdf.blade.php) - Ringkasan Layanan Penunjang
8. [`indicator-matrix-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/indicator-matrix-pdf.blade.php) - Matriks Indikator Rawat Inap (BOR, ALOS, BTO, TOI)
9. [`ancillary-yearly-matrix-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/ancillary-yearly-matrix-pdf.blade.php) - Matriks Indikator Penunjang Tahunan
10. [`pharmacy-yearly-matrix-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/pharmacy-yearly-matrix-pdf.blade.php) - Matriks Indikator Farmasi Tahunan
11. [`pharmacy-stock-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/pharmacy-stock-pdf.blade.php) - Rekapitulasi Stok Obat Farmasi
12. [`emergency-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/emergency-report-pdf.blade.php) - Laporan Pelayanan IGD
13. [`patient-monthly-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/patient-monthly-report-pdf.blade.php) - Laporan Kunjungan & Pengunjung Pasien
14. [`outpatient-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/outpatient-report-pdf.blade.php) - Laporan Data Rawat Jalan Terpadu
15. [`outpatient-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/outpatient-pdf.blade.php) - Laporan Ringkas Rawat Jalan
16. [`inpatient-report-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/inpatient-report-pdf.blade.php) - Laporan Data Rawat Inap Terpadu
17. [`inpatient-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/inpatient-pdf.blade.php) - Laporan Ringkas Rawat Inap
18. [`patient-pdf.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/reports/patient-pdf.blade.php) - Laporan Data Pasien
