<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Insiden Keselamatan Pasien (IKP) - {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #e11d48; padding-bottom: 8px; }
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN REKAPITULASI INSIDEN KESELAMATAN PASIEN (IKP)</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Sub Komite Keselamatan Pasien (SKKP)</div>
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
            <td>: Permenkes RI No. 11 Tahun 2017</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Jenis Insiden</th>
                <th style="width: 15%; text-align: right;">Jumlah Terlapor</th>
                <th style="width: 20%;">Grading Biru/Hijau (Investigasi Sederhana)</th>
                <th style="width: 20%;">Grading Kuning/Merah (RCA)</th>
                <th style="width: 20%;">Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Kejadian Nyaris Cedera (KNC)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ $summary['counts']['knc'] ?? 0 }}</td>
                <td>Rendah / Sedang</td>
                <td>-</td>
                <td>Investigasi Sederhana</td>
            </tr>
            <tr>
                <td><strong>Kejadian Tidak Cedera (KTC)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ $summary['counts']['ktc'] ?? 0 }}</td>
                <td>Rendah / Sedang</td>
                <td>-</td>
                <td>Investigasi Sederhana</td>
            </tr>
            <tr>
                <td><strong>Kejadian Tidak Diharapkan (KTD)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ $summary['counts']['ktd'] ?? 0 }}</td>
                <td>-</td>
                <td>Tinggi / Ekstrim</td>
                <td>RCA Tim KP</td>
            </tr>
            <tr>
                <td><strong>Kejadian Sentinel</strong></td>
                <td style="text-align: right; font-weight: bold; color: #e11d48;">{{ $summary['counts']['sentinel'] ?? 0 }}</td>
                <td>-</td>
                <td>Ekstrim (Maksimal 45 Hari)</td>
                <td>RCA Komprehensif</td>
            </tr>
            <tr>
                <td><strong>Kondisi Potensial Cedera (KPC)</strong></td>
                <td style="text-align: right; font-weight: bold;">{{ $summary['counts']['kpc'] ?? 0 }}</td>
                <td>Proaktif</td>
                <td>-</td>
                <td>Tindakan Preventif</td>
            </tr>
        </tbody>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">Rincian Laporan Insiden Pasien</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 14%;">No. Insiden</th>
                <th style="width: 16%;">Jenis Insiden</th>
                <th style="width: 16%;">Unit Terkait</th>
                <th style="width: 12%;">Grading</th>
                <th style="width: 30%;">Kronologis Singkat Insiden</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incidents as $inc)
                <tr>
                    <td>{{ $inc['tanggal'] }}</td>
                    <td>{{ $inc['no_insiden'] }}</td>
                    <td><strong>{{ $inc['jenis'] }}</strong></td>
                    <td>{{ $inc['unit'] }}</td>
                    <td>{{ $inc['grading'] }}</td>
                    <td>{{ $inc['insiden'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b;">Tidak ada insiden keselamatan pasien tercatat pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
