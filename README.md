# Dashboard SIMRS

[![Latest Release](https://img.shields.io/badge/version-v2.1.2-10b981?style=for-the-badge&logo=git&logoColor=white)](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v2.1.2)
[![GitHub Release](https://img.shields.io/github/v/release/yoviansyah-damanik/dashboard-simrs?style=for-the-badge&color=2563eb)](https://github.com/yoviansyah-damanik/dashboard-simrs/releases)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-f43f5e?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire Version](https://img.shields.io/badge/Livewire-3.x-fb7185?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-6366f1?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

Dashboard manajemen rumah sakit internal yang dibangun menggunakan **Laravel 11** dan **Livewire 3**, menyediakan rekapitulasi, laporan, dan tampilan infografis di atas database SIMRS (Sistem Informasi Manajemen Rumah Sakit) yang sudah ada. Dashboard ini bersifat *read-oriented*: memvisualisasikan dan melaporkan data yang dicatat oleh SIMRS rumah sakit, tanpa menduplikasi alur kerja transaksional dari sistem tersebut.

> **Penting:** Dashboard ini hanya dapat digunakan berdampingan dengan **SIMRS Khanza**. Seluruh model, kolom, dan relasi pada koneksi `simrs` dibangun mengikuti skema database SIMRS Khanza (mis. `pasien`, `reg_periksa`, `dokter`, `poliklinik`, `kamar`, `bangsal`, `penjab`, `booking_operasi`, dsb). Dashboard ini **tidak kompatibel** dengan SIMRS lain kecuali struktur tabel database tersebut disesuaikan terlebih dahulu agar sama persis dengan skema SIMRS Khanza.

---

## Tentang SIMRS Khanza

[SIMRS Khanza](https://khanzaid.com/) adalah aplikasi Sistem Informasi Manajemen Rumah Sakit (SIMRS) yang bersifat **open source**, dikembangkan dan dipelihara oleh komunitas **Khanza Open Source Community (KOSC)** di Indonesia. SIMRS Khanza digunakan secara luas oleh rumah sakit, klinik, dan puskesmas di seluruh Indonesia sebagai sistem pencatatan pelayanan kesehatan.

Karakteristik utama SIMRS Khanza:

- **Lisensi**: open source (bebas digunakan, dimodifikasi, dan dikembangkan oleh masing-masing instansi/vendor)
- **Basis teknologi**: PHP/Java dengan database **MySQL/MariaDB**
- **Cakupan modul**: pendaftaran pasien (rawat jalan, rawat inap, IGD), rekam medis, farmasi/apotek, laboratorium, radiologi, gizi, kamar operasi, kasir/keuangan, hingga pelaporan ke BPJS Kesehatan (Bridging SEP/VClaim) dan SATUSEHAT Kemenkes
- **Skema database**: nama tabel dan kolom berbahasa Indonesia yang khas, misalnya `pasien` (data pasien), `reg_periksa` (registrasi/kunjungan), `dokter`, `poliklinik`, `kamar`/`bangsal` (rawat inap), `penjab` (penanggung jawab/jenis pembayaran), `booking_operasi`/`operasi` (kamar operasi), `pasien_tni`/`pasien_polri` (data dinas TNI/POLRI)

Dashboard ini dibangun sebagai lapisan pelaporan dan visualisasi tambahan (*read-only reporting layer*) yang membaca langsung dari skema database SIMRS Khanza milik rumah sakit, tanpa mengubah data operasional di dalamnya.

---

## Arsitektur

Aplikasi ini terhubung ke **dua database**:

- **`mysql`** (koneksi default) — data milik dashboard sendiri: pengguna, peran & hak akses, serta konfigurasi aplikasi.
- **`simrs`** (eksternal, *read-only*) — database SIMRS rumah sakit yang sudah ada (data pasien, pendaftaran, rawat inap/rawat jalan, farmasi, laboratorium, radiologi, dan lain-lain).

Model pada koneksi `simrs` boleh saling berelasi satu sama lain (karena berada di database yang sama), tetapi model pada `simrs` **tidak boleh** direlasikan langsung dengan model pada koneksi default — korelasi antar keduanya harus dilakukan di level kode aplikasi (pemetaan/lookup manual), bukan melalui relasi Eloquent lintas koneksi.

---

## Modul & Fitur Utama

- **Data Master & SDM**: Pasien, Kamar/Bangsal, Poliklinik, serta Tenaga Medis & Non-Medis.
- **Pendaftaran & Demografi**: Registrasi pasien baru vs lama, sebaran demografi pengunjung (umur, gender, wilayah, jenis penjamin/asuransi).
- **Rawat Jalan & Gawat Darurat (IGD)**: Rekapitulasi terstandarisasi dengan pemisahan eksplisit antara **Poliklinik (Poli)** dan **Gawat Darurat (IGD)**, serta filter status registrasi periksa valid.
- **Rawat Inap & Efisiensi Tempat Tidur**: Monitoring kapasitas bangsal/kamar real-time dan perhitungan indikator efisiensi **Barber-Johnson** (BOR, ALOS, TOI, BTO, NDR, GDR).
- **Kamar Operasi (OK)**: Jadwal dan pemantauan tindakan operasi dengan filter status booking, ruang operasi, dan DPJP.
- **Layanan Penunjang Medis**:
  - **Laboratorium**: Rekapitulasi pemeriksaan PK, PA, MB, dan pemantauan nilai kritis (*critical values*).
  - **Radiologi**: Rekapitulasi tindakan dan pemeriksaan diagnostik per jenis rontgen/USG/CT-Scan.
  - **Farmasi**: Rekapitulasi peresepan, tren konsumsi obat, dan pemantauan stok menipis/kedaluwarsa.
  - **Gizi**: Pengelolaan dan rekapitulasi permintaan diet pasien rawat inap.
- **Morbiditas & Prosedur Medis**:
  - **Rekap Penyakit (ICD-10)**: 10 besar penyakit, tren kasus baru vs lama, serta surveilans penyakit khusus (ISK, ISPA, TBC, Diare, DBD, Tifoid, Hipertensi, DM, Dispepsia, IDO).
  - **Rekap Tindakan Medis (ICD-9-CM)**: Visualisasi per bab prosedur (mode Stacked, Grouped, Total Kasus) dan drilldown pasien.
- **Pengendalian Mutu Rumah Sakit**:
  - **INM (Indikator Nasional Mutu)**: Monitoring kepatuhan indikator mutu nasional Kemenkes.
  - **Penilaian Mutu RS**: Insiden Keselamatan Pasien (IKP), Pencegahan & Pengendalian Infeksi (PPI), Kelengkapan Rekam Medis (KLPCM), dan Standar Pelayanan Minimal (SPM).
- **Rekapitulasi Pasien Dinas TNI & POLRI**:
  - Pemisahan data dan pelaporan eksplisit antara personel **TNI** dan **POLRI**.
  - Klasifikasi personel dinas (Militer/Anggota Aktif, ASN/PNS, Keluarga Personel, Purnawirawan).
  - Analisis sebaran satuan kerja / kesatuan asal pasien dinas.
- **Cetak Laporan & Ekspor Data**:
  - Ekspor dokumen server-side berbasis **PDF Data formal (DomPDF)** di seluruh modul (bebas dari screen printing `window.print`).
  - Ekspor data mentah dan rekapitulasi ke format Excel (XLSX) dan CSV.
- **Administrasi & Keamanan**: Manajemen peran dan izin (Spatie Role & Permission), otentikasi aman, dan audit konfigurasi.

---

## Versioning & Riwayat Rilis

Dashboard SIMRS menerapkan prinsip **[Semantic Versioning 2.0.0](https://semver.org/)** (`MAJOR.MINOR.PATCH`). 

### Mekanisme Pengelolaan Versi
1. **Pusat Konfigurasi Versi**: Seluruh riwayat dan rincian perubahan dikelola pada berkas [`version.json`](file:///d:/WebApps/dashboard-simrs/version.json).
2. **Halaman Catatan Rilis Bawaan**: Pengguna terautentikasi dapat melihat timeline dan mencari perubahan versi di halaman menu [`/changelog`](file:///d:/WebApps/dashboard-simrs/resources/views/pages/changelog.blade.php).
3. **Modal Catatan Rilis Cepat**: Badge versi pada halaman Login dapat diklik langsung untuk memunculkan modal dialog changelog tanpa perlu login terlebih dahulu.
4. **Otomatisasi GitHub Releases**: Setiap tag git versi baru (mis. `v2.0.0`) yang dipush ke repositori GitHub akan memicu GitHub Actions (`auto-release.yml`) untuk membuat GitHub Release dan merender changelog secara otomatis dari `version.json`.

### Tabel Riwayat Versi (Changelog Summary)

| Versi | Tanggal | Tipe Rilis | Sorotan Pembaruan | Rilis GitHub |
|:---:|:---:|:---:|---|:---:|
| **`v2.1.2`** | 2026-10-10 | `Patch Release` | Workflow membuat GitHub Release (bukan hanya tag) + backfill semua versi | [Release v2.1.2](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v2.1.2) |
| **`v2.1.1`** | 2026-10-10 | `Patch Release` | Skill rilis versi (label & deskripsi otomatis), judul sidebar dinamis | [Release v2.1.1](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v2.1.1) |
| **`v2.1.0`** | 2026-10-10 | `Minor Release` | Akumulasi Ralan (Poli + IGD), perbaikan grafik invisible, Rekap Diagnosa ICD-10 (Ralan/Ranap/IGD), Rekap Pasien Dinas IGD | [Release v2.1.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v2.1.0) |
| **`v2.0.0`** | 2026-10-08 | `Major Release` | Pemisahan Pasien Dinas TNI vs POLRI, Modul Rekap Morbiditas (ICD-10) & Tindakan (ICD-9-CM), SPM Rumah Sakit, Ekspor DomPDF terstandarisasi | [Release v2.0.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v2.0.0) |
| **`v1.5.0`** | 2026-09-15 | `Feature Update` | Matriks Indikator Penunjang (Lab, Rad, Farmasi), Integrasi Mutu RS (INM, IKP, PPI), Optimasi kueri analitik | [Release v1.5.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.5.0) |
| **`v1.4.0`** | 2026-08-20 | `Feature Update` | Modul Rekap Lab & Nilai Kritis, Rekap Radiologi, Jadwal Operasi (OK), Rekap Resep & Stok Farmasi | [Release v1.4.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.4.0) |
| **`v1.3.0`** | 2026-07-10 | `Feature Update` | Matriks Barber-Johnson (BOR, ALOS, TOI, BTO, NDR, GDR), Monitoring Bed Occupancy, Dukungan Dark Mode | [Release v1.3.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.3.0) |
| **`v1.2.0`** | 2026-06-01 | `Feature Update` | Laporan & Rekapitulasi Rawat Jalan, Poliklinik Spesialis, Pelayanan IGD, Ekspor data awal | [Release v1.2.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.2.0) |
| **`v1.1.0`** | 2026-04-15 | `Enhancement` | Modul registrasi pasien baru vs lama, demografi pengunjung, integrasi penjamin | [Release v1.1.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.1.0) |
| **`v1.0.0`** | 2026-01-01 | `Initial Release` | Fondasi dasar Laravel 11 & Livewire 3, autentikasi, data master pasien & pendaftaran awal | [Release v1.0.0](https://github.com/yoviansyah-damanik/dashboard-simrs/releases/tag/v1.0.0) |

---

## Kebutuhan Sistem

- PHP 8.2+ (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`/`imagick`, `bcmath`)
- Composer 2.x
- Node.js 18+ & npm
- MySQL / MariaDB (Database internal dashboard & database eksternal SIMRS)
- Akses jaringan ke database SIMRS rumah sakit (koneksi `simrs`)

---

## Instalasi

```bash
# 1. Kloning repositori
git clone https://github.com/yoviansyah-damanik/dashboard-simrs.git
cd dashboard-simrs

# 2. Instal dependensi backend & frontend
composer install
npm install

# 3. Salin konfigurasi environment & buat application key
cp .env.example .env
php artisan key:generate
```

Sesuaikan berkas `.env`:

```env
APP_NAME="Dashboard SIMRS"
APP_URL="http://localhost:8000"
HOSPITAL_NAME="Nama Rumah Sakit"

# Koneksi Database Dashboard (Internal)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dashboard_simrs
DB_USERNAME=root
DB_PASSWORD=

# Koneksi Database SIMRS Khanza (Eksternal)
DB_SIMRS_HOST=192.168.1.100
DB_SIMRS_PORT=3306
DB_SIMRS_DATABASE=sik
DB_SIMRS_USERNAME=simrs_reader
DB_SIMRS_PASSWORD=secret
```

Jalankan migrasi database dan seeding data awal:

```bash
php artisan migrate
php artisan db:seed --class=RoleAndPermissionsSeeder
```

Kompilasi aset antarmuka dan jalankan server lokal:

```bash
# Terminal 1: Vite asset builder
npm run dev      # atau: npm run build (untuk produksi)

# Terminal 2: Web server Laravel
php artisan serve
```

---

## Teknologi yang Digunakan

- **Backend**: [Laravel 11](https://laravel.com/docs)
- **Frontend Interaktif**: [Livewire 3](https://livewire.laravel.com/) & [Alpine.js](https://alpinejs.dev/)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/)
- **Visualisasi Grafik**: [Chart.js](https://www.chartjs.org/)
- **Manajemen Hak Akses**: [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)
- **Ekspor Dokumen PDF**: [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)
- **Spreadsheet**: [maatwebsite/excel](https://phpspreadsheet.readthedocs.io/)

---

## Informasi Developer

| | |
|---|---|
| **Nama** | Yoviansyah Rizki Pratama |
| **Email** | [yoviansyahrizkypratama@gmail.com](mailto:yoviansyahrizkypratama@gmail.com) |
| **Telepon/WhatsApp** | [+62 812 2277 8197](https://wa.me/6281222778197) |
| **GitHub** | [@yoviansyah-damanik](https://github.com/yoviansyah-damanik) |

Untuk bantuan pengembangan lebih lanjut, pelaporan kendala, atau permintaan integrasi modul baru, silakan hubungi kontak di atas.


