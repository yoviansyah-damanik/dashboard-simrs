# Modul Rekapitulasi Data Tindakan & Prosedur Medis (ICD-9-CM)

Dokumentasi modul pelaporan dan visualisasi prosedur medis (ICD-9-CM) pasien SIMRS.

---

## 1. Fitur Utama
- **Filter Komprehensif**: Mode Periode (Bulan/Tahun atau Rentang Tanggal), Status Rawat (Semua / Poli / IGD / Ralan / Ranap), Kategori Bab ICD-9 (Injeksi, Gigi & Mulut, Konsultasi, Radiologi, Laboratorium, Kebidanan/SC, Pencernaan, Tulang, Rehab/Luka, Mata, Kardiovaskular, Prosedur Lainnya), Prioritas Tindakan (Utama / Sekunder), Jenis Kelamin (L / P), serta Pencarian teks (Kode & Deskripsi Tindakan).
- **Executive KPI Cards**: Total Tindakan Terinput, Prosedur Unik, Pasien Unik, Rincian Layanan Ralan (**Poli** dan **Gawat Darurat / IGD**) dan **Rawat Inap**, serta Proporsi Tindakan Utama vs Sekunder.
- **Pemisahan Ralan (Poli & IGD)**: Setiap menampilkan data Rawat Jalan, data dipisahkan secara eksplisit antara Poliklinik (`kd_poli != 'IGDK'`) dan IGD (`kd_poli = 'IGDK'`) serta menyajikan data keduanya.
- **Filter Status Registrasi**: Sesuai ketentuan, pelayanan Rawat Jalan memfilter `stts NOT IN ('Batal', 'Belum')`.
- **Grafik Interaktif Real-Time**: Bar chart *10 Besar Tindakan Terbanyak* sinkron dengan Livewire via event `procedure-chart-updated`, mendukung 3 mode tampilan: **Bertumpuk (Stacked)**, **Berdampingan (Grouped)**, dan **Total Kasus**, dengan pembatasan lebar batang proporsional (`maxBarThickness`) dan label horizontal rapi.
- **Tabel Rekapitulasi Berpaginasi**: Menampilkan Kode ICD-9, Deskripsi Prosedur, Gender (L/P), Rincian Layanan (Poli / IGD / Ranap), Prioritas (Utama / Sekunder), dan Total Tindakan.
- **Modal Drilldown Pasien**: Rincian kunjungan pasien per tindakan (No. Rawat, No. RM, Nama, JK, Umur, Tgl Rawat, Unit/Layanan, DPJP, Prioritas) dengan class modifier `!mt-0`.
- **Cetak PDF Data Server-side**: Ekspor berkas PDF formal dengan DomPDF (`procedure-recap-pdf.blade.php`), menyertakan kop rumah sakit resmi, parameter filter, ringkasan eksekutif, dan rincian Poli/IGD/Ranap.

---

## 2. Struktur Berkas
- **Repository**: [app/Repository/ProcedureReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/ProcedureReportRepository.php)
- **Livewire**: [app/Livewire/Icd/ProcedureRecap.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Icd/ProcedureRecap.php)
- **View**: [resources/views/pages/icd/procedure-recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/icd/procedure-recap.blade.php)
- **Template PDF**: [resources/views/reports/procedure-recap-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/procedure-recap-pdf.blade.php)
- **Navigasi Sidebar**: [app/View/Components/Sidebar.php](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php) (Menu *Rekap Data Tindakan* dengan ikon `i-ph-syringe`)
- **Rute Web**: [routes/web.php](file:///d:/WebApps/dashboard-simrs/routes/web.php) (`/laporan-data-tindakan`, dengan alias redirect `/icd/icd-9` dan `/rekap-tindakan`)
- **Pengujian**: [tests/Feature/ProcedureRecapTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/ProcedureRecapTest.php)
