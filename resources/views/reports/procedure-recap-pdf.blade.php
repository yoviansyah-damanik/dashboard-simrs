<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Data Tindakan & Prosedur Medis (ICD-9-CM)</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .filter-meta {
            margin-bottom: 12px;
            padding: 8px 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .filter-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-meta td {
            padding: 2px 4px;
            font-size: 7.5pt;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .filter-value {
            color: #0f172a;
            width: 32%;
        }
        .summary-boxes {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-box {
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
        }
        .summary-box .num {
            font-size: 10pt;
            font-weight: bold;
            color: #047857;
        }
        .summary-box .lbl {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #94a3b8;
            padding: 3.5px 5px;
            font-size: 7.5pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7pt;
        }
        table.data-table tr.subheader th {
            background-color: #334155;
            font-size: 6.5pt;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; font-size: 7.5pt; }
        .footer-note {
            margin-top: 15px;
            font-size: 7pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'title' => 'Laporan Rekapitulasi Data Tindakan & Prosedur Medis (ICD-9-CM)',
        'subtitle' => 'Distribusi Layanan Rawat Jalan (Poli & IGD) dan Rawat Inap Berdasarkan Rekam Medis',
    ])

    <!-- Metadata Parameter Filter Laporan -->
    <div class="filter-meta">
        <table>
            <tr>
                <td class="filter-label">Periode Waktu:</td>
                <td class="filter-value">{{ $filterInfo['period'] ?? '-' }}</td>
                <td class="filter-label">Instalasi / Layanan:</td>
                <td class="filter-value">{{ $filterInfo['service_status'] ?? 'Semua Layanan' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Kategori Tindakan:</td>
                <td class="filter-value">{{ $filterInfo['category'] ?? 'Semua Bab ICD-9' }}</td>
                <td class="filter-label">Prioritas Prosedur:</td>
                <td class="filter-value">{{ $filterInfo['priority'] ?? 'Semua Prioritas' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Jenis Kelamin:</td>
                <td class="filter-value">{{ $filterInfo['gender'] ?? 'Semua Gender' }}</td>
                <td class="filter-label">Tanggal Cetak:</td>
                <td class="filter-value">{{ date('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Eksekutif (KPI Cards) -->
    <table class="summary-boxes">
        <tr>
            <td class="summary-box" style="width: 14%;">
                <div class="num">{{ number_format($summary['total_tindakan'] ?? 0) }}</div>
                <div class="lbl">Total Tindakan</div>
            </td>
            <td class="summary-box" style="width: 14%;">
                <div class="num" style="color: #0284c7;">{{ number_format($summary['total_prosedur_unik'] ?? 0) }}</div>
                <div class="lbl">Prosedur Unik</div>
            </td>
            <td class="summary-box" style="width: 14%;">
                <div class="num" style="color: #6366f1;">{{ number_format($summary['total_pasien_unik'] ?? 0) }}</div>
                <div class="lbl">Pasien Unik</div>
            </td>
            <td class="summary-box" style="width: 14%;">
                <div class="num" style="color: #10b981;">{{ number_format($summary['poli'] ?? 0) }}</div>
                <div class="lbl">Poli (Ralan)</div>
            </td>
            <td class="summary-box" style="width: 14%;">
                <div class="num" style="color: #f59e0b;">{{ number_format($summary['igd'] ?? 0) }}</div>
                <div class="lbl">Gawat Darurat</div>
            </td>
            <td class="summary-box" style="width: 15%;">
                <div class="num" style="color: #8b5cf6;">{{ number_format($summary['ranap'] ?? 0) }}</div>
                <div class="lbl">Rawat Inap</div>
            </td>
            <td class="summary-box" style="width: 15%;">
                <div class="num" style="color: #3b82f6;">{{ number_format($summary['utama'] ?? 0) }} <span style="font-size: 8pt; color: #64748b;">/ {{ number_format($summary['sekunder'] ?? 0) }}</span></div>
                <div class="lbl">Utama / Sekunder</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Tindakan ICD-9-CM -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 50px;">Kode</th>
                <th rowspan="2">Deskripsi Prosedur Medis (ICD-9-CM)</th>
                <th colspan="2">Gender</th>
                <th colspan="3">Instalasi / Layanan</th>
                <th colspan="2">Prioritas</th>
                <th rowspan="2" style="width: 45px;">Total</th>
                <th rowspan="2" style="width: 35px;">%</th>
            </tr>
            <tr class="subheader">
                <th style="width: 28px;">L</th>
                <th style="width: 28px;">P</th>
                <th style="width: 34px;">Poli</th>
                <th style="width: 34px;">IGD</th>
                <th style="width: 36px;">Ranap</th>
                <th style="width: 36px;">Utama</th>
                <th style="width: 38px;">Sekunder</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($procedures as $idx => $p)
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono font-bold">{{ $p['kode'] }}</td>
                    <td>
                        <span class="font-bold">{{ $p['deskripsi'] }}</span>
                        @if (!empty($p['deskripsi_pendek']) && $p['deskripsi_pendek'] !== $p['deskripsi'])
                            <br><small style="color: #64748b;">{{ $p['deskripsi_pendek'] }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($p['total_pria']) }}</td>
                    <td class="text-center">{{ number_format($p['total_wanita']) }}</td>
                    <td class="text-center">{{ number_format($p['total_poli']) }}</td>
                    <td class="text-center">{{ number_format($p['total_igd']) }}</td>
                    <td class="text-center">{{ number_format($p['total_ranap']) }}</td>
                    <td class="text-center">{{ number_format($p['total_utama']) }}</td>
                    <td class="text-center">{{ number_format($p['total_sekunder']) }}</td>
                    <td class="text-right font-bold">{{ number_format($p['total_tindakan']) }}</td>
                    <td class="text-right">{{ $p['persentase'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data tindakan pada periode dan kriteria filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
        @if (!empty($procedures))
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="3" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_pria')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_wanita')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_poli')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_igd')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_ranap')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_utama')) }}</td>
                    <td class="text-center">{{ number_format(collect($procedures)->sum('total_sekunder')) }}</td>
                    <td class="text-right">{{ number_format($total_all) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer-note">
        * Sesuai ketentuan pelaporan SIMRS, data Rawat Jalan dipisahkan secara eksplisit antara Poliklinik (Poli) dan Gawat Darurat (IGD). Prosedur utama menunjukkan tindakan primer pasien.
    </div>

    <!-- Tanda Tangan / Pengesahan Dokumen Resmi -->
    <table style="width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center; font-size: 8pt;">
                <div>{{ $profile->kota ?? 'Rumah Sakit' }}, {{ date('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 4px;">Kepala Instalasi Rekam Medis</div>
                <div style="margin-top: 55px; font-weight: bold; text-decoration: underline;">
                    ( .................................................... )
                </div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">NIP / NRK: .......................................</div>
            </td>
        </tr>
    </table>
</body>
</html>
