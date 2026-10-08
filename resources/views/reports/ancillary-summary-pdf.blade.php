<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ringkasan Layanan Penunjang Medis RS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #0284c7; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        .summary-box { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background-color: #f8fafc; }
        .summary-box .val { font-size: 14px; font-weight: bold; color: #0f172a; }
        .summary-box .lbl { font-size: 8.5px; color: #64748b; text-transform: uppercase; }
        .section-title { background-color: #e0f2fe; color: #0369a1; font-weight: bold; padding: 4px 8px; font-size: 10px; margin-top: 10px; border: 1px solid #bae6fd; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; font-size: 8.5px; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 25px; width: 100%; border-collapse: collapse; }
        .footer td { border: none; font-size: 9px; }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">RINGKASAN EKSEKUTIF LAYANAN PENUNJANG</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Farmasi, Laboratorium, Radiologi, dan Gizi</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Periode</strong></td>
            <td style="width: 35%;">: {{ $startDate }} s/d {{ $endDate }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Sumber Data</strong></td>
            <td>: Database SIMRS Terpadu</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ number_format($summary['total_pelayanan'] ?? 0) }}</div>
                <div class="lbl">Total Layanan Penunjang</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ number_format($summary['total_farmasi'] ?? 0) }}</div>
                <div class="lbl">Resep Farmasi</div>
            </td>
            <td>
                <div class="val" style="color: #0284c7;">{{ number_format($summary['total_lab'] ?? 0) }}</div>
                <div class="lbl">Pemeriksaan Lab</div>
            </td>
            <td>
                <div class="val" style="color: #7c3aed;">{{ number_format($summary['total_rad'] ?? 0) }}</div>
                <div class="lbl">Radiologi</div>
            </td>
            <td>
                <div class="val" style="color: #ea580c;">{{ number_format($summary['total_gizi'] ?? 0) }}</div>
                <div class="lbl">Pemberian Diet Gizi</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. RINGKASAN PRODUKTIVITAS PER INSTALASI PENUNJANG</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Instalasi Penunjang</th>
                <th style="width: 25%; text-align: right;">Volume Pelayanan</th>
                <th style="width: 20%; text-align: right;">Proporsi (%)</th>
                <th style="width: 25%;">Keterangan Metrik</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totAll = $summary['total_pelayanan'] ?? 0;
            @endphp
            <tr>
                <td><strong>Instalasi Farmasi</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($farmasi['total'] ?? 0) }} R/</td>
                <td style="text-align: right;">{{ $totAll > 0 ? round((($farmasi['total'] ?? 0) / $totAll) * 100, 1) : 0 }}%</td>
                <td>{{ number_format($farmasi['diserahkan'] ?? 0) }} obat diserahkan</td>
            </tr>
            <tr>
                <td><strong>Laboratorium Patologi</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($laboratorium['total'] ?? 0) }} Sampel</td>
                <td style="text-align: right;">{{ $totAll > 0 ? round((($laboratorium['total'] ?? 0) / $totAll) * 100, 1) : 0 }}%</td>
                <td>{{ number_format($laboratorium['pasien'] ?? 0) }} pasien terlayani</td>
            </tr>
            <tr>
                <td><strong>Radiologi & Imejing</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($radiologi['total'] ?? 0) }} Tindakan</td>
                <td style="text-align: right;">{{ $totAll > 0 ? round((($radiologi['total'] ?? 0) / $totAll) * 100, 1) : 0 }}%</td>
                <td>{{ number_format($radiologi['pasien'] ?? 0) }} pasien terlayani</td>
            </tr>
            <tr>
                <td><strong>Instalasi Gizi</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($gizi['total'] ?? 0) }} Porsi</td>
                <td style="text-align: right;">{{ $totAll > 0 ? round((($gizi['total'] ?? 0) / $totAll) * 100, 1) : 0 }}%</td>
                <td>{{ number_format($gizi['pasien'] ?? 0) }} pasien rawat inap</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
