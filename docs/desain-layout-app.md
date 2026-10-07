# Penyesuaian Desain Header, Sidebar, dan Footer

## Ringkasan Perubahan
1. **Layout Utama ([app.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/layouts/app.blade.php))**:
   - Memperbarui palet latar belakang dengan efek **semi-semi cahaya (ambient glow)**:
     - Tiga bola cahaya berbaur halus (Emerald, Teal, Cyan) dengan efek `blur-[130px]` dan radial gradient vignette dari atas.
     - Mode Terang: `bg-slate-50` yang jernih dan segar dengan cahaya hijau zamrud lembut.
     - Mode Gelap: `bg-[#090d16]` / `slate-950` dengan aurora pendar hijau zamrud mewah.
   - Mengisolasi area gulir (*scroll container*) mandiri pada area konten utama di antara header dan footer.
   - Sinkronisasi judul halaman dengan `env('APP_NAME')` dan `config('app.hospital_name')`.

2. **Sidebar ([sidebar.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/components/sidebar.blade.php))**:
   - Menyelaraskan *branding* atas dengan logo berefek *glow*, lencana versi kecil (*version badge*) dengan titik indikator denyut (*pulse dot*) yang diposisikan rapi di atas nama aplikasi (`env('APP_NAME')`), serta nama rumah sakit di bawahnya.
   - Menghilangkan baris pemisah bawah berlebih agar header sidebar lebih ringkas dan hemat ruang vertikal.
   - Tipografi menu kategori lebih tegas dan rapi (`tracking-[0.2em]`).
   - Status aktif menu utama menggunakan gradien zamrud halus (`from-emerald-500/20 to-teal-500/10`) dengan *border* aksen.
   - Status aktif *child item* (submenu) didesain bersih tanpa border (`border-none`), berlatar hijau zamrud lembut (`bg-emerald-500/10`), teks kontras tinggi, dan titik indikator aktif.
   - Submenu terhubung dengan panduan garis vertikal rapi.
   - Tombol *hamburger floating* yang responsif untuk perangkat bergerak.

3. **Header ([header.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/components/header.blade.php))**:
   - Tampilan *glassmorphism* transparan berkabut (`backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80`).
   - Nama rumah sakit dihilangkan dari header agar tata letak sisi kiri lebih bersih dan minimalis.
   - Tombol **Menu** dan widget **Jam digital** didesain modern tanpa border (`border-none`) dengan latar hijau zamrud lembut (`bg-emerald-500/10 dark:bg-emerald-500/15 rounded-xl h-9`).
   - Widget jam dilengkapi kapsul ikon tersendiri (`w-6 h-6 rounded-lg bg-emerald-500/15`) serta tipografi waktu dan tanggal yang bersih dan proporsional.
   - Ditambahkan garis pembatas vertikal (*separator*) minimalis (`h-5 w-px`) di antara widget jam dan tombol akun pengguna.
   - Avatar profil bulat (*rounded-full ring-2 ring-emerald-500/40*) dengan *dropdown* mengambang modern.

4. **Footer ([footer.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/components/footer.blade.php))**:
   - Didesain **tetap di bawah (*sticky/docked*)** di dasar jendela layar dengan `sticky bottom-0 shrink-0 z-20`.
   - Menggunakan efek kaca transparan (`backdrop-blur-xl bg-white/85 dark:bg-slate-950/85`) dengan bayangan halus ke atas (*elevation shadow*).
   - Konten halaman bergulir di belakang footer tanpa saling bertumpuk atau terpotong.
   - Informasi aplikasi terpadu dengan titik pemisah aksen dan atribusi pengembang terjaga utuh.

5. **Harmonisasi Light & Dark Mode**:
   - **Dark Mode**: Latar `slate-950`, kartu/panel `slate-900/95`, teks `slate-100`/`white`, aksen `emerald-400`.
   - **Light Mode**: Latar `slate-100`, kartu/panel `white/95`, batas `slate-200`, teks `slate-800`/`slate-600`, aksen `emerald-600`/`emerald-700`.
   - Menghilangkan *hardcoded dark background* pada pembungkus layout utama agar transisi tema bekerja sempurna.

6. **Halaman Rekap Jadwal Operasi ([recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/operation-schedule/recap.blade.php))**:
   - Desain terpadu *glass toolbar* untuk tombol switcher (Tabel/Grafik), kolom pencarian, dan pilihan periode tanggal.
   - Ringkasan 4 kartu KPI metriks operasional (Total, Selesai, Proses, Menunggu).
   - Tabel data proporsional, status berwarna, bilah kemajuan (*progress bar*) gradien, dan baris total terstruktur.

7. **Halaman Pengaturan Akun ([account.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/account.blade.php))**:
   - Tata letak terbagi dua: Kartu identitas profil pengguna dan menu navigasi tab interaktif di sisi kiri.
   - Tab 1: **Informasi Akun** untuk memperbarui nama dan email dengan input modern dan username terkunci.
   - Tab 2: **Keamanan Kata Sandi** dilengkapi panduan kriteria sandi dan tombol sembunyikan/tampilkan sandi (*show/hide toggle*).
   - Tab 3: **Riwayat Login** menyajikan sorotan sesi terkini, tabel log terperinci (waktu, jenis perangkat, peramban, OS, dan alamat IP), serta paginasi dinamis.

8. **Halaman Login ([login.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/auth/login.blade.php))**:
   - Ditambahkan tombol pengalih tema (*Dark / Light Mode toggle*) melayang di sisi kanan atas (`fixed top-4 right-4 z-50`).
   - Menggunakan kapsul kaca transparan (`backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-2xl`) dengan ikon matahari/bulan dan teks label dinamis.
   - Terintegrasi penuh dengan `localStorage('darkMode')` dan tata warna menyeluruh di halaman login (latar, teks, kartu fitur, kartu form, input, dan modal bantuan).

9. **Halaman Rawat Jalan Rekap ([outpatient/recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/outpatient/recap.blade.php))**:
   - Toolbar terpadu: Switcher tampilan (*Tabel* & *Grafik*) dan filter periode waktu bergaya *glassmorphism*.
   - Banner ringkasan 3-panel dengan pendaran aksen (*ambient glow*): Total kunjungan, proporsi gender, dan rincian kelompok usia.
   - Grid 4 kartu KPI: Pasien baru, pasien lama, selesai periksa, dan belum periksa dengan kapsul ikon bertema.
   - Tabel responsif berlatar bersih dengan *badge* status, font nomor proporsional, baris total rata-rata, dan tampilan kosong ramah.
   - Mode grafik analitis interaktif untuk tren harian, 10 poliklinik teratas, gender (*doughnut*), dan penjamin/asuransi.

10. **Halaman Rawat Inap Rekap ([inpatient/recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/inpatient/recap.blade.php))**:
    - Navigasi tab terpadu: **Pasien Dirawat**, **Rekapitulasi**, dan **Snapshot Bed** dalam kontainer kapsul modern.
    - Tab 1 (*Pasien Dirawat*): Kartu sensus aktif, pencarian instan nama/RM, tabel sensus kamar/bangsal dengan *badge* lama rawat.
    - Tab 2 (*Rekapitulasi*): Banner gradien 3-panel (total pasien, gender, usia, dan hari perawatan/HP), 3 kartu volume pasien (masuk, keluar, dinas), 5 kartu indikator efisiensi RS (BOR, ALOS, BTO, TOI, GDR), serta tabel bangsal bertingkat dan 6 grafik visual.
    - Tab 3 (*Snapshot Bed*): Banner ketersediaan tempat tidur, kartu okupansi per kelas dengan bilah kemajuan (*progress bar*) halus, tampilan kartu per bangsal, dan modal detail okupansi.

11. **Halaman Error 4xx dan 5xx ([resources/views/errors/](file:///d:/WebApps/dashboard-simrs/resources/views/errors/))**:
    - Master Layout ([layout.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/layout.blade.php)): Wadah *glassmorphic* premium dengan efek pendaran cahaya (*ambient glow*), lencana status numerik besar, tombol pengalih *Dark/Light mode*, tombol kembali ke beranda/halaman sebelumnya, dan tautan bantuan IT SIMRS (WhatsApp & Email).
    - Halaman Error 4xx Spesifik & Fallback:
      - [403.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/403.blade.php): Akses Ditolak (Izin hak akses / peran).
      - [404.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/404.blade.php): Halaman Tidak Ditemukan (Rute tidak valid).
      - [419.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/419.blade.php): Sesi Kedaluwarsa (CSRF token dengan tombol Masuk Kembali).
      - [429.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/429.blade.php): Terlalu Banyak Permintaan (*Rate limit*).
      - [4xx.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/4xx.blade.php): Fallback untuk seluruh kode status HTTP 4xx lainnya (400, 405, 422, dsb).
    - Halaman Error 5xx Spesifik & Fallback:
      - [500.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/500.blade.php): Kesalahan Server Internal dengan tombol Coba Muat Ulang.
      - [503.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/503.blade.php): Layanan Dalam Pemeliharaan (*Maintenance mode*).
      - [5xx.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/errors/5xx.blade.php): Fallback untuk seluruh kode status HTTP 5xx lainnya (502, 504, dsb).

12. **Laporan Kunjungan dan Pengunjung ([patient-report/index.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/patient-report/index.blade.php))**:
    - Penambahan tabel rekapan bulanan saat mode **Periode Tahunan** aktif (`$period === 'yearly'`) untuk 12 bulan (Januari &ndash; Desember).
    - Pemisahan data IGD (`kd_poli = 'IGDK'`) dari Rawat Jalan Poli (`status_lanjut = 'Ralan' AND kd_poli != 'IGDK'`), serta penambahan kolom akumulasi keduanya (**Total Ralan**):
      - **Pengunjung**: Poli, IGD, Total Ralan (Poli + IGD), Total.
      - **Kunjungan**: Poli, IGD, Total Ralan (Poli + IGD), Rawat Inap, Total.
    - Penyesuaian pada KPI card (8 kartu metrik), tabel rekapan tahunan bulanan, tabel rekapan formal (TNI/POLRI/Umum), dan grafik tren (*line/bar* dengan pilihan metrik *Ralan / IGD / Ranap*).
    - Pada modul **Rawat Jalan Rekap** ([outpatient/recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/outpatient/recap.blade.php)), kunjungan IGD (`kd_poli = 'IGDK'`) dikecualikan secara konsisten agar data murni mencerminkan poliklinik rawat jalan.

13. **Monitoring Kamar & Tempat Tidur ([room/index.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/room/index.blade.php))**:
    - Rekonsep halaman monitoring kamar dengan tata letak dasbor yang bersih dan ramah perangkat mobile (*mobile-friendly*).
    - 4 KPI cards kapasitas tempat tidur rumah sakit: Total Kapasitas, Bed Tersedia, Bed Terisi, dan Tingkat Okupansi (BOR).
    - Kartu kategori kelas kamar responsif (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`) dengan visual bilah kemajuan (*progress bar*) okupansi.
    - Tabel rekapitulasi komparasi okupansi antar kelas kamar di halaman utama.
    - **Modal Detail Kamar**: Saat pengguna memilih kartu kelas kamar, detail setiap bed langsung ditampilkan dalam dialog modal interaktif:
      - Header modal dengan indikator *Live status* dan ringkasan angka.
      - Toolbar pencarian langsung (*live search*) nomor bed/nama bangsal dan tombol filter cepat (*Semua, Tersedia, Terisi*).
      - Kisi kartu bed responsif (2 kolom di mobile, 3–6 kolom di desktop) menampilkan status, nomor kamar, nama bangsal, dan tarif.
      - Dukungan navigasi tombol tutup dan penutupan dengan tombol Escape atau klik di luar modal.
    - **Sistem Ikon Kategori Kelas Kamar** ([StatusHelper::getRoomClassMeta](file:///d:/WebApps/dashboard-simrs/app/Helpers/StatusHelper.php)):
      - VIP / VVIP: `icon-[solar--crown-star-bold-duotone]` (amber).
      - Kelas 1: `icon-[ph--number-circle-one-duotone]` (sky).
      - Kelas 2: `icon-[ph--number-circle-two-duotone]` (teal).
      - Kelas 3: `icon-[ph--number-circle-three-duotone]` (emerald).
      - ICU / Intensif: `icon-[solar--heart-pulse-bold-duotone]` (rose).
      - HCU: `icon-[solar--pulse-2-bold-duotone]` (orange).
      - Ruang Isolasi: `icon-[solar--shield-cross-bold-duotone]` (violet).
      - Transit IGD / Non-Kelas: `icon-[solar--siren-bold-duotone]` (cyan).
      - Sinkronisasi visual otomatis pada kartu kelas, tabel komparasi, dan header dialog modal.

