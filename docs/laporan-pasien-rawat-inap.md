# Laporan Pasien Rawat Inap

## Deskripsi
Halaman laporan data pasien rawat inap berdasarkan query SIMRS Khanza dengan relasi registrasi periksa, kamar inap, bangsal, pasien, dan DPJP ranap.

## Fitur Utama
- **Filter Periode**: Pilihan per bulan (default), tahun, hari ini, rentang kustom (custom date).
- **Filter Penjamin**: Default BPJS Kesehatan (`BPJ`), dapat diubah ke penjamin lain atau semua.
- **Filter Status Pulang**: Semua, Sudah Pulang/Keluar (`tgl_keluar <> '0000-00-00'`), Masih Dirawat (`tgl_keluar = '0000-00-00'`).
- **Filter Bangsal**: Berdasarkan bangsal perawatan.
- **Pencarian**: No. Rawat, No. Rekam Medis, atau Nama Pasien.
- **Ringkasan Metrik**: Total pasien, pasien dirawat, pasien sudah pulang, dan penjamin aktif.
- **Ekspor**: Cetak (Print), Excel (.xlsx), CSV, dan PDF.

## Kolom Laporan
1. No
2. No. Rawat
3. No. RM
4. Nama Pasien
5. Bangsal
6. Tgl Masuk
7. Tgl Keluar (indikator badge jika masih dirawat)
8. Penjamin
9. DPJP Ranap (daftar dokter DPJP via `GROUP_CONCAT`)

## Lokasi File
- **Repository**: [InpatientReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/InpatientReportRepository.php)
- **Livewire Component**: [Report.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Inpatient/Report.php)
- **Blade View**: [report.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/inpatient/report.blade.php)
- **PDF View**: [inpatient-report-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/inpatient-report-pdf.blade.php)
- **Route**: `route('inpatient.report')` (`/ranap/laporan`)
- **Permission**: `inpatient report`
