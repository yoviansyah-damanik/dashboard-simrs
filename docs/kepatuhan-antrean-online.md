# Dokumentasi Modul Konsistensi & Kepatuhan Antrean Online

## 1. Ikhtisar Modul

Modul evaluasi tingkat konsistensi dan kepatuhan pemanfaatan antrean online bagi pelayanan rawat jalan poliklinik (Mobile JKN vs Onsite Loket & APM), terintegrasi langsung dengan API BPJS Antrean Terapi (`/api/v1/bpjs/antrean/report`).

- **Grup Menu**: Mutu & Akreditasi
- **Route**: `/mutu/kepatuhan-antrean` (alias: `/kepatuhan-antrean-online`)
- **Controller/Livewire**:
    - Parent: `App\Livewire\OnlineQueue\Compliance`
    - Child Tabs (Lazy Loaded):
        - `App\Livewire\OnlineQueue\Tabs\OverviewTab` (Grafik & Tren Kepatuhan)
        - `App\Livewire\OnlineQueue\Tabs\AnnualReportTab` (Laporan Tahunan & Growth MoM)
        - `App\Livewire\OnlineQueue\Tabs\PolyclinicDoctorTab` (Breakdown Poliklinik & Dokter)
        - `App\Livewire\OnlineQueue\Tabs\ConformityTab` (Audit Kesesuaian JKN vs SEP/SKDP)
- **Repository**: `App\Repository\OnlineQueueComplianceRepository`
- **API Client**: `App\Services\Bpjs\TerapiAntreanApiClient` (mengonsumsi API Terapi)
- **Views**:
    - Web: `resources/views/pages/online-queue/compliance.blade.php`
    - PDF: `resources/views/reports/online-queue-compliance-pdf.blade.php`

## 2. Aturan Data & Kriteria

- **Sumber Data Terintegrasi**: API BPJS Antrean Terapi (`/api/v1/bpjs/antrean/report/*`):
    - `summary`: Ringkasan bulanan (total antrean, kanal, kesesuaian, pemanfaatan SEP, waktu tunggu).
    - `daily`: Rekapitulasi harian antrean per tanggal.
    - `polyclinic` & `doctor`: Rekapitulasi per unit poli dan dokter.
    - `poly-doctor`: Rincian kombinasi poliklinik dan dokter.
    - `mismatches`: Audit detail ketidaksesuaian antrean terhadap SEP / SKDP / rujukan.
- **Klasifikasi Kanal Booking BPJS**:
    - `B`: Antrean Online Mobile JKN (misal Oktober 2026: 1.275 antrean / 69,4%)
    - `A`: Onsite Loket Admisi (misal Oktober 2026: 490 antrean / 26,7%)
    - `D`: Onsite APM Anjungan Mandiri (misal Oktober 2026: 71 antrean / 3,9%)
    - Total Antrean: 1.836 antrean
    - Pemanfaatan SEP Berantrean: 99,2%
- **Autentikasi API**: Bearer token via endpoint `/api/v1/auth/token` dengan scope `bpjs` dan caching token otomatis.

## 3. Ambang Batas (Threshold) & Kepatuhan

- **Threshold Default**: 60.0% (target baku BPJS Kesehatan).
- **Interaktif**: Dapat diubah secara live (slider dan input) pada dashboard.
- **Formula Kepatuhan JKN**:
  $$\text{Kepatuhan JKN (\%)} = \frac{\text{Total JKN (B)}}{\text{Total Antrean Rawat Jalan}} \times 100\%$$
- **Status Evaluasi**:
    - **Patuh / Tercapai**: Capaian JKN $\ge$ Threshold (contoh: 69,4% $\ge$ 60% = Tercapai).
    - **Belum Tercapai**: Capaian JKN < Threshold.

4. **Analisis Growth MoM & Audit Kesesuaian**:
    - **Laporan Tahunan (MoM Rumah Sakit)**: Matriks 12 bulan (Januari–Desember) memuat volume kanal dan pertumbuhan MoM (%) Total Ralan dan JKN (hanya muncul saat memilih Semua Bulan / Laporan Tahunan).
    - **2. Matriks 12 Bulan & Pertumbuhan MoM Kumulatif per Poliklinik & Dokter DPJP**: Rekapitulasi kumulatif tahunan per unit poliklinik dan dokter DPJP, dilengkapi fitur drilldown tren 12 bulan (MoM Total %, MoM JKN %, dan $\Delta$ Poin Kepatuhan) untuk setiap dokter.
    - **Audit Kesesuaian JKN vs SEP / SKDP (Mismatches)**: Validasi nomor referensi antrean terhadap SKDP (pasien kontrol) atau rujukan (pasien rujukan baru), mendeteksi anomali antrean batal saat SEP terbit.
    - **Ekspor PDF Formal**: Menghasilkan berkas PDF data berbasis server (DomPDF) berorientasi landscape lengkap dengan tabel MoM Poli & Dokter serta ringkasan eksekutif.
