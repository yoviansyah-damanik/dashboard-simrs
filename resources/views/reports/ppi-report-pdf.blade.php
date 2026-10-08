<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Surveilans PPI & HAIs - {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #0284c7; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN SURVEILANS INFEKSI RUMAH SAKIT (HAIs)</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Komite Pencegahan dan Pengendalian Infeksi (KPPI)</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Periode</strong></td>
            <td style="width: 35%;">: {{ $month ? 'Bulan ' . $month . ' ' . $year : 'Tahun ' . $year . ' (Kumulatif Tahunan)' }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Regulasi</strong></td>
            <td>: Permenkes RI No. 27 Tahun 2017</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Parameter Surveilans HAIs</th>
                <th style="width: 15%; text-align: right;">Numerator (Kasus)</th>
                <th style="width: 15%; text-align: right;">Denominator (Hari Pasang)</th>
                <th style="width: 15%; text-align: right;">Laju Infeksi (‰)</th>
                <th style="width: 15%; text-align: center;">Target Standar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary['indicators'] as $indKey => $ind)
                <tr>
                    <td><strong>{{ $ind['nama'] ?? '-' }}</strong></td>
                    <td style="text-align: right;">{{ number_format($ind['kasus'] ?? 0) }}</td>
                    <td style="text-align: right;">{{ number_format($ind['hari_alat'] ?? 0) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $ind['rate'] ?? 0 }} {{ $ind['satuan'] ?? '‰' }}</td>
                    <td style="text-align: center;">{{ $ind['standar'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">Daftar Sampel Kasus Teridentifikasi HAIs</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">No. Rawat</th>
                <th style="width: 25%;">Nama Pasien</th>
                <th style="width: 20%;">Ruang Rawat</th>
                <th style="width: 28%;">Jenis Infeksi Terdeteksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($patientList as $p)
                <tr>
                    <td>{{ $p['tanggal'] ?? '-' }}</td>
                    <td>{{ $p['no_rawat'] ?? '-' }}</td>
                    <td>{{ $p['pasien'] ?? '-' }}</td>
                    <td>{{ $p['kamar'] ?? '-' }}</td>
                    <td>{{ $p['temuan_infeksi'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;">Tidak ditemukan kasus HAIs pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
