<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kunjungan & Pengunjung Pasien</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        .summary-box { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background-color: #f8fafc; }
        .summary-box .val { font-size: 14px; font-weight: bold; color: #0f172a; }
        .summary-box .lbl { font-size: 8.5px; color: #64748b; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN KUNJUNGAN DAN PENGUNJUNG PASIEN</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Rekam Medis & Informasi Kesehatan</div>
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
                <div class="val">{{ number_format($summary['total_kunjungan'] ?? 0) }}</div>
                <div class="lbl">Total Kunjungan</div>
            </td>
            <td>
                <div class="val" style="color: #0284c7;">{{ number_format($summary['total_pengunjung'] ?? 0) }}</div>
                <div class="lbl">Total Pengunjung</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ number_format($summary['pengunjung_baru'] ?? 0) }}</div>
                <div class="lbl">Pasien Baru</div>
            </td>
            <td>
                <div class="val" style="color: #7c3aed;">{{ number_format($summary['pengunjung_lama'] ?? 0) }}</div>
                <div class="lbl">Pasien Lama</div>
            </td>
        </tr>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">1. Distribusi Kunjungan per Jenis Pelayanan</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Instalasi Pelayanan</th>
                <th style="width: 30%; text-align: right;">Jumlah Kunjungan</th>
                <th style="width: 30%; text-align: right;">Proporsi (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Rawat Jalan (Poliklinik)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($summary['kunjungan_ralan'] ?? 0) }}</td>
                <td style="text-align: right;">{{ ($summary['total_kunjungan'] ?? 0) > 0 ? round((($summary['kunjungan_ralan'] ?? 0) / $summary['total_kunjungan']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td><strong>Rawat Inap (Bangsal)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($summary['kunjungan_ranap'] ?? 0) }}</td>
                <td style="text-align: right;">{{ ($summary['total_kunjungan'] ?? 0) > 0 ? round((($summary['kunjungan_ranap'] ?? 0) / $summary['total_kunjungan']) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">2. Demografi Pasien (Jenis Kelamin)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Jenis Kelamin</th>
                <th style="width: 30%; text-align: right;">Jumlah Pasien</th>
                <th style="width: 30%; text-align: right;">Proporsi (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Laki-Laki (L)</strong></td>
                <td style="text-align: right;">{{ number_format($summary['gender_male'] ?? 0) }}</td>
                <td style="text-align: right;">{{ ($summary['total_pengunjung'] ?? 0) > 0 ? round((($summary['gender_male'] ?? 0) / $summary['total_pengunjung']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td><strong>Perempuan (P)</strong></td>
                <td style="text-align: right;">{{ number_format($summary['gender_female'] ?? 0) }}</td>
                <td style="text-align: right;">{{ ($summary['total_pengunjung'] ?? 0) > 0 ? round((($summary['gender_female'] ?? 0) / $summary['total_pengunjung']) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
