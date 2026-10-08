# Modul Rekapitulasi Data Penyakit & Morbiditas (ICD-10)

Dokumentasi modul pelaporan morbiditas dan 10/20 besar pola penyakit pasien SIMRS.

---

## 1. Fitur Utama
- **Filter Fleksibel**: Pemilihan periode (Bulan/Tahun atau Rentang Tanggal), Status Rawat (Semua / Poli / IGD / Ralan / Ranap), Kategori Kasus Khusus (ISK, ISPA, TBC, Diare, DBD, Tifoid, Hipertensi, DM, Dispepsia, IDO), Prioritas Diagnosa (Primer / Sekunder), Jenis Kasus (Baru / Lama), Jenis Kelamin (L / P), serta Pencarian teks (Kode ICD-10 & Nama Penyakit).
- **Kasus Khusus & Surveilans Klinis**: Pengelompokan kode ICD-10 resmi Kemenkes:
  - *Infeksi Saluran Kemih (ISK)*: `N39.0`, `N30` (Sistitis), `N10-N12` (Pielonefritis), `N34` (Uretritis), `T83.5` (CAUTI), `O23` (ISK Kehamilan).
  - *Kasus Surveilans Lainnya*: ISPA/Pneumonia (`J00-J22`), TBC (`A15-A19`), Diare/GEA (`A00-A09`, `K52.9`), DBD (`A90`, `A91`), Tifoid (`A01`), Hipertensi (`I10-I15`), DM (`E10-E14`), Dispepsia/Gastritis (`K29`, `K30`), IDO (`T81.4`).
- **Executive KPI Cards**: Total Kasus Terinput, Penyakit Unik, Pasien Unik, Kasus Baru vs Lama, Diagnosa Primer vs Sekunder, dan Demografi Gender + breakdown instalasi (Poli, IGD, Ranap).
- **Grafik Interaktif Real-Time**: Bar chart *10 Besar Penyakit Terbanyak* sinkron dengan Livewire via event `disease-chart-updated`, mendukung 3 mode tampilan: **Bertumpuk (Stacked)**, **Berdampingan (Grouped)**, dan **Total Kasus**, dilengkapi tooltip rincian gender & kasus baru/lama.
- **Tabel Morbiditas Berpaginasi**: Menampilkan Kode ICD-10, Nama Penyakit, rincian Kasus Baru (L/P), Kasus Lama (L/P), Total Gender (L/P), Klasifikasi Layanan (Poli / IGD / Ranap), Total Kasus, dan Persentase.
- **Pemisahan Ralan (Poli & IGD)**: Setiap penampilan data rawat jalan dipisahkan menjadi Poliklinik (`kd_poli != 'IGDK'`) dan Gawat Darurat (`kd_poli = 'IGDK'`), serta menampilkan data keduanya.
- **Modal Drilldown Kasus Pasien**: Rincian kunjungan pasien per penyakit (No. RM, Nama, Umur, JK, Tgl Rawat, DPJP, Poliklinik/Ruangan, Penjamin) dengan modifier class `!mt-0` dan badge layanan Poli/IGD/Ranap.
- **Cetak PDF Data Server-side**: Ekspor PDF data resmi menggunakan DomPDF (`disease-recap-pdf.blade.php`) dengan kop surat formal rumah sakit dan kolom rincian Poli/IGD/Ranap.

---

## 2. Struktur File
- **Repository**: [app/Repository/DiseaseReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/DiseaseReportRepository.php)
- **Livewire**: [app/Livewire/Icd/Recap.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Icd/Recap.php)
- **View**: [resources/views/pages/icd/recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/icd/recap.blade.php)
- **Template PDF**: [resources/views/reports/disease-recap-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/disease-recap-pdf.blade.php)
- **Navigasi Sidebar**: [app/View/Components/Sidebar.php](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php#L366-L373) (Menu *Rekap Data Penyakit* dengan ikon `i-ph-first-aid`)
- **Rute Web**: [routes/web.php](file:///d:/WebApps/dashboard-simrs/routes/web.php#L185-L194) (`/laporan-data-penyakit`, dengan alias redirect `/icd` dan `/rekap-penyakit`)
- **Pengujian**: [tests/Feature/DiseaseRecapTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/DiseaseRecapTest.php)
