# Dashboard SIMRS

Dashboard manajemen rumah sakit internal yang dibangun menggunakan **Laravel 11** dan **Livewire 3**, menyediakan rekapitulasi, laporan, dan tampilan infografis di atas database SIMRS (Sistem Informasi Manajemen Rumah Sakit) yang sudah ada. Dashboard ini bersifat *read-oriented*: memvisualisasikan dan melaporkan data yang dicatat oleh SIMRS rumah sakit, tanpa menduplikasi alur kerja transaksional dari sistem tersebut.

## Arsitektur

Aplikasi ini terhubung ke **dua database**:

- **`mysql`** (koneksi default) — data milik dashboard sendiri: pengguna, peran & hak akses, serta konfigurasi aplikasi.
- **`simrs`** (eksternal, sebagian besar hanya dibaca) — database SIMRS rumah sakit yang sudah ada (data pasien, pendaftaran, rawat inap/rawat jalan, farmasi, laboratorium, radiologi, dan lain-lain).

Model pada koneksi `simrs` boleh saling berelasi satu sama lain (karena berada di database yang sama), tetapi model pada `simrs` **tidak boleh** direlasikan langsung dengan model pada koneksi default — korelasi antar keduanya harus dilakukan di level kode aplikasi (pemetaan/lookup manual), bukan melalui relasi Eloquent lintas koneksi.

## Modul

- **Data Master** — Pasien, Kamar, Poliklinik
- **Sumber Daya Manusia** — Tenaga medis & non-medis
- **Pendaftaran** — daftar pendaftaran pasien, rekap, dan Laporan Kunjungan dan Pengunjung
- **Rawat Inap** — daftar pasien rawat inap dan rekap
- **Rawat Jalan** — daftar kunjungan dan rekap
- **IGD** — daftar kunjungan dan rekap
- **Jadwal Operasi** — jadwal booking operasi dengan filter status, ruang, dan dokter
- **Layanan Penunjang Medis** — Gizi, Laboratorium, Radiologi, Farmasi (dengan rekap)
- **Laporan** — Laporan keuangan, laporan pasien (per kelompok personel TNI), rekap ICD-10/ICD-9
- **Catatan Sipil** — Kelahiran dan kematian
- **Administrasi** — Manajemen pengguna, hak akses, akses API, dan pengaturan aplikasi

Setiap modul bertipe daftar umumnya mengikuti pola yang sama: halaman index dengan filter/paginasi, dan halaman rekap/infografis berbasis visualisasi Chart.js beserta kartu statistik, keduanya digerakkan oleh komponen Livewire yang didukung oleh satu kelas repository khusus per domain data.

## Kebutuhan Sistem

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL/MariaDB (untuk koneksi default)
- Akses jaringan ke database SIMRS rumah sakit (untuk koneksi `simrs`)

## Instalasi

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Sesuaikan `.env`:

- `APP_NAME`, `APP_URL`, `HOSPITAL_NAME` — identitas aplikasi dan rumah sakit
- `DB_*` — koneksi database milik dashboard
- `DB_SIMRS_*` — detail koneksi ke database SIMRS rumah sakit

Jalankan migrasi dan seed peran/hak akses:

```bash
php artisan migrate
php artisan db:seed --class=RoleAndPermissionsSeeder
```

Build aset front-end dan jalankan aplikasi:

```bash
npm run dev      # atau: npm run build
php artisan serve
```

## Teknologi yang Digunakan

- [Laravel 11](https://laravel.com/docs)
- [Livewire 3](https://livewire.laravel.com/) dengan Alpine.js
- Tailwind CSS
- [Chart.js](https://www.chartjs.org/) untuk visualisasi rekap/infografis
- [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) untuk kontrol akses berbasis peran
- [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) untuk ekspor PDF

## Informasi Developer

| | |
|---|---|
| **Nama** | Yoviansyah Rizki Pratama |
| **Email** | [yoviansyahrizkypratama@gmail.com](mailto:yoviansyahrizkypratama@gmail.com) |
| **Telepon/WhatsApp** | [+62 812 2277 8197](https://wa.me/6281222778197) |

Untuk bantuan pengembangan lebih lanjut, laporan bug, atau permintaan fitur pada aplikasi ini, silakan hubungi developer melalui kontak di atas.
