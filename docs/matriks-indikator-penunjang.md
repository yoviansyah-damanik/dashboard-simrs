# Matriks Indikator Tahunan Layanan Penunjang

## Ringkasan
Halaman evaluasi kinerja dan matriks indikator tahunan 12 bulan (Januari - Desember) untuk layanan penunjang medis diagnostik (**Laboratorium** dan **Radiologi**).

## Fitur Utama
1. **Filter Evaluasi Tahunan**: Pilihan tahun evaluasi dinamis.
2. **KPI Banner**:
   - Total Pemeriksaan Penunjang (Tahunan, Rata-rata/Bulan, Bulan Puncak).
   - Pasien Terlayani (Unik).
   - Asal Pasien (Ralan vs Ranap).
   - Rasio Kontribusi Layanan (Lab vs Radiologi).
3. **Visualisasi Interaktif (Chart.js & Alpine.js)**:
   - Tren bulanan (Garis / Batang).
   - Donut proporsi layanan (Lab vs Rad) & asal pasien (Ralan vs Ranap).
   - Bar modalitas radiologi & kategori lab.
4. **Tabel Matriks 12 Bulan (Tab-based)**:
   - **Semua Penunjang**: Gabungan pemeriksaan, pasien, ralan, ranap, rasio %.
   - **Laboratorium**: Rincian total, pasien, ralan/ranap, PK, PA, MB.
   - **Radiologi**: Rincian total, pasien, ralan/ranap, CR, CT, US, MR, PX, MG.
5. **Cetak / Print Ready**: Format cetak laporan dengan tanda tangan pengesahan.

## File Terkait
- Repository: `app/Repository/AncillaryReportRepository.php`
- Livewire: `app/Livewire/Ancillary/YearlyMatrix.php`
- View: `resources/views/pages/ancillary/yearly-matrix.blade.php`
- Route: `/laporan-indikator-penunjang` & `/penunjang/matriks-tahunan` (`ancillary.yearly-matrix`)
- Sidebar: `app/View/Components/Sidebar.php` (Grup Laporan: *Matriks Indikator Penunjang*)
- Test: `tests/Feature/AncillaryRecapTest.php`
