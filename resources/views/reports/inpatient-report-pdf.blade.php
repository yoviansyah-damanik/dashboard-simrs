<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pasien Rawat Inap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 0 0 6px 0;
            font-size: 12px;
            font-weight: normal;
            color: #475569;
        }
        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .meta-row {
            display: table-row;
        }
        .meta-cell {
            display: table-cell;
            padding: 2px 0;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-active {
            background-color: #fef3c7;
            color: #b45309;
        }
        .badge-done {
            background-color: #dcfce7;
            color: #15803d;
        }
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 25px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        @page {
            margin: 1.2cm 1cm 1.8cm 1cm;
        }
    </style>
</head>
<body>
    <footer>
        Dokumen ini dibuat otomatis melalui {{ config('app.name') }} milik {{ config('app.hospital_name') }} pada {{ now()->format('d/m/Y H:i:s') }}.
    </footer>

    <div class="header">
        <h2>{{ config('app.hospital_name', 'RUMAH SAKIT') }}</h2>
        <h3>LAPORAN PASIEN RAWAT INAP</h3>
    </div>

    <div class="meta-info">
        <div class="meta-row">
            <div class="meta-cell" style="width: 50%;">
                <strong>Periode Tanggal Masuk:</strong>
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </div>
            <div class="meta-cell" style="width: 50%; text-align: right;">
                <strong>Penjamin / Cara Bayar:</strong> {{ $payTypeTitle ?? 'Semua Penjamin' }}
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">
                <strong>Total Pasien:</strong> {{ number_format($summary['total_pasien'] ?? count($patients), 0, ',', '.') }} orang
                (Sudah Pulang: {{ number_format($summary['sudah_pulang'] ?? 0, 0, ',', '.') }},
                Masih Dirawat: {{ number_format($summary['masih_dirawat'] ?? 0, 0, ',', '.') }})
            </div>
            <div class="meta-cell" style="text-align: right;">
                <strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i') }} WIB
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 105px;">No. Rawat</th>
                <th style="width: 55px;">No. RM</th>
                <th>Nama Pasien</th>
                <th>Bangsal</th>
                <th style="width: 65px; text-align: center;">Tgl Masuk</th>
                <th style="width: 65px; text-align: center;">Tgl Keluar</th>
                <th style="width: 90px;">Penjamin</th>
                <th>DPJP Ranap</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patients as $index => $patient)
                @php
                    $isMasihDirawat = ($patient->tgl_keluar == '0000-00-00' || empty($patient->tgl_keluar));
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $patient->no_rawat }}</td>
                    <td>{{ $patient->no_rkm_medis }}</td>
                    <td><strong>{{ $patient->nm_pasien }}</strong></td>
                    <td>{{ $patient->nm_bangsal }}</td>
                    <td style="text-align: center;">
                        {{ \Carbon\Carbon::parse($patient->tgl_masuk)->format('d/m/Y') }}
                    </td>
                    <td style="text-align: center;">
                        @if($isMasihDirawat)
                            <span class="badge badge-active">Dirawat</span>
                        @else
                            {{ \Carbon\Carbon::parse($patient->tgl_keluar)->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>{{ $patient->png_jawab ?? '-' }}</td>
                    <td>{{ $patient->dpjp_ranap ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Tidak ada data pasien rawat inap untuk periode dan kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
