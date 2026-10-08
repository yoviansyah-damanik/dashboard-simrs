# Rekapitulasi Pasien Dinas (TNI / POLRI) SIMRS

## 1. Deskripsi Fitur
Modul agregasi dan pelaporan data pasien dinas (TNI dan POLRI) yang diintegrasikan pada 3 area pelaporan utama SIMRS:
1. **Ringkasan Pelayanan Medis** (`/medical-services/summary`): Menyajikan metrik dinas lintas instalasi (Poli, IGD, Ranap) beserta tab analisis dinas.
2. **Laporan Pasien Rawat Jalan** (`/outpatient/report`): Tab "Rekap Pasien Dinas" dengan sebaran poliklinik tujuan, gender, demografi status kunjungan, dan kategori personel.
3. **Laporan Pasien Rawat Inap** (`/inpatient/report`): Tab "Rekap Pasien Dinas" dengan sebaran bangsal/ruangan rawat, kategori personel, dan top satuan TNI.

---

## 2. Kriteria & Sumber Data Dinas
Data pasien dinas diperoleh melalui identifikasi relasi nomor rekam medis (`no_rkm_medis`) ke tabel instansi dinas di SIMRS:
- **Pasien TNI**: `pasien_tni (pt)`
- **Pasien POLRI**: `pasien_polri (pp)`
- **Pengelompokan Penjamin**: Filter `DINAS` mencakup pasien terdaftar di `pt`, `pp`, atau nama penjamin mengandung kata `'DINAS'`.

### Klasifikasi Kategori Personel
- **Militer / Anggota Aktif**: `golongan_tni IN (1, 2, 3)` atau `golongan_polri = 1`
- **ASN / PNS**: `golongan_tni IN (8, 9, 10)` atau `golongan_polri = 2`
- **Keluarga Personel**: `golongan_tni IN (5, 6, 7)` atau `golongan_polri = 3`
- **Purnawirawan**: `golongan_tni IN (4, 11, 12)` atau `golongan_polri = 4`

---

## 3. Aturan Pemisahan Eksplisit TNI & POLRI
Di setiap antarmuka yang menyajikan data pasien dinas, wajib memisahkan secara eksplisit antara entitas **TNI** dan **POLRI**:
- **Kartu Metrik & Summary KPI**: Menyajikan angka terpisah untuk pasien TNI dan pasien POLRI di samping total dinas.
- **Tabel Rekapitulasi (Poli, Bangsal, Overview)**: Menyediakan kolom/baris tersendiri untuk TNI dan POLRI.
- **Daftar Pasien**: Menyematkan badge penjamin khusus (`TNI` hijau dan `POLRI` biru).
- **Ekspor Dokumen**: Kolom CSV, Excel, dan PDF memuat kolom status dinas terpisah (`TNI` / `POLRI`).

---

## 4. Fitur Ekspor Dokumen
- **PDF Data (DomPDF)**: Menggunakan engine server-side `Barryvdh\DomPDF\Facade\Pdf` dengan format formal landscape A4 dan header resmi rumah sakit (`reports.partials.header`). Dilarang screen printing.
- **Excel (.xlsx)**: Ekspor multi-sheet PhpSpreadsheet dengan sheet terpisah khusus rekapitulasi pasien dinas per unit dan kategori.
