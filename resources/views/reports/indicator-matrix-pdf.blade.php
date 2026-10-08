<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Matriks Indikator Pelayanan RS - {{ $tahun }}</title>
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">MATRIKS INDIKATOR PELAYANAN RAWAT INAP TAHUN {{ $tahun }}</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Rekam Medis & Manajemen Informasi Kesehatan</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Tahun Evaluasi</strong></td>
            <td style="width: 35%;">: {{ $tahun }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Standar Benchmark</strong></td>
            <td>: Kemenkes RI / Barber-Johnson</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Indikator Efisiensi</th>
                @for ($m = 1; $m <= 12; $m++)
                    <th>{{ substr(\App\Helpers\SirsHelper::getMonthName($m), 0, 3) }}</th>
                @endfor
                <th style="background-color: #e2e8f0;">Rata2</th>
            </tr>
        </thead>
        <tbody>
            @php
                $indicators = [
                    'bor' => ['label' => 'BOR (%)', 'suffix' => '%', 'standard' => '60 - 85%'],
                    'alos' => ['label' => 'ALOS (Hari)', 'suffix' => ' hr', 'standard' => '6 - 9 hari'],
                    'bto' => ['label' => 'BTO (Kali)', 'suffix' => ' x', 'standard' => '40 - 50 kali'],
                    'toi' => ['label' => 'TOI (Hari)', 'suffix' => ' hr', 'standard' => '1 - 3 hari'],
                    'ndr' => ['label' => 'NDR (‰)', 'suffix' => ' ‰', 'standard' => '< 25 ‰'],
                    'gdr' => ['label' => 'GDR (‰)', 'suffix' => ' ‰', 'standard' => '< 45 ‰'],
                ];
            @endphp
            @foreach ($indicators as $key => $ind)
                <tr>
                    <td><strong>{{ $ind['label'] }}</strong></td>
                    @for ($m = 1; $m <= 12; $m++)
                        <td>{{ $matrix['monthly'][$m][$key] ?? '-' }}</td>
                    @endfor
                    <td style="font-weight: bold; background-color: #f1f5f9;">{{ $matrix['yearly'][$key] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 9px; color: #0f172a;">Data Dasar Operasional Rawat Inap</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Data Dasar</th>
                @for ($m = 1; $m <= 12; $m++)
                    <th>{{ substr(\App\Helpers\SirsHelper::getMonthName($m), 0, 3) }}</th>
                @endfor
                <th style="background-color: #e2e8f0;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tempat Tidur Siap Pakai</td>
                @for ($m = 1; $m <= 12; $m++)
                    <td>{{ $matrix['monthly'][$m]['tempat_tidur'] ?? '-' }}</td>
                @endfor
                <td style="font-weight: bold; background-color: #f1f5f9;">{{ $matrix['yearly']['tempat_tidur'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Hari Perawatan (HP)</td>
                @for ($m = 1; $m <= 12; $m++)
                    <td>{{ number_format($matrix['monthly'][$m]['hari_perawatan'] ?? 0) }}</td>
                @endfor
                <td style="font-weight: bold; background-color: #f1f5f9;">{{ number_format($matrix['yearly']['hari_perawatan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Pasien Pulang (Hidup + Mati)</td>
                @for ($m = 1; $m <= 12; $m++)
                    <td>{{ number_format($matrix['monthly'][$m]['pasien_keluar'] ?? 0) }}</td>
                @endfor
                <td style="font-weight: bold; background-color: #f1f5f9;">{{ number_format($matrix['yearly']['pasien_keluar'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Jumlah Kematian Total</td>
                @for ($m = 1; $m <= 12; $m++)
                    <td>{{ number_format($matrix['monthly'][$m]['pasien_mati'] ?? 0) }}</td>
                @endfor
                <td style="font-weight: bold; background-color: #f1f5f9;">{{ number_format($matrix['yearly']['pasien_mati'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
