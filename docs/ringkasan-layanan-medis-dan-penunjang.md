# Dokumentasi Ringkasan Layanan Medis & Layanan Penunjang

## 1. Ringkasan Layanan Medis
- **Route:** `/layanan-medis/ringkasan` (`medical-services.summary`)
- **Controller/Livewire:** `App\Livewire\MedicalServices\Summary`
- **Repository:** `App\Repository\MedicalServicesReportRepository`
- **Tampilan:** `resources/views/pages/medical-services/summary.blade.php`
- **Cakupan Data:**
  - **Rawat Jalan (Poli):** Kunjungan poli (non-IGDK), pengunjung (pasien unik), pasien baru vs lama, breakdown 10 poli terbanyak, tren harian.
  - **Gawat Darurat (IGD):** Kunjungan IGD (`kd_poli = 'IGDK'`), pengunjung, alur keluar (pulang, rawat inap, rujuk, meninggal/DOA).
  - **Rawat Inap (Ranap):** Kunjungan ranap, admisi (`tgl_masuk`), keluar/discharge (`tgl_keluar`), pasien aktif (`stts_pulang = '-'`), ALOS (rata-rata lama rawat), breakdown bangsal terbanyak.
  - **Komparasi & Cara Bayar:** Rasio pelayanan per unit, komparasi penjamin (BPJS, Umum, Asuransi Lain), dan Chart.js tren harian.

## 2. Ringkasan Layanan Penunjang Medis
- **Route:** `/penunjang/ringkasan` (`ancillary.summary`)
- **Controller/Livewire:** `App\Livewire\Ancillary\Summary`
- **Repository:** `App\Repository\AncillaryReportRepository::getSummary()`
- **Tampilan:** `resources/views/pages/ancillary/summary.blade.php`
- **Cakupan Data:**
  - **Farmasi:** Total lembar resep, asal peresepan (Poli, IGD, Ranap harian, Resep pulang), klasifikasi (Biasa, Kronis, CITO, PRB), status penyerahan, dan rata-rata waktu tunggu SPM.
  - **Laboratorium:** Total pemeriksaan, total pengunjung, asal unit (Ralan/Poli, IGD, Ranap), kategori (Patologi Klinik, Patologi Anatomi, Mikrobiologi), dan top 6 tindakan lab.
  - **Radiologi:** Total pemeriksaan, total pengunjung, asal unit (Ralan/Poli, IGD, Ranap), modalitas (CR/X-Ray, CT Scan, USG, MRI, Panoramic, Mammography), dan top 6 tindakan radiologi.
  - **Gizi:** 5 Kartu KPI terpisah (Total, Farmasi, Lab, Radiologi, Gizi), total porsi diet disajikan, total pengunjung dilayani, waktu saji (Pagi, Siang, Sore), sebaran bangsal rawat inap terbanyak, admisi ranap vs ralan, evaluasi asuhan ADIME, jenis diet terbanyak, dan tautan langsung ke modul Permintaan Diet serta Rekap Diet.
  - **Komparasi & Tren:** Grafik tren pelayanan harian gabungan, proporsi kontribusi antar unit penunjang, dan rasio asal pasien.

## 3. Integrasi Sidebar
- Item menu **Ringkasan** diletakkan di posisi paling atas pada grup menu **Layanan Medis** dan grup menu **Layanan Penunjang Medis** dengan ikon `i-ph-chart-pie-slice`.
