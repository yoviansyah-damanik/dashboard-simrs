# Perhitungan Kunjungan & Pengunjung Pasien

## 1. Definisi & Kriteria Data
- **Rawat Jalan (Poli)**: `status_lanjut = 'Ralan' AND kd_poli != 'IGDK'`
- **IGD (Ralan)**: `status_lanjut = 'Ralan' AND kd_poli = 'IGDK'`
- **Total Rawat Jalan (Poli + IGD)**: `status_lanjut = 'Ralan'`
- **Rawat Inap**: `status_lanjut = 'Ranap'`
- **Total Kunjungan**: `Total Rawat Jalan + Rawat Inap`

## 2. Perhitungan Pengunjung
- **Pengunjung Harian/Bulanan**: Dihitung berdasarkan `COUNT(DISTINCT no_rkm_medis)` per rentang waktu yang sesuai.
- **Pengunjung Total Periode**: Menghitung *distinct* pasien unik sepanjang rentang tanggal penuh (bukan penjumlahan kumulatif hari per hari untuk menghindari hitung ganda pasien berulang).

## 3. Kontinuitas Kalender Tren
- Tanggal/bulan yang tidak memiliki registrasi otomatis diisi nilai `0` sehingga grafik tren harian membentuk rentang tanggal utuh tanpa lompatan tanggal.
