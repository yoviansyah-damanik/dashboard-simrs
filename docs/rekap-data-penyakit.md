# Modul Rekapitulasi Data Penyakit & Morbiditas (ICD-10)

Dokumentasi modul pelaporan morbiditas dan 10/20 besar pola penyakit pasien SIMRS.

---

## 1. Fitur Utama
- **Filter Fleksibel**: Pemilihan periode (Bulan/Tahun atau Rentang Tanggal), Status Rawat (Semua / Poli / IGD / Ralan / Ranap), Kategori Kasus Khusus (ISK, ISPA, TBC, Diare, DBD, Tifoid, Jantung, Hipertensi, DM, Dispepsia, IDO), Prioritas Diagnosa (Primer / Sekunder), Jenis Kasus (Baru / Lama), Jenis Kelamin (L / P), serta Pencarian teks (Kode ICD-10 & Nama Penyakit).
- **Kasus Khusus & Surveilans Klinis**: Pengelompokan kode ICD-10 resmi Kemenkes:
  - *Infeksi Saluran Kemih (ISK)*: `N39.0`, `N30` (Sistitis), `N10-N12` (Pielonefritis), `N34` (Uretritis), `T83.5` (CAUTI), `O23` (ISK Kehamilan).
  - *Penyakit Jantung*: `I05-I09` (Rematik), `I11` (HHD), `I20-I25` (PJK / Iskemik / AMI), `I26-I28` (Pulmonal), `I30-I38` (Katup & Perikard), `I40-I43` (Kardiomiopati), `I44-I49` (Aritmia), `I50-I52` (Gagal Jantung / Decompensatio Cordis).
  - *Kasus Surveilans Lainnya*: ISPA/Pneumonia (`J00-J22`), TBC (`A15-A19`), Diare/GEA (`A00-A09`, `K52.9`), DBD (`A90`, `A91`), Tifoid (`A01`), Hipertensi (`I10-I15`), DM (`E10-E14`), Dispepsia/Gastritis (`K29`, `K30`), IDO (`T81.4`).
- **Executive KPI Cards**: Total Kasus Terinput, Penyakit Unik, Pasien Unik, Kasus Baru vs Lama, Diagnosa Primer vs Sekunder, dan Demografi Gender + breakdown instalasi (Poli, IGD, Ranap).
- **Grafik Interaktif Real-Time**: Bar chart *10 Besar Penyakit Terbanyak* sinkron dengan Livewire via event `disease-chart-updated`, mendukung 3 mode tampilan: **Bertumpuk (Stacked)**, **Berdampingan (Grouped)**, dan **Total Kasus**, dilengkapi tooltip rincian gender & kasus baru/lama.
- **Tabel Morbiditas Berpaginasi**: Menampilkan Kode ICD-10, Nama Penyakit, rincian Kasus Baru (L/P), Kasus Lama (L/P), Total Gender (L/P), Klasifikasi Layanan (Poli / IGD / Ranap), Total Kasus, dan Persentase.
- **Rekapitulasi Kasus Per Bulan (Tahunan)**: Menampilkan tabel rincian bulanan (Januari s.d. Desember) saat filter periode memilih *Semua Bulan (Setahun)*, mencakup rincian Kasus Baru/Lama, Diagnosa Primer/Sekunder, Gender L/P, Layanan (Poli, IGD, Total Ralan, Ranap), Kelompok Dinas (TNI, POLRI, Non-Dinas), Penyakit & Pasien Unik, Total Kasus, Persentase, serta tombol pintas pemilihan bulan.
- **Pemisahan Ralan (Poli & IGD)**: Setiap penampilan data rawat jalan dipisahkan menjadi Poliklinik (`kd_poli != 'IGDK'`) dan Gawat Darurat (`kd_poli = 'IGDK'`), serta menampilkan data keduanya.
- **Modal Drilldown Kasus Pasien**: Rincian kunjungan pasien per penyakit (No. RM, Nama, Umur, JK, Tgl Rawat, DPJP, Poliklinik/Ruangan, Penjamin) dengan modifier class `!mt-0` dan badge layanan Poli/IGD/Ranap.
- **Tab Data Kunjungan & Pengunjung Unik**: Navigasi 3 tab data (*1. Morbiditas Penyakit*, *2. Data Kunjungan Pasien*, *3. Data Pengunjung Unik*) terintegrasi dengan pagination dan filter aktif.
- **Mode Sensor Privasi (Data Masking)**: Toggle proteksi kerahasiaan data medis yang secara otomatis menyamarkan 3 digit belakang No. RM (`054***`) dan huruf tengah nama pasien per kata (`Y********H R****I`) baik di tampilan web maupun modal drilldown.
- **Cetak PDF Data Server-side dengan Opsi Sensor**: Ekspor PDF formal berbasis DomPDF untuk Rekapitulasi Morbiditas, Data Kunjungan (`disease-visits-pdf`), Data Pengunjung (`disease-visitors-pdf`), dan Rincian Pasien Diagnosa Spesifik (`disease-drilldown-pdf`) dengan opsi pilihan cetak *Mode Sensor Privasi* vs *Tanpa Sensor (Data Lengkap)*.
- **Ekspor Spreadsheet Excel (.xlsx)**: Ekspor dataset lengkap ke berkas Microsoft Excel (.xlsx) menggunakan `PhpSpreadsheet` native dengan layout dan styling tabel formal (header bergaya emerald/teal, borders, auto-width, text wrapping, format teks RM) untuk Morbiditas Penyakit, Data Kunjungan, Data Pengunjung, serta Rincian Pasien Diagnosa Spesifik dengan opsi Mode Sensor atau Tanpa Sensor.
- **Modul Matriks 12 Bulan & Pertumbuhan MoM Growth Penyakit**: Tab ke-4 (*Matriks 12 Bulan & MoM Growth*) menyajikan analisis komparasi tren bulanan rumah sakit:
  - *Laju Pertumbuhan Tiap Bulan (Monthly MoM Growth %)*: Menghitung dan menyajikan persentase laju pertumbuhan kasus naik/turun (`▲ +xx%` / `▼ -xx%`) di setiap bulannya (Februari s.d. Desember) terhadap bulan sebelumnya.
  - *Pengalih Mode Tampilan Sel*: Kontrol switch tampilan sel matriks (1. *Kasus & % Growth*, 2. *Hanya % Growth*, 3. *Hanya Kasus*).
  - *Kartu KPI MoM*: Laju pertumbuhan Total Kasus, Kasus Baru, Poliklinik (Poli), Gawat Darurat (IGD), dan Rawat Inap (Ranap) terhadap bulan komparasi acuan.
  - *Deteksi Lonjakan Kasus (Surge Alert)*: Indikator peringatan dini diagnosa penyakit dengan lonjakan kasus tertinggi (&ge; 35% dan pertambahan &ge; 15 pasien).
  - *4 Sub-View Analisis*: Penyakit (Top ICD-10 dengan pivot 12 bulan & laju MoM per bulan), Poliklinik & IGD (pemisahan Poli dan IGD eksplisit dengan laju MoM per bulan), Dokter DPJP (laju MoM per bulan), serta Tren Bulanan Rumah Sakit (pertumbuhan MoM per bulan untuk Kasus Baru, Kasus Lama, Poli, IGD, Ranap, dan Total).
  - *Ekspor MoM PDF & Excel*: Template cetak PDF landscape (`disease-mom-pdf.blade.php`) dan workbook Excel multi-sheet terstruktur (Sheet Penyakit, Tren RS, Poli & IGD, Dokter DPJP).

---

## 2. Struktur File
- **Repository**: [app/Repository/DiseaseReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/DiseaseReportRepository.php)
- **Livewire**: [app/Livewire/Icd/Recap.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Icd/Recap.php)
- **View**: [resources/views/pages/icd/recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/icd/recap.blade.php)
- **Template PDF**: [resources/views/reports/disease-recap-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/disease-recap-pdf.blade.php)
- **Navigasi Sidebar**: [app/View/Components/Sidebar.php](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php#L366-L373) (Menu *Rekap Data Penyakit* dengan ikon `i-ph-first-aid`)
- **Rute Web**: [routes/web.php](file:///d:/WebApps/dashboard-simrs/routes/web.php#L185-L194) (`/laporan-data-penyakit`, dengan alias redirect `/icd` dan `/rekap-penyakit`)
- **Pengujian**: [tests/Feature/DiseaseRecapTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/DiseaseRecapTest.php)
