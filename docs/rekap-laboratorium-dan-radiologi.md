# Rekap Laboratorium dan Radiologi

## 1. Modul Laboratorium
- **Rute**: `/laboratorium/rekap` (`laboratory.recap`), izin: `laboratory recap`.
- **Komponen**: [Laboratory\Recap](file:///d:/WebApps/dashboard-simrs/app/Livewire/Laboratory/Recap.php) & [LaboratoryRepository](file:///d:/WebApps/dashboard-simrs/app/Repository/LaboratoryRepository.php).
- **Fitur Utama**:
  - Banner 3-Panel: Total Pemeriksaan Lab, Pasien Unik, dan Instalasi Pelayanan (Ralan vs Ranap).
  - 4 Kartu KPI: Rawat Jalan, Rawat Inap, Item Pemeriksaan, dan Dokter Pengirim.
  - Dokter Pengirim: Diambil dari `permintaan_lab.dokter_perujuk`.
  - Grafik Tren: Rangkaian waktu kontinu per periode (Total, Ralan, Ranap).
  - Distribusi: Status Pelayanan (Doughnut), Sebaran Cara Bayar (Bar), dan 10 Pemeriksaan Terbanyak (Bar).
  - Mode *Dalam Angka*: Rincian statistik berbasis kartu (Status, Kategori, 10 Pemeriksaan, Dokter Pengirim, Penjamin).

## 2. Modul Radiologi
- **Rute**: `/radiologi` (`radiology.index`) & `/radiologi/rekap` (`radiology.recap`), izin: `radiology recap`.
- **Komponen**: [Radiology\Index](file:///d:/WebApps/dashboard-simrs/app/Livewire/Radiology/Index.php), [Radiology\Recap](file:///d:/WebApps/dashboard-simrs/app/Livewire/Radiology/Recap.php) & [RadiologyRepository](file:///d:/WebApps/dashboard-simrs/app/Repository/RadiologyRepository.php).
- **Fitur Utama**:
  - Banner 3-Panel: Total Pemeriksaan Radiologi, Pasien Unik, dan Instalasi Pelayanan (Ranap vs Ralan).
  - 4 Kartu KPI: Rawat Inap, Rawat Jalan, Modalitas (Modality), dan Dokter Pengirim.
  - Modality: Klasifikasi standar (CR/X-Ray, CT Scan, USG, MRI, PX, MG) dari `mapping_radiologi_modality` dengan fallback cerdas berbasis nama pemeriksaan. Tersedia di filter list, kartu item, chart donat, dan mode *Dalam Angka*.
  - Dokter Pengirim: Diambil dari `permintaan_radiologi.dokter_perujuk`.
  - Grafik Tren: Rangkaian waktu kontinu per periode (Total, Ranap, Ralan).
  - Distribusi: Modalitas Radiologi (Doughnut), Status Pelayanan (Doughnut), Pemeriksaan Terbanyak (Bar), dan Sebaran Cara Bayar (Bar).
  - Mode *Dalam Angka*: Rincian data numerik instan (Status, Modality, Pemeriksaan Terbanyak, Dokter Pengirim, Penjamin).

