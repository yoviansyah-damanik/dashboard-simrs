# Rule: Standar Cetak Data PDF (Dilarang Screen Print)

Setiap membuat atau memodifikasi modul aplikasi di Dashboard SIMRS:

1. **Dilarang Menggunakan Screen Print**:
   - Jangan gunakan `window.print()` atau CSS print screen langsung dari browser.
2. **Wajib Cetak PDF Data**:
   - Fitur cetak harus menghasilkan berkas dokumen PDF riil berbasis server menggunakan package `Barryvdh\DomPDF\Facade\Pdf`.
   - Data diambil langsung dari repository operasional, kemudian di-stream ke pengguna menggunakan `response()->streamDownload(...)`.
   - Template PDF dibuat khusus di folder `resources/views/reports/` dengan tata letak laporan resmi (kop surat/header, tabel tabular bergaris, metadata periode cetak, dan tanda tangan).
