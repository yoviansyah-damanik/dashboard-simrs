# Project Guidelines & Rules (Dashboard SIMRS)

## 1. Aturan Global Pengembangan
- Rangkum dokumentasi kode sesingkat mungkin di folder `docs/`.
- Gunakan bahasa Indonesia hanya untuk komentar dan dokumentasi; penamaan file, class, method, variabel menggunakan bahasa Inggris.
- Eksekusi pengecekan/testing langsung tanpa meminta persetujuan.
- Setiap modal wajib menggunakan class modifier `!mt-0` agar posisi mentok ke atas.

## 2. Aturan Cetak Laporan (Wajib Cetak PDF Data)
- **Dilarang keras screen print**: Jangan gunakan `onclick="window.print()"` atau cetak layar browser.
- **Wajib buatkan cetak PDF data**: Setiap modul baru/terbaru yang memiliki fitur cetak wajib menyediakan method ekspor PDF berbasis server menggunakan `Barryvdh\DomPDF\Facade\Pdf::loadView(...)`.
- Template PDF diletakkan di `resources/views/reports/` dengan format tabel formal dan styling CSS inline yang kompatibel dengan DomPDF.
- Skill terkait: `.agents/skills/pdf-report-export/SKILL.md`.

## 3. Aturan Penyajian Data Rawat Jalan (Pemisahan Ralan: Poli & IGD)
- **Pemisahan Data Ralan**: Setiap menampilkan data Rawat Jalan (Ralan), data wajib dibagi menjadi 2 entitas:
  1. **Poliklinik (Poli)**: Pelayanan rawat jalan poliklinik (`status_lanjut = 'Ralan'` dan `kd_poli != 'IGDK'`).
  2. **Gawat Darurat (IGD)**: Pelayanan kegawatdaruratan (`kd_poli = 'IGDK'`).
- **Wajib Tampilkan Keduanya**: Pada kartu KPI metrik, tabel rekapitulasi, visualisasi, maupun laporan PDF, selalu sajikan rincian kedua data tersebut (**Poli** dan **Gawat Darurat / IGD**) secara eksplisit di samping total rawat jalan atau rawat inap.

## 4. Aturan Status Registrasi Periksa (Pengecualian 'Batal' & 'Belum' pada Ralan)
- **Sumber Data `reg_periksa`**: Ketika menampilkan atau mengagregasi laporan yang bersumber dari tabel `reg_periksa`, jika status pelayanan adalah Rawat Jalan (`status_lanjut = 'Ralan'`), wajib memastikan status periksa **bukan** 'Batal' dan **bukan** 'Belum':
  `stts NOT IN ('Batal', 'Belum')`
  - Jika kueri mencakup Ralan & Ranap sekaligus:
    `WHERE (reg_periksa.status_lanjut != 'Ralan' OR reg_periksa.stts NOT IN ('Batal', 'Belum'))`
  - Jika kueri khusus Rawat Jalan (Poliklinik maupun IGD):
    `WHERE reg_periksa.stts NOT IN ('Batal', 'Belum')`

## 5. Aturan Pencatatan Version Log (Changelog)
- **Wajib Catat Penambahan Fitur**: Setiap kali ada penambahan atau pembaruan fitur baru pada aplikasi, wajib mencatat ringkasan perubahannya ke dalam berkas `version.json` pada entri `changeLog` versi aktif (serta menaikkan nomor versi semantik jika diperlukan).
- Hal ini memastikan riwayat perubahan sistem selalu mutakhir, terdokumentasi, dan dapat langsung dilihat pada halaman `/changelog` serta badge versi aplikasi.
