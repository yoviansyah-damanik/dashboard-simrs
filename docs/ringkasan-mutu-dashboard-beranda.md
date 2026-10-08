# Ringkasan Mutu & Akreditasi Dashboard Beranda (Home)

Dokumentasi integrasi widget ringkasan eksekutif mutu rumah sakit pada halaman Beranda utama.

---

## 1. Fitur Utama
- **Capaian Indikator Nasional Mutu (INM)**: Rata-rata kepatuhan 13 indikator Kemenkes (Permenkes 30/2022) dan jumlah indikator tercapai.
- **Standar Pelayanan Minimal (SPM)**: Rekapitulasi target terpenuhi di 5 unit (IGD, Rawat Jalan, Rawat Inap, Farmasi, Penunjang Medis) sesuai Kepmenkes 129/2008.
- **Keselamatan Pasien (IKP)**: Rekap insiden dan status kejadian sentinel.
- **Surveilans PPI / HAIs**: Laju infeksi per 1.000 hari pasang alat dan kepatuhan bundle pencegahan.
- **Highlight 6 Indikator INM**: Waktu tunggu rawat jalan BPJS TaskID, kepatuhan Fornas, waktu tanggap SC emergensi, identifikasi pasien, pencegahan pasien jatuh, dan visite DPJP.
- **Kepatuhan SPM 5 Unit**: Indikator persentase capaian per unit pelayanan.
- **Optimalisasi Kecepatan**: Menggunakan `Cache::remember` dengan TTL 10 menit (600 detik) untuk respon instan, dilengkapi tombol sinkronisasi manual (`wire:click="refreshMutuCache"`).

---

## 2. File Terkait
- [app/Livewire/Home.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Home.php): Computed property `mutuSummary` dan method `refreshMutuCache()`.
- [resources/views/pages/home.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/home.blade.php): Section `Mutu & Akreditasi Rumah Sakit`.
- [tests/Feature/HomeExecutiveMutuTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/HomeExecutiveMutuTest.php): Pengujian otomatis integrasi beranda mutu.
