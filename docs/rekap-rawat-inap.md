# Rekapitulasi Rawat Inap

## Deskripsi
Fitur ini menyediakan rekapitulasi data pasien rawat inap berdasarkan periode tanggal tertentu. Data dikelompokkan per **Bangsal** dan **Kelas**.

## Fitur Utama
- **Filter Tanggal**: Memungkinkan pengguna memilih rentang waktu rekap.
- **Statistik Overview**: Menampilkan total pasien, bed terisi (saat ini), bed rusak/tidak tersedia, dan pasien meninggal.
- **Statistik Per Kelas**: Kartu ringkasan yang menampilkan jumlah pasien, kapasitas bed, dan persentase hunian untuk setiap kelas perawatan.
- **Tabel Rekapitulasi**:
    - **Bangsal & Kelas**: Lokasi dan kelas perawatan.
    - **TT (Tempat Tidur)**: Kapasitas tempat tidur yang tersedia.
    - **Terisi**: Jumlah bed yang sedang terisi saat ini.
    - **Rusak**: Jumlah bed yang sedang tidak tersedia (rusak/maintenance).
    - **Total Pasien**: Jumlah total pasien dalam periode tersebut.
    - **Rawat**: Pasien yang status pulangnya masih `-`.
    - **Pulang**: Pasien yang sudah keluar (semua status kecuali `-` dan `Pindah Kamar`).
    - **Rujuk**: Pasien dengan status pulang `Rujuk`.
    - **APS**: Pasien yang pulang Atas Permintaan Sendiri.
    - **Mati**: Pasien dengan status pulang `Meninggal`.
    - **HP (Hari Perawatan)**: Total hari rawat seluruh pasien dalam periode tersebut.
    - **ALOS (Average Length of Stay)**: Rata-rata lama hari rawat pasien yang sudah pulang.
    - **BOR (Bed Occupancy Rate)**: Persentase penggunaan tempat tidur.

## Lokasi File
- **Livewire Component**: [Recap.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Inpatient/Recap.php)
- **Blade View**: [recap.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/inpatient/recap.blade.php)
- **Route**: `route('inpatient.recap')` (/ranap/rekap)
