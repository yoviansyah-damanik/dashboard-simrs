# Rule: Filter Status Registrasi Periksa (reg_periksa)

Setiap modul atau fitur laporan yang mengambil sumber data dari tabel `reg_periksa`:

1. **Pengecualian Status Pelayanan Rawat Jalan**:
   - Jika status pelayanan adalah Rawat Jalan (`status_lanjut = 'Ralan'`), status registrasi periksa **TIDAK BOLEH** berstatus `'Batal'` atau `'Belum'`:
     ```sql
     rp.stts NOT IN ('Batal', 'Belum')
     ```
   
2. **Kueri Gabungan (Ralan & Ranap)**:
   - Jika kueri melibatkan data gabungan Rawat Jalan dan Rawat Inap, pasien Rawat Jalan wajib memfilter status 'Batal' dan 'Belum', sedangkan Rawat Inap tidak terpengaruh status 'Belum':
     ```php
     $query->where(function ($q) {
         $q->where('rp.status_lanjut', '!=', 'Ralan')
           ->orWhereNotIn('rp.stts', ['Batal', 'Belum']);
     });
     ```

3. **Kueri Khusus Rawat Jalan (Poliklinik / IGD)**:
   - Untuk kueri khusus poliklinik atau IGD (keduanya adalah `status_lanjut = 'Ralan'`), filter langsung diterapkan:
     ```php
     $query->whereNotIn('rp.stts', ['Batal', 'Belum']);
     ```
