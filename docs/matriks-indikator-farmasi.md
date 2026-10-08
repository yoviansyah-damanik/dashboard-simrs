# Matriks Indikator Tahunan Farmasi

## Ringkasan
Halaman evaluasi kinerja dan matriks indikator tahunan 12 bulan (Januari - Desember) untuk instalasi farmasi rumah sakit.

## Fitur Utama
1. **Filter Evaluasi Tahunan**: Pemilihan tahun evaluasi dinamis.
2. **KPI Banner**:
   - Total Lembar Resep (Tahunan, Rata-rata/Bulan, Bulan Puncak).
   - Pasien Terlayani (Unik).
   - Asal Peresepan: Ralan vs Ranap (termasuk rincian Ranap Harian & Resep Pulang).
   - Mutu Pelayanan SPM: Rata-rata waktu tunggu (menit) vs standar SPM (≤ 30 menit) & % resep diserahkan.
3. **Visualisasi Interaktif (Chart.js & Alpine.js)**:
   - Tren bulanan lembar resep (Garis / Batang).
   - Donut asal peresepan (Ralan vs Ranap).
   - Donut status penyerahan obat (Sudah vs Belum).
   - Bar jenis resep (Biasa, Kronis, CITO, PRB).
   - Line tren waktu tunggu pelayanan resep vs batas target SPM.
4. **Tabel Matriks 12 Bulan (Tab-based)**:
   - **Ringkasan Utama**: Volume resep, pasien, ralan, ranap (Ranap Harian, Resep Pulang, Total Ranap), diserahkan, waktu tunggu, item obat.
   - **Ralan vs Ranap**: Rincian lembar dan persentase asal peresepan (Ralan, Ranap Harian, Resep Pulang, Total Ranap).
   - **Jenis Resep**: Klasifikasi Biasa, Kronis, CITO, dan PRB.
   - **Mutu & Waktu Tunggu**: Resep diserahkan, % penyerahan, waktu tunggu, dan evaluasi SPM.
5. **Cetak / Print Ready**: Format cetak laporan dengan tanda tangan Kepala Instalasi Farmasi.

## File Terkait
- Repository: `app/Repository/PharmacyReportRepository.php`
- Livewire: `app/Livewire/Pharmacy/YearlyMatrix.php`
- View: `resources/views/pages/pharmacy/yearly-matrix.blade.php`
- Route: `/laporan-indikator-farmasi` & `/farmasi/matriks-tahunan` (`pharmacy.yearly-matrix`)
- Sidebar: `app/View/Components/Sidebar.php` (Grup Laporan: *Matriks Indikator Farmasi*)
- Test: `tests/Feature/PharmacyMatrixTest.php`
