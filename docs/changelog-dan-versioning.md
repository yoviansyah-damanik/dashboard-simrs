# Sistem Versioning & Halaman Change Log (v2.0.0)

Dokumentasi manajemen versi dan halaman catatan rilis aplikasi Dashboard SIMRS.

---

## 1. Skema Versioning (`version.json`)
Data rilis dikelola secara terpusat pada berkas `version.json` dengan struktur:
- `version`: Kode versi semantik (saat ini `2.0.0`).
- `date`: Tanggal rilis resmi.
- `title`: Judul ringkasan pembaruan.
- `badge`: Label kategori rilis (`Major Release`, `Feature Update`, `Enhancement`).
- `type`: Tipe rilis (`major` atau `minor`).
- `changeLog`: Larik poin-poin perubahan fitur dan perbaikan.

---

## 2. Fitur Halaman & Modal Change Log
- **Hero & Indikator Versi Aktif**: Menampilkan badge interaktif versi aktif `v2.0.0` dan metadata rilis terbaru.
- **Timeline Rilis Interaktif**: Visualisasi vertikal perjalanan rilis dari versi `v2.0.0` hingga `v1.0.0`.
- **Pencarian Cepat & Filter Rilis**: Filter instan berdasarkan tipe rilis (*Major Release* vs *Feature & Enhancement*) dan pencarian teks kata kunci.
- **Tautan Cepat Sidebar**: Badge versi di pojok kiri atas sidebar dapat diklik langsung untuk membuka halaman `/changelog`.
- **Modal Changelog Halaman Login**: Badge versi di footer formulir login (`login.blade.php`) memicu modal dialog pop-up (`!mt-0`) untuk melihat seluruh riwayat catatan rilis tanpa perlu autentikasi.

---

## 3. Struktur Berkas
- **Berkas Konfigurasi**: [version.json](file:///d:/WebApps/dashboard-simrs/version.json)
- **Helper**: [app/Helpers/GeneralHelper.php](file:///d:/WebApps/dashboard-simrs/app/Helpers/GeneralHelper.php) (`getVersion()`, `getAllVersions()`)
- **Komponen Livewire**: [app/Livewire/ChangeLog.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/ChangeLog.php)
- **Tampilan Blade**: [resources/views/pages/changelog.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/changelog.blade.php)
- **Komponen Header Sidebar**: [resources/views/components/sidebar.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/components/sidebar.blade.php)
- **Navigasi Sidebar**: [app/View/Components/Sidebar.php](file:///d:/WebApps/dashboard-simrs/app/View/Components/Sidebar.php) (Menu *Catatan Rilis (v2.0.0)*)
- **Rute Web**: [routes/web.php](file:///d:/WebApps/dashboard-simrs/routes/web.php) (`/changelog`, dengan alias `/change-log`)
- **Pengujian Otomatis**: [tests/Feature/ChangeLogTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/ChangeLogTest.php)
