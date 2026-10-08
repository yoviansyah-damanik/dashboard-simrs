# Rule: Pemisahan Data Rawat Jalan (Poli & Gawat Darurat)

Dalam seluruh modul pelaporan dan visualisasi data Dashboard SIMRS:

1. **Pemisahan Kategori Ralan**:
   - Setiap kali menyajikan atau mengagregasikan data **Rawat Jalan (Ralan)**, data wajib dipecah menjadi 2 komponen:
     - **Poliklinik (Poli)**: Pasien ralan pada poliklinik rawat jalan umum/spesialis (`status_lanjut = 'Ralan'` AND `kd_poli != 'IGDK'`).
     - **Gawat Darurat (IGD)**: Pasien kegawatdaruratan (`kd_poli = 'IGDK'`).

2. **Wajib Menampilkan Kedua Data**:
   - Tidak diperkenankan hanya menampilkan angka total Ralan gabungan tanpa rincian.
   - Pada kartu KPI, tabel data, modal drilldown, dan template laporan cetak PDF, selalu sediakan informasi eksplisit untuk:
     - Jumlah/kasus **Poliklinik**
     - Jumlah/kasus **Gawat Darurat (IGD)**
     - Serta total akumulasi keduanya jika relevan.
