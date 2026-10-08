<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Matriks Indikator Farmasi RS - {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 4px 5px; text-align: right; }
        table.data-table th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data-table th:first-child, table.data-table td:first-child { text-align: left; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 25px; width: 100%; border-collapse: collapse; }
        .footer td { border: none; font-size: 9px; }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">MATRIKS INDIKATOR PELAYANAN RESEP TAHUN {{ $tahun }}</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Farmasi Rumah Sakit</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Tahun Evaluasi</strong></td>
            <td style="width: 35%;">: {{ $tahun }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Sumber Data</strong></td>
            <td>: Database Farmasi SIMRS</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22%;">Parameter Farmasi</th>
                @for ($m = 1; $m <= 12; $m++)
                    <th>{{ substr(\App\Helpers\SirsHelper::getMonthName($m), 0, 3) }}</th>
                @endfor
                <th style="background-color: #e2e8f0;">Total / Avg</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rows = [
                    'total_resep' => 'Total Lembar Resep (R/)',
                    'ralan' => 'Resep Rawat Jalan (Ralan)',
                    'ranap' => 'Resep Rawat Inap (Ranap)',
                    'diserahkan' => 'Resep Diserahkan (Tuntas)',
                    'belum_diserahkan' => 'Resep Belum Diserahkan',
                    'biasa' => 'Kategori Obat Biasa',
                    'kronis' => 'Kategori Obat Kronis',
                    'cito' => 'Kategori Obat CITO',
                    'prb' => 'Kategori Program PRB',
                    'waktu_tunggu' => 'Waktu Tunggu Rata2 (Menit)',
                ];
            @endphp
            @foreach ($rows as $field => $label)
                <tr style="{{ $field === 'total_resep' ? 'font-weight: bold; background-color: #ecfdf5;' : '' }}">
                    <td>{{ $label }}</td>
                    @for ($m = 1; $m <= 12; $m++)
                        <td>{{ $field === 'waktu_tunggu' ? ($matrix['months'][$m][$field] ?? 0) . 'm' : number_format($matrix['months'][$m][$field] ?? 0) }}</td>
                    @endfor
                    <td style="font-weight: bold; background-color: #f1f5f9;">
                        {{ $field === 'waktu_tunggu' ? ($matrix['averages'][$field] ?? 0) . 'm' : number_format($matrix['totals'][$field] ?? 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
