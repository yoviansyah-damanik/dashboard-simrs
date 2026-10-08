# Matriks Indikator Tahunan Layanan Penunjang

## Ringkasan
Dashboard evaluasi kinerja dan matriks indikator tahunan 12 bulan (Januari - Desember) layanan penunjang medis terpadu: **Laboratorium**, **Radiologi**, **Farmasi**, dan **Gizi**.

## Fitur Utama
1. **Filter Evaluasi Tahunan**: Pemilihan tahun dinamis dengan komputasi agregasi 12 bulan otomatis.
2. **Executive KPI Cards**:
   - Total Pelayanan Penunjang (Farmasi + Lab + Rad + Gizi) beserta rata-rata bulanan & bulan puncak.
   - Pasien Terlayani (akumulasi kunjungan unik 4 unit penunjang).
   - Asal Pasien: Rincian Poli (Rawat Jalan Poliklinik), IGD (Gawat Darurat), Akumulasi Ralan (Poli + IGD), dan Rawat Inap (Ranap).
   - Kontribusi 4 Unit Penunjang (% Farmasi, Lab, Radiologi, Gizi).
3. **Visualisasi Interaktif (Chart.js & Alpine.js)**:
   - Tren bulanan interaktif (Toggle mode Garis / Batang) untuk 4 layanan penunjang.
   - Donut Proporsi Layanan (Farmasi, Lab, Radiologi, Gizi).
   - Donut Asal Pasien (Poli, IGD, Ranap).
   - Bar Klasifikasi Resep Farmasi (Biasa, Kronis, CITO, PRB).
   - Bar Waktu Pemberian Makan Gizi (Pagi, Siang, Sore/Malam).
4. **Tabel Matriks 12 Bulan (5 Tab Navigasi)**:
   - **Semua Penunjang**: Ringkasan eksekutif volume pelayanan 4 unit, pasien unik, asal pasien (Poli, IGD, Akumulasi Ralan, Ranap), dan rasio kontribusi (%).
   - **Farmasi**: Lembar resep, pasien, resep ralan (Poli & IGD), resep ranap harian, resep pulang, total ranap, resep diserahkan, waktu tunggu SPM, dan jenis resep.
   - **Laboratorium**: Pemeriksaan, pasien, asal (Poli, IGD, Akumulasi Ralan, Ranap), kategori PK, PA, MB.
   - **Radiologi**: Pemeriksaan, pasien, asal (Poli, IGD, Akumulasi Ralan, Ranap), modalitas CR, CT, USG, MRI, Panoramic, Mammography.
   - **Gizi & Diet**: Porsi makanan disajikan, pasien diet, distribusi jadwal makan (Pagi, Siang, Sore), asal (Ranap vs Ralan), evaluasi asuhan gizi ADIME, daftar jenis diet terbanyak, sebaran bangsal penerima diet, serta tautan cepat ke modul Permintaan Diet dan Rekap Diet.
5. **Cetak & Pengesahan**: Format cetak laporan ramah printer dengan pengesahan Kepala Instalasi Penunjang Medis.

## File Terkait
- Repository: [AncillaryReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/AncillaryReportRepository.php)
- Livewire Component: [YearlyMatrix.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Ancillary/YearlyMatrix.php)
- Blade View: [yearly-matrix.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/ancillary/yearly-matrix.blade.php)
- Routes: `/laporan-indikator-penunjang` (`ancillary.yearly-matrix`), `/laporan-indikator-farmasi` (redirect ke tab farmasi)
- Sidebar Menu: [Sidebar.php](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php) (Grup Laporan: *Matriks Indikator Penunjang*)
- Automated Tests: [AncillaryRecapTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/AncillaryRecapTest.php), [PharmacyMatrixTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/PharmacyMatrixTest.php)
