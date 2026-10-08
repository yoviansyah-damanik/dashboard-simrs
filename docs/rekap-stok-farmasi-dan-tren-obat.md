# Rekap Stok Farmasi & Tren 10 Obat Paling Sering Digunakan

## 1. Ringkasan Fitur
- **Halaman Rekap Stok Obat Farmasi (`/farmasi/stok`)**:
  - Menyajikan inventaris obat SIMRS (`databarang`, `gudangbarang`, `kategori_barang`).
  - KPI: Total Item Aktif, Fisik Unit, Nilai Valuasi Aset, Stok Kosong, Stok Akan Habis, Stok Aman, Kadaluarsa / Mendekati Expired.
  - Filter: Depo/Gudang Bangsal, Kategori Barang, Status Stok (`Semua`, `Akan Habis`, `Stok Kosong`, `Stok Aman`, `Mendekati Expired`, `Expired`).
  - Pencarian Nama/Kode Obat & Pengurutan (Sort).
  - Modal rincian stok per depo gudang & nomor batch/faktur hanya dapat dibuka dan menampilkan data obat berstatus aktif (`db.status = '1'`).
  - Filter Obat Aktif: Seluruh kueri inventaris, rincian detail barang, depo gudang, dan kategori dibatasi secara ketat hanya untuk obat aktif (`databarang.status = '1'`).
- **Penanda Obat Akan Habis**:
  - Obat ditandai **Akan Habis** jika stok fisik `> 0` dan `stok <= stokminimal`.
  - Visualisasi baris disorot warna kuning/amber dengan badge status berkedip dan rasio progres terhadap stok minimal.
  - Obat dengan stok `<= 0` ditandai **Stok Kosong** dengan sorotan merah.
- **Tren 10 Obat Paling Sering Digunakan di Rekap Farmasi (`/farmasi/rekap`)**:
  - Multi-line chart Chart.js (`chartPharmTop10Trend`) dinamis 10 warna kontras untuk melacak tren kuantitas pemakaian 10 obat teratas (obat aktif `db.status = '1'`).
  - Tabel peringkat 10 obat dilengkapi jumlah terpakai, frekuensi resep pasien, sisa stok SIMRS, penanda status stok (*Akan Habis / Habis / Aman*), serta tombol shortcut ke rincian stok farmasi.
  - Integrasi pada tampilan "Grafik" maupun "Dalam Angka".

## 2. Struktur Komponen & File
- **Repository**: [`PharmacyStockRepository`](file:///d:/WebApps/dashboard-simrs/app/Repository/PharmacyStockRepository.php)
- **Livewire**:
  - [`App\Livewire\Pharmacy\Stock`](file:///d:/WebApps/dashboard-simrs/app/Livewire/Pharmacy/Stock.php)
  - [`App\Livewire\Pharmacy\Recap`](file:///d:/WebApps/dashboard-simrs/app/Livewire/Pharmacy/Recap.php)
- **Views**:
  - [`resources/views/pages/pharmacy/stock.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/pages/pharmacy/stock.blade.php)
  - [`resources/views/pages/pharmacy/recap.blade.php`](file:///d:/WebApps/dashboard-simrs/resources/views/pages/pharmacy/recap.blade.php)
- **Menu & Rute**:
  - Sidebar: *Layanan Penunjang Medis* -> *Farmasi* -> *Rekap Stok Obat* (`route('pharmacy.stock')`).
- **Pengujian**:
  - [`PharmacyStockTest`](file:///d:/WebApps/dashboard-simrs/tests/Feature/PharmacyStockTest.php) (7 pengujian lulus).
