# Laporan Pasien Rawat Jalan

## Deskripsi
Halaman laporan dan rekapitulasi data pasien rawat jalan SIMRS dengan agregasi multi-dimensi per poliklinik/unit, per jenis bayar/penjamin, per jenis kelamin, dan kelompok umur standar SIRS Kemkes.

## Fitur Utama
- **Filter Periode**: Pilihan per bulan, tahun, hari ini, 7 hari, 30 hari, minggu ini, rentang kustom (custom date).
- **Filter Poliklinik**: Semua atau spesifik poliklinik/unit rawat jalan.
- **Filter Penjamin**: Semua, BPJS Kesehatan, Umum/Mandiri, Dinas (TNI/POLRI), atau kode penjamin spesifik.
- **Filter Dokter**: Berdasarkan dokter spesialis / umum pemeriksa.
- **Filter Demografi & Status**: Jenis kelamin (L/P), status kunjungan (Baru/Lama).
- **Ringkasan Metrik**: Total kunjungan, status sudah/belum diperiksa, rasio gender (L/P), breakdown penjamin, dan rasio pasien baru.
- **Tab Rekapitulasi**:
  1. *Rekap per Poliklinik / Unit* (Total, proporsi, gender, baru/lama, penjamin, status periksa).
  2. *Rekap Jenis Bayar / Penjamin* (Total, proporsi, gender, baru/lama).
  3. *Demografi & Kelompok Umur* (Klasifikasi standar SIRS Kemkes: neonatus, bayi, balita, anak, remaja, dewasa, lansia).
- **Visualisasi Grafik (Charts)**:
  1. *Tren Kunjungan Rawat Jalan*: Line chart kurva halus dengan fill area gradien (Total Pasien, Laki-laki, Perempuan).
  2. *Proporsi Penjamin*: Doughnut chart distribusi asuransi & cara bayar.
  3. *Top 8 Poliklinik / Unit*: Horizontal bar chart poliklinik bervolume tertinggi.
  4. *Sebaran Kelompok Umur (SIRS)*: Grouped bar chart komparasi Laki-laki vs Perempuan per 8 kategori umur SIRS.
  5. *Toggle Tampilkan/Sembunyikan*: Tombol kendali visualisasi untuk fleksibilitas tampilan data.
- **Ekspor Dokumen**: Cetak langsung (Window Print), Excel (.xlsx multi-sheet rekap), CSV rekap, dan PDF landscape rekap.

## Lokasi File Terkait
- **Repository**: [OutpatientReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/OutpatientReportRepository.php)
- **Livewire Component**: [Report.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Outpatient/Report.php)
- **Blade View**: [report.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/outpatient/report.blade.php)
- **PDF View**: [outpatient-report-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/outpatient-report-pdf.blade.php)
- **Route**: `route('outpatient.report')` (`/ralan/laporan`)
- **Permission**: `outpatient report`
