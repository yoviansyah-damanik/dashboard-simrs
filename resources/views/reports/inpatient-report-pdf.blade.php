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
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 20px;
            margin-bottom: 6px;
            border-left: 3px solid #7c3aed;
            padding-left: 6px;
        }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 12px;">
        <h3 style="margin: 0; font-size: 13px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN PASIEN RAWAT INAP</h3>
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
                | <strong>TNI:</strong> {{ number_format($summary['total_tni'] ?? 0, 0, ',', '.') }}
                | <strong>POLRI:</strong> {{ number_format($summary['total_polri'] ?? 0, 0, ',', '.') }}
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
                <th style="width: 95px;">Penjamin</th>
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
                    <td>
                        {{ $patient->png_jawab ?? '-' }}
                        @if($patient->status_dinas === 'TNI')
                            <span style="display: inline-block; padding: 1px 4px; font-size: 8px; font-weight: bold; background: #dcfce7; color: #166534; border-radius: 3px;">TNI</span>
                        @elseif($patient->status_dinas === 'POLRI')
                            <span style="display: inline-block; padding: 1px 4px; font-size: 8px; font-weight: bold; background: #dbeafe; color: #1e40af; border-radius: 3px;">POLRI</span>
                        @endif
                    </td>
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

    {{-- Rekapitulasi Pasien Dinas (TNI / POLRI) --}}
    @if (!empty($dinasBreakdown) && !empty($dinasBreakdown['wards']))
        <div style="page-break-inside: avoid;">
            <div class="section-title">Rekapitulasi Pasien Dinas Rawat Inap (TNI / POLRI) per Bangsal</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px; text-align: center;">No</th>
                        <th>Bangsal / Ruangan</th>
                        <th style="width: 60px; text-align: center;">Total Dinas</th>
                        <th style="width: 50px; text-align: center;">Proporsi</th>
                        <th style="width: 55px; text-align: center;">TNI</th>
                        <th style="width: 55px; text-align: center;">POLRI</th>
                        <th style="width: 70px; text-align: center;">Masih Dirawat</th>
                        <th style="width: 70px; text-align: center;">Sudah Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dinasBreakdown['wards'] as $index => $dw)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td><strong>{{ $dw['nm_bangsal'] }}</strong> ({{ $dw['kd_bangsal'] }})</td>
                            <td style="text-align: center; font-weight: bold; color: #7c3aed;">{{ number_format($dw['total'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ $dw['percent'] }}%</td>
                            <td style="text-align: center;">{{ number_format($dw['tni'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($dw['polri'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($dw['masih_dirawat'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($dw['sudah_pulang'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!empty($dinasBreakdown['categories']))
            <div style="page-break-inside: avoid; margin-top: 15px;">
                <div class="section-title" style="border-left-color: #0284c7;">Distribusi Kategori Personel Pasien Dinas Rawat Inap</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25px; text-align: center;">No</th>
                            <th>Kategori Personel</th>
                            <th style="width: 60px; text-align: center;">Total Pasien</th>
                            <th style="width: 50px; text-align: center;">Proporsi</th>
                            <th style="width: 55px; text-align: center;">TNI</th>
                            <th style="width: 55px; text-align: center;">POLRI</th>
                            <th style="width: 70px; text-align: center;">Masih Dirawat</th>
                            <th style="width: 70px; text-align: center;">Sudah Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dinasBreakdown['categories'] as $cIdx => $cat)
                            <tr>
                                <td style="text-align: center;">{{ $cIdx + 1 }}</td>
                                <td><strong>{{ $cat['kategori'] }}</strong></td>
                                <td style="text-align: center; font-weight: bold; color: #0284c7;">{{ number_format($cat['total'], 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ $cat['percent'] }}%</td>
                                <td style="text-align: center;">{{ number_format($cat['tni'], 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ number_format($cat['polri'], 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ number_format($cat['masih_dirawat'], 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ number_format($cat['sudah_pulang'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @if (!empty($diagnosisBreakdown))
        <div style="page-break-inside: avoid; margin-top: 15px;">
            <div class="section-title" style="border-left-color: #e11d48;">Rekapitulasi Top 20 Diagnosa Penyakit Terbanyak (ICD-10) Rawat Inap</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px; text-align: center;">Rank</th>
                        <th style="width: 55px; text-align: center;">Kode ICD</th>
                        <th>Nama Penyakit / Diagnosa</th>
                        <th style="width: 50px; text-align: center;">Primer</th>
                        <th style="width: 50px; text-align: center;">Sekunder</th>
                        <th style="width: 55px; text-align: center;">Total Kasus</th>
                        <th style="width: 45px; text-align: center;">Proporsi</th>
                        <th style="width: 50px; text-align: center;">L / P</th>
                        <th style="width: 50px; text-align: center;">Meninggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($diagnosisBreakdown, 0, 20) as $diag)
                        <tr>
                            <td style="text-align: center;">{{ $diag['rank'] }}</td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold; color: #e11d48;">{{ $diag['kd_penyakit'] }}</td>
                            <td><strong>{{ $diag['nm_penyakit'] }}</strong></td>
                            <td style="text-align: center;">{{ number_format($diag['primer'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ number_format($diag['sekunder'], 0, ',', '.') }}</td>
                            <td style="text-align: center; font-weight: bold; color: #0f172a;">{{ number_format($diag['total_kasus'] ?? $diag['total'], 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ $diag['percent'] }}%</td>
                            <td style="text-align: center;">{{ number_format($diag['pria'], 0, ',', '.') }} / {{ number_format($diag['wanita'], 0, ',', '.') }}</td>
                            <td style="text-align: center; color: #e11d48; font-weight: bold;">{{ number_format($diag['meninggal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
