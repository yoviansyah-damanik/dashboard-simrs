---
name: pdf-report-export
description: >-
  Enforces server-side PDF data generation for all module printing in the SIMRS dashboard. Use this skill whenever creating or updating application modules that require printing or report exporting, ensuring that reports generate structured DomPDF files from data instead of browser screen printing (window.print).
---

# PDF Data Report Export Guide

Panduan standar untuk fitur cetak laporan di dashboard SIMRS. Setiap modul yang menyediakan fungsi cetak **DILARANG MENGGUNAKAN CETAK LAYAR (window.print)** dan **WAJIB MENGGUNAKAN CETAK PDF DATA** berbasis server (`barryvdh/laravel-dompdf`).

---

## 1. Aturan Wajib Cetak Modul

1. **Dilarang Screen Printing**:
   - Jangan gunakan `onclick="window.print()"` atau CSS `@media print` untuk mencetak halaman aplikasi langsung dari browser.
   - Tombol cetak harus memanggil aksi Livewire atau route Controller untuk mengunduh berkas PDF data riil (misal: `wire:click="exportPdf"`).

2. **Gunakan DomPDF Facade**:
   - Gunakan `Barryvdh\DomPDF\Facade\Pdf` untuk merender data ke template Blade PDF khusus.
   - Format kertas standar: A4 (portrait atau landscape disesuaikan lebar tabel data).

3. **Struktur Berkas Laporan**:
   - Template view PDF diletakkan di `resources/views/reports/<nama-modul>-pdf.blade.php`.
   - Layout PDF harus menyertakan:
     - Kop Surat Rumah Sakit / Header identitas resmi
     - Judul laporan, periode/filter tanggal, waktu cetak, dan nama user pencetak
     - Tabel ringkasan dan rincian data tabular yang rapi
     - Footer nomor halaman dan blok tanda tangan / pengesahan penanggung jawab

---

## 2. Pola Implementasi pada Livewire Component

```php
namespace App\Livewire\Mutu;

use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class MyModule extends Component
{
    public int $selectedYear = 2026;
    public ?int $selectedMonth = null;

    /**
     * Ekspor laporan data ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        // 1. Ambil data riil dari repository
        $data = MyModuleRepository::getSummary($this->selectedYear, $this->selectedMonth);

        // 2. Render view PDF khusus
        $pdf = Pdf::loadView('reports.my-module-pdf', [
            'data' => $data,
            'year' => $this->selectedYear,
            'month' => $this->selectedMonth,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-mutu-' . $this->selectedYear . '-' . now()->format('YmdHis') . '.pdf';

        // 3. Stream download berkas PDF
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
```

---

## 3. Pola Implementasi pada Blade View UI

Ganti tombol `window.print()` dengan tombol aksi Livewire yang dilengkapi indikator loading:

```blade
<x-button color="default" icon="i-ph-file-pdf" wire:click="exportPdf" wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="exportPdf">Cetak PDF Data</span>
    <span wire:loading wire:target="exportPdf" class="flex items-center gap-1.5">
        <span class="icon-[solar--spinner-linear] animate-spin text-sm"></span>
        <span>Menyiapkan PDF...</span>
    </span>
</x-button>
```

---

## 4. Standar Styling Template Blade PDF

- Gunakan CSS murni inline atau tag `<style>` standar (DomPDF tidak mendukung Tailwind utility classes modern).
- Font standar: `Helvetica, Arial, sans-serif`.
- Tabel dengan `border-collapse: collapse; width: 100%; font-size: 11px;`.
- Atur page break dengan `page-break-inside: avoid;` pada baris tabel agar tidak terpotong canggung antar halaman.
