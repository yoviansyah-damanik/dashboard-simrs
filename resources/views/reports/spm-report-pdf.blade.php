<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Standar Pelayanan Minimal (SPM) - {{ $year }}</title>
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
            border-bottom: 2px solid #4f46e5;
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
        .section-title {
            background-color: #e0e7ff;
            color: #312e81;
            font-weight: bold;
            padding: 5px 8px;
            font-size: 10px;
            margin-top: 10px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN STANDAR PELAYANAN MINIMAL (SPM) RUMAH SAKIT</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Bidang Pelayanan Medis & Keperawatan</div>
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
            <td>: Kepmenkes RI No. 129/Menkes/SK/II/2008</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ $summary['total_indikator'] }}</div>
                <div class="lbl">Total Indikator SPM</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ $summary['total_tercapai'] }}</div>
                <div class="lbl">Memenuhi Standar</div>
            </td>
            <td>
                <div class="val" style="color: #dc2626;">{{ $summary['total_indikator'] - $summary['total_tercapai'] }}</div>
                <div class="lbl">Belum Tercapai</div>
            </td>
            <td>
                <div class="val" style="color: #4f46e5;">{{ $summary['persen_tercapai'] }}%</div>
                <div class="lbl">Tingkat Kepatuhan</div>
            </td>
        </tr>
    </table>

    @foreach ($summary['sections'] as $secKey => $sec)
        <div class="section-title">{{ strtoupper($sec['unit']) }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Indikator Standar</th>
                    <th style="width: 15%;">Standar SPM</th>
                    <th style="width: 15%; text-align: right;">Capaian</th>
                    <th style="width: 15%; text-align: center;">Status</th>
                    <th style="width: 15%;">Keterangan Data Riil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sec['indicators'] as $ind)
                    <tr>
                        <td><strong>{{ $ind['nama'] }}</strong></td>
                        <td>{{ $ind['standar'] }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $ind['capaian'] }}</td>
                        <td style="text-align: center;">
                            <span class="{{ $ind['is_achieved'] ? 'badge-pass' : 'badge-fail' }}">
                                {{ $ind['is_achieved'] ? 'SESUAI' : 'BELUM' }}
                            </span>
                        </td>
                        <td style="font-size: 8px; color: #475569;">{{ $ind['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
