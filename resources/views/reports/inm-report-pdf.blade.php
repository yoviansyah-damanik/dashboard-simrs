<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Indikator Nasional Mutu (INM) - {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 15px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #475569;
            font-weight: normal;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9px;
            border-collapse: collapse;
        }
        .meta-info td {
            padding: 2px 0;
            border: none;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: center;
            background-color: #f8fafc;
        }
        .summary-box .val {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-box .lbl {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge-pass {
            color: #065f46;
            font-weight: bold;
        }
        .badge-fail {
            color: #991b1b;
            font-weight: bold;
        }
        .footer {
            margin-top: 25px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer td {
            border: none;
            font-size: 9px;
        }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN CAPAIAN 13 INDIKATOR NASIONAL MUTU (INM)</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Komite Mutu dan Keselamatan Pasien (KMKP)</div>
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
            <td>: Permenkes RI No. 30 Tahun 2022</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ $summary['total_indicators'] }}</div>
                <div class="lbl">Total Indikator</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ $summary['achieved_count'] }}</div>
                <div class="lbl">Memenuhi Standar</div>
            </td>
            <td>
                <div class="val" style="color: #dc2626;">{{ $summary['unachieved_count'] }}</div>
                <div class="lbl">Belum Tercapai</div>
            </td>
            <td>
                <div class="val" style="color: #2563eb;">{{ $summary['average_score'] }}%</div>
                <div class="lbl">Rata-Rata Capaian</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">Kode</th>
                <th style="width: 32%;">Indikator Mutu Nasional</th>
                <th style="width: 14%;">Target Kemenkes</th>
                <th style="width: 12%; text-align: right;">Numerator</th>
                <th style="width: 12%; text-align: right;">Denominator</th>
                <th style="width: 12%; text-align: right;">Capaian</th>
                <th style="width: 12%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary['indicators'] as $ind)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $ind['code'] }}</td>
                    <td>
                        <strong>{{ $ind['title'] }}</strong>
                        @if (!empty($ind['additional_info']))
                            <br><span style="color: #64748b; font-size: 8px;">{{ $ind['additional_info'] }}</span>
                        @endif
                    </td>
                    <td>{{ $ind['standard_label'] }}</td>
                    <td style="text-align: right;">{{ number_format($ind['numerator']) }}</td>
                    <td style="text-align: right;">{{ number_format($ind['denominator']) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $ind['rate'] }}{{ $ind['unit'] }}</td>
                    <td style="text-align: center;">
                        <span class="{{ $ind['is_achieved'] ? 'badge-pass' : 'badge-fail' }}">
                            {{ $ind['is_achieved'] ? 'TERCAPAI' : 'BELUM' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
