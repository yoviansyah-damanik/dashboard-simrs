# Redesain Halaman Login SIMRS

## Deskripsi
Pembaruan total visual dan antarmuka halaman autentikasi (login) Dashboard SIMRS Rumkit Tk. IV Padangsidimpuan dengan desain modern, estetika militer-medis premium, glassmorphism, dan integrasi identitas kelembagaan.

## Fitur & Peningkatan Desain
- **Visual & Atmosfer**: Latar belakang bertema gelap (`bg-slate-950`) dengan lapisan ambient mesh grid, efek glow halus, dan perpaduan foto rumah sakit bergradien kedalaman.
- **Identitas Institusi**:
  - Tiga emblem resmi: TNI Angkatan Darat, Kesehatan AD (Hesti Wira Sakti Kesdam I/BB), dan Rumah Sakit Tk. IV Padangsidimpuan.
  - Tipografi modern bernuansa tegas dan elegan.
  - Tiga pilar fitur: Keamanan Terstandar, Interoperabilitas (SATUSEHAT & BPJS VClaim), dan Analitik Presisi.
- **Form Login Glassmorphism**:
  - Kartu kaca frosted (`backdrop-blur-2xl bg-slate-900/80 border border-white/10`) dengan aksen garis neon gradien hijau zamrud.
  - Input nama pengguna dengan ikon pendukung.
  - Input kata sandi dengan tombol interaktif toggle lihat/sembunyikan sandi (*show/hide password*).
  - Opsi *Ingatkan Saya* (remember me) terintegrasi.
  - Tombol aksi masuk bergradien dinamis dengan animasi status memuat (*loading state*).
- **Pusat Bantuan IT**: Dialog interaktif untuk panduan reset sandi dan kontak unit IT/SIMRS 24 jam (telepon/WhatsApp & email).
- **Optimasi Mobile View**:
  - Kolom deskripsi desktop disembunyikan di layar kecil (`hidden lg:flex`).
  - Kartu login langsung terpusat dan pas dalam 1 layar ponsel tanpa perlu scroll.
  - Header kartu di mobile menampilkan logo ringkas, nama aplikasi, dan nama rumah sakit.
  - Teks keterangan dibuat ringkas dan padat.

## Lokasi File Terkait
- **Livewire Component**: [Login.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Auth/Login.php)
- **Blade View**: [login.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/auth/login.blade.php)
- **Auth Layout**: [auth.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/layouts/auth.blade.php)
- **Route**: `route('login')` (`/login`)
