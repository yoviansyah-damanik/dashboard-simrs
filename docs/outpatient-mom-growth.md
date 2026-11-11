# Modul MoM Growth Pasien Rawat Jalan

Dokumentasi modul analisis pertumbuhan Month-over-Month (MoM & DoD Growth) pasien rawat jalan pada SIMRS Dashboard.

---

## 1. Fitur Utama
- **Granularitas Adaptif Berbasis Periode**:
  - **Periode Bulanan (`period = 'monthly'`)**: Analisis **harian** (tanggal 1 s.d. 28/29/30/31). Menghitung laju pertumbuhan harian Day-over-Day (`DoD % Growth`) dan komparasi tanggal yang sama di bulan sebelumnya (`MoM Date % Growth`).
  - **Periode Tahunan (`period = 'yearly'`)**: Analisis **bulanan** (Januari s.d. Desember). Menghitung laju pertumbuhan bulanan Month-over-Month (`MoM % Growth`) dan proporsi kontribusi (`Share %`).
- **Pemisahan Standar Layanan Ralan**:
  - **Poliklinik (Poli)**: `status_lanjut = 'Ralan'` dan `kd_poli != 'IGDK'`, `stts NOT IN ('Batal', 'Belum')`.
  - **Gawat Darurat (IGD)**: `kd_poli = 'IGDK'`, `stts NOT IN ('Batal', 'Belum')`.
  - Disajikan berdampingan secara eksplisit pada kartu KPI, grafik tren, tabel rekap, matriks unit, dan laporan PDF.
- **Sub-View Analisis**:
  1. *Ringkasan Tren RS*: Timeline harian/bulanan dengan rincian Poli, IGD, Pasien Baru/Lama, Gender L/P, Penjamin BPJS/Umum/Dinas, dan % Growth.
  2. *Matriks Poliklinik & IGD*: Pivot matriks per unit dengan nilai kasus dan laju pertumbuhan naik/turun di tiap kolom tanggal/bulan.
  3. *Matriks Dokter DPJP*: Pivot matriks per dokter pemeriksa dengan nilai pasien dan laju pertumbuhan.
- **Pengalih Mode Tampilan Sel**: Pilihan tampilan *Pasien & %*, *Hanya %*, atau *Hanya Pasien*.
- **Ekspor Dokumen**:
  - **PDF Data Server-side (`outpatient-mom-pdf.blade.php`)**: Berbasis DomPDF format landscape A4 dengan kop resmi rumah sakit.
  - **Workbook Excel Multi-sheet**: Menghasilkan berkas `.xlsx` dengan 3 lembar kerja (*Tren MoM RS*, *Matriks Poliklinik*, *Matriks Dokter DPJP*).

---

## 2. Struktur File
- **Repository**: [app/Repository/OutpatientReportRepository.php](file:///d:/WebApps/dashboard-simrs/app/Repository/OutpatientReportRepository.php) (`getOutpatientMomGrowth`)
- **Livewire**: [app/Livewire/Outpatient/Report.php](file:///d:/WebApps/dashboard-simrs/app/Livewire/Outpatient/Report.php) (Tab `mom_growth`, method `exportMomPdf`, `exportMomExcel`)
- **View**: [resources/views/pages/outpatient/report.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/pages/outpatient/report.blade.php)
- **Template PDF**: [resources/views/reports/outpatient-mom-pdf.blade.php](file:///d:/WebApps/dashboard-simrs/resources/views/reports/outpatient-mom-pdf.blade.php)
- **Pengujian**: [tests/Feature/OutpatientMomGrowthTest.php](file:///d:/WebApps/dashboard-simrs/tests/Feature/OutpatientMomGrowthTest.php)

