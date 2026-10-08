# Modul Gizi: Permintaan Diet & Rekap Permintaan Diet

## 1. Ringkasan Fitur
- **Penyesuaian Modul Gizi**:
  - Di Sidebar kelompok *Layanan Penunjang Medis*, menu Gizi dijadikan grup menu bertingkat:
    - **Permintaan Diet** (`route('nutrition')` / `/gizi`)
    - **Rekap Permintaan Diet** (`route('nutrition.recap')` / `/gizi/rekap`)
- **Daftar Permintaan Diet (`/gizi`)**:
  - Menampilkan data pemberian diet pasien rawat inap dari tabel `detail_beri_diet` SIMRS (bergabung dengan `diet`, `reg_periksa`, `pasien`, `kamar`, `bangsal`, `sisa_diet_pasien`).
  - KPI: Total Permintaan Diet, Porsi Pagi, Porsi Siang, Porsi Sore / Malam, dan Variasi Diet yang Digunakan.
  - Filter: Pencarian (Pasien, RM, Rawat, Kamar), Periode Cepat (Hari Ini, Kemarin, 7 Hari, Minggu Ini, Bulan Ini, Custom), Waktu Makan (Pagi, Siang, Sore), Jenis Diet, dan Bangsal/Ruangan.
  - Terdapat tab untuk beralih antara *Permintaan Diet Pasien* dan *Evaluasi Asuhan Gizi*.
- **Rekap Permintaan Diet (`/gizi/rekap`)**:
  - Filter Periode: Keseluruhan, Hari Ini, 7 Hari, 30 Hari, Minggu Ini, Bulan Ini, Tahun Ini, Pilihan Bulan, Pilihan Tahun, dan Custom.
  - Switcher Tampilan: **Grafik** dan **Dalam Angka**.
  - KPI Banner: Total Porsi Diet, Total Pasien Unik, Komposisi Waktu Makan, dan Rincian Waktu.
  - Visualisasi Grafik:
    - Line Chart: Tren Permintaan Diet Waktu ke Waktu (`chartDietTrend`).
    - Doughnut Chart: Proporsi Waktu Makan Pagi, Siang, Sore (`chartDietWaktu`).
    - Bar Chart: 10 Jenis Diet Paling Banyak Diminta (`chartDietTopJenis`).
    - Bar Chart: 10 Bangsal / Ruangan Penerima Diet Terbanyak (`chartDietBangsal`).
  - Tabel Rincian Rekapitulasi: Distribusi Porsi Pagi, Siang, Sore, Total, dan Rasio (%) per Jenis Diet dan per Bangsal.

## 2. Struktur Komponen & File
- **Repository**: [`NutritionRepository`](file:///d:/WebApps/dashboard-simrs/app/Repository/NutritionRepository.php)
  - `getDietOrders()`: Kueri terpaginasi permintaan diet dengan multi-filter.
  - `getDietSummary()`: Ringkasan statistik cepat.
  - `getDietRecap()`: Data rekapitulasi, grafik, dan tabel rincian.
  - `getMasterDiet()` & `getMasterBangsal()`: Master opsi filter.
- **Livewire Components**:
  - [`App\Livewire\Nutrition\Index`](file:///d:/WebApps/dashboard-simrs/app/Livewire/Nutrition/Index.php)
  - [`App\Livewire\Nutrition\Recap`](file:///d:/WebApps/dashboard-simrs/app/Livewire/Nutrition/Recap.php)
- **Blade Views**:
  - [`resources/views/pages/nutrition/index.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/pages/nutrition/index.blade.php)
  - [`resources/views/pages/nutrition/recap.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/pages/nutrition/recap.blade.php)
- **Menu & Rute**:
  - [`routes/web.php`](file:///d:/WebApps/dashboard-simrs/routes/web.php) & [`Sidebar.php`](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php).
- **Pengujian**:
  - [`NutritionTest`](file:///d:/WebApps/dashboard-simrs/tests/Feature/NutritionTest.php) (6 pengujian lulus, 56 assertions).
