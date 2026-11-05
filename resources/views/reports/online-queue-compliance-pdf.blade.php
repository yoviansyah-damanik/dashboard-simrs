<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kepatuhan Antrean Online - {{ $periodeLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-title {
            text-align: center;
            margin-bottom: 8px;
        }

        .header-title h2 {
            margin: 0 0 2px 0;
            font-size: 13px;
            color: #0f172a;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-title .sub {
            font-size: 8.5px;
            color: #64748b;
        }

        .meta-info {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8px;
            border-collapse: collapse;
        }

        .meta-info td {
            padding: 1.5px 0;
            border: none;
        }

        .kpi-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .kpi-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: center;
            background-color: #f8fafc;
        }

        .kpi-table .val {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-table .lbl {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .section-header {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            padding: 3px 6px;
            font-size: 8.5px;
            margin-top: 8px;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 3.5px 4.5px;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge-pass {
            color: #065f46;
            font-weight: bold;
            background-color: #d1fae5;
            padding: 1px 4px;
            border-radius: 2px;
            display: inline-block;
        }

        .badge-fail {
            color: #991b1b;
            font-weight: bold;
            background-color: #fee2e2;
            padding: 1px 4px;
            border-radius: 2px;
            display: inline-block;
        }

        .growth-pos {
            color: #059669;
            font-weight: bold;
        }

        .growth-neg {
            color: #dc2626;
            font-weight: bold;
        }

        .footer-table {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .footer-table td {
            border: none;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    @include('reports.partials.header')

    <div class="header-title">
        <h2>Laporan Kepatuhan & Konsistensi Pemanfaatan Antrean Online</h2>
        <div class="sub">Evaluasi Pemanfaatan Jalur JKN (Mobile JKN) vs Onsite (Loket & APM) &bull; Pelayanan Rawat
            Jalan Poliklinik</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 14%;"><strong>Periode</strong></td>
            <td style="width: 36%;">: {{ $periodeLabel }}</td>
            <td style="width: 18%;"><strong>Target Kepatuhan</strong></td>
            <td style="width: 32%;">: Minimal {{ number_format($threshold, 1) }}%</td>
        </tr>
        <tr>
            <td><strong>Poliklinik</strong></td>
            <td>: {{ $namaPoli }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Kriteria Data</strong></td>
            <td>: Rawat Jalan Poliklinik BPJS Kesehatan (kd_pj = 'BPJ', IGD & Batal Dikecualikan)</td>
            <td><strong>Petugas Evaluasi</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    {{-- KOTAK KPI RINGKASAN --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 16%;">
                <div class="val">{{ number_format($summary['total_ralan']) }}</div>
                <div class="lbl">Total Kunjungan Poli (Denominator)</div>
            </td>
            <td style="width: 17%;">
                <div class="val" style="color: #7c3aed;">{{ number_format($summary['total_jkn']) }}</div>
                <div class="lbl">JKN Terlayani (Kode B) &bull; {{ $summary['pct_jkn'] }}%</div>
            </td>
            <td style="width: 17%;">
                <div class="val" style="color: #0284c7;">{{ number_format($summary['total_loket']) }}</div>
                <div class="lbl">Onsite Loket (Kode A) &bull; {{ $summary['pct_loket'] }}%</div>
            </td>
            <td style="width: 17%;">
                <div class="val" style="color: #0d9488;">{{ number_format($summary['total_apm']) }}</div>
                <div class="lbl">Onsite APM (Kode D) &bull; {{ $summary['pct_apm'] }}%</div>
            </td>
            <td style="width: 17%;">
                <div class="val">{{ number_format($summary['total_onsite']) }}</div>
                <div class="lbl">Total Onsite (A+D) &bull; {{ $summary['pct_onsite'] }}%</div>
            </td>
            <td style="width: 16%;">
                <div class="val" style="color: {{ $summary['is_patuh'] ? '#059669' : '#dc2626' }};">
                    {{ $summary['pct_jkn'] }}%
                </div>
                <div class="lbl">
                    @if ($summary['is_patuh'])
                        <span class="badge-pass">TERCAPAI
                            ({{ $summary['gap_threshold'] >= 0 ? '+' : '' }}{{ $summary['gap_threshold'] }}%)</span>
                    @else
                        <span class="badge-fail">BELUM ({{ $summary['gap_threshold'] }}%)</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- TABEL 1: REKAPITULASI TAHUNAN & MONTH-OVER-MONTH (MoM) GROWTH --}}
    <div class="section-header">1. Rekapitulasi Tren Bulanan & Pertumbuhan (Month-over-Month Growth) Tahun
        {{ $year }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;" class="text-center">No</th>
                <th rowspan="2" style="width: 12%;">Bulan</th>
                <th colspan="2" class="text-center">Total Kunjungan Poli</th>
                <th colspan="3" class="text-center">JKN (Kode B)</th>
                <th colspan="2" class="text-center">Onsite Loket (A)</th>
                <th colspan="2" class="text-center">Onsite APM (D)</th>
                <th colspan="2" class="text-center">Total Onsite (A+D)</th>
                <th rowspan="2" style="width: 8%;" class="text-center">Status (Target &ge;{{ (int) $threshold }}%)
                </th>
            </tr>
            <tr>
                <th style="width: 7%;" class="text-right">Volume</th>
                <th style="width: 6%;" class="text-center">MoM %</th>
                <th style="width: 7%;" class="text-right">Volume</th>
                <th style="width: 6%;" class="text-center">Rasio %</th>
                <th style="width: 6%;" class="text-center">MoM %</th>
                <th style="width: 6%;" class="text-right">Volume</th>
                <th style="width: 5%;" class="text-center">Rasio %</th>
                <th style="width: 6%;" class="text-right">Volume</th>
                <th style="width: 5%;" class="text-center">Rasio %</th>
                <th style="width: 6%;" class="text-right">Volume</th>
                <th style="width: 5%;" class="text-center">Rasio %</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totAllRalan = 0;
                $totAllJkn = 0;
                $totAllLoket = 0;
                $totAllApm = 0;
            @endphp
            @foreach ($monthlyReport as $row)
                @php
                    $totAllRalan += $row['total_ralan'];
                    $totAllJkn += $row['jkn'];
                    $totAllLoket += $row['loket'];
                    $totAllApm += $row['apm'];
                @endphp
                <tr>
                    <td class="text-center">{{ $row['month_num'] }}</td>
                    <td><strong>{{ $row['month_name'] }}</strong></td>
                    <td class="text-right">{{ number_format($row['total_ralan']) }}</td>
                    <td class="text-center">
                        @if ($row['growth_total'] !== null)
                            <span class="{{ $row['growth_total'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                {{ $row['growth_total'] > 0 ? '+' : '' }}{{ $row['growth_total'] }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #7c3aed;">{{ number_format($row['jkn']) }}
                    </td>
                    <td class="text-center"
                        style="font-weight: bold; color: {{ $row['pct_jkn'] >= $threshold ? '#059669' : '#dc2626' }};">
                        {{ $row['pct_jkn'] }}%
                    </td>
                    <td class="text-center">
                        @if ($row['growth_jkn'] !== null)
                            <span class="{{ $row['growth_jkn'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                {{ $row['growth_jkn'] > 0 ? '+' : '' }}{{ $row['growth_jkn'] }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($row['loket']) }}</td>
                    <td class="text-center">{{ $row['pct_loket'] }}%</td>
                    <td class="text-right">{{ number_format($row['apm']) }}</td>
                    <td class="text-center">{{ $row['pct_apm'] }}%</td>
                    <td class="text-right">{{ number_format($row['onsite']) }}</td>
                    <td class="text-center">{{ $row['pct_onsite'] }}%</td>
                    <td class="text-center">
                        @if ($row['total_ralan'] > 0)
                            @if ($row['is_patuh'])
                                <span class="badge-pass">TERCAPAI</span>
                            @else
                                <span class="badge-fail">BELUM</span>
                            @endif
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @php
                $totAllOnsite = $totAllLoket + $totAllApm;
                $totPctJkn = $totAllRalan > 0 ? round(($totAllJkn / $totAllRalan) * 100, 2) : 0.0;
                $totPctLoket = $totAllRalan > 0 ? round(($totAllLoket / $totAllRalan) * 100, 2) : 0.0;
                $totPctApm = $totAllRalan > 0 ? round(($totAllApm / $totAllRalan) * 100, 2) : 0.0;
                $totPctOnsite = $totAllRalan > 0 ? round(($totAllOnsite / $totAllRalan) * 100, 2) : 0.0;
                $totIsPatuh = $totPctJkn >= $threshold;
            @endphp
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL TAHUNAN</td>
                <td class="text-right">{{ number_format($totAllRalan) }}</td>
                <td class="text-center">-</td>
                <td class="text-right" style="color: #7c3aed;">{{ number_format($totAllJkn) }}</td>
                <td class="text-center" style="color: {{ $totIsPatuh ? '#059669' : '#dc2626' }};">
                    {{ $totPctJkn }}%</td>
                <td class="text-center">-</td>
                <td class="text-right">{{ number_format($totAllLoket) }}</td>
                <td class="text-center">{{ $totPctLoket }}%</td>
                <td class="text-right">{{ number_format($totAllApm) }}</td>
                <td class="text-center">{{ $totPctApm }}%</td>
                <td class="text-right">{{ number_format($totAllOnsite) }}</td>
                <td class="text-center">{{ $totPctOnsite }}%</td>
                <td class="text-center">
                    @if ($totIsPatuh)
                        <span class="badge-pass">TERCAPAI</span>
                    @else
                        <span class="badge-fail">BELUM</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- TABEL 2: MATRIKS 12 BULAN & PERTUMBUHAN MOM KUMULATIF PER POLIKLINIK & DOKTER DPJP --}}
    @if (!empty($annualPolyDoc) && empty($month))
        <div class="section-header">2. Matriks 12 Bulan & Pertumbuhan MoM Kumulatif per Poliklinik & Dokter DPJP (Tahun {{ $year }})</div>

        {{-- RINCIAN PER BULAN & GROWTH MOM PER POLIKLINIK & DOKTER DPJP (IDENTIK DENGAN TABEL 1) --}}
        @foreach (array_slice($annualPolyDoc, 0, 4) as $docIdx => $poli)
            <div style="font-size: 8pt; font-weight: bold; margin-top: 6px; margin-bottom: 2px; color: #0f172a;">
                2.{{ $docIdx + 1 }}. {{ $poli['nm_poli'] }} &mdash; {{ $poli['nm_dokter'] }}
                <span style="font-size: 7.5pt; font-weight: normal; color: #475569;">
                    &bull; Total Setahun: {{ number_format($poli['total']) }} Pasien | JKN: {{ number_format($poli['jkn']) }} ({{ $poli['pct_jkn'] }}%) &bull; Status: {{ $poli['is_patuh'] ? 'TERCAPAI' : 'DI BAWAH TARGET' }}
                </span>
            </div>
            <table class="data-table" style="margin-bottom: 8px;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 3%;" class="text-center">No</th>
                        <th rowspan="2" style="width: 12%;">Bulan</th>
                        <th colspan="2" class="text-center">Total Kunjungan</th>
                        <th colspan="3" class="text-center">JKN (Kode B)</th>
                        <th colspan="2" class="text-center">Loket (A)</th>
                        <th colspan="2" class="text-center">APM (D)</th>
                        <th colspan="2" class="text-center">Total Onsite</th>
                        <th rowspan="2" style="width: 8%;" class="text-center">Status</th>
                    </tr>
                    <tr>
                        <th style="width: 7%;" class="text-right">Volume</th>
                        <th style="width: 6%;" class="text-center">MoM %</th>
                        <th style="width: 7%;" class="text-right">Volume</th>
                        <th style="width: 6%;" class="text-center">Rasio %</th>
                        <th style="width: 6%;" class="text-center">MoM %</th>
                        <th style="width: 6%;" class="text-right">Volume</th>
                        <th style="width: 5%;" class="text-center">Rasio %</th>
                        <th style="width: 6%;" class="text-right">Volume</th>
                        <th style="width: 5%;" class="text-center">Rasio %</th>
                        <th style="width: 6%;" class="text-right">Volume</th>
                        <th style="width: 5%;" class="text-center">Rasio %</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $docSumTotal = 0; $docSumJkn = 0; $docSumLoket = 0; $docSumApm = 0;
                    @endphp
                    @foreach ($poli['monthly'] as $mIdx => $m)
                        @php
                            $docSumTotal += $m['total'];
                            $docSumJkn += $m['jkn'];
                            $docSumLoket += $m['loket'];
                            $docSumApm += $m['apm'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $mIdx + 1 }}</td>
                            <td><strong>{{ $m['month_name'] }}</strong></td>
                            <td class="text-right">{{ number_format($m['total']) }}</td>
                            <td class="text-center">
                                @if ($m['growth_total'] !== null)
                                    <span class="{{ $m['growth_total'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                        {{ $m['growth_total'] > 0 ? '+' : '' }}{{ $m['growth_total'] }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right" style="font-weight: bold; color: #7c3aed;">{{ number_format($m['jkn']) }}</td>
                            <td class="text-center" style="font-weight: bold; color: {{ $m['pct_jkn'] >= $threshold ? '#059669' : '#dc2626' }};">
                                {{ $m['pct_jkn'] }}%
                            </td>
                            <td class="text-center">
                                @if ($m['growth_jkn'] !== null)
                                    <span class="{{ $m['growth_jkn'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                        {{ $m['growth_jkn'] > 0 ? '+' : '' }}{{ $m['growth_jkn'] }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($m['loket']) }}</td>
                            <td class="text-center">{{ $m['pct_loket'] }}%</td>
                            <td class="text-right">{{ number_format($m['apm']) }}</td>
                            <td class="text-center">{{ $m['pct_apm'] }}%</td>
                            <td class="text-right">{{ number_format($m['onsite']) }}</td>
                            <td class="text-center">{{ $m['pct_onsite'] }}%</td>
                            <td class="text-center">
                                @if ($m['total'] > 0)
                                    @if ($m['is_patuh'])
                                        <span class="badge-pass">TERCAPAI</span>
                                    @else
                                        <span class="badge-fail">BELUM</span>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $docSumOnsite = $docSumLoket + $docSumApm;
                        $docTotPctJkn = $docSumTotal > 0 ? round(($docSumJkn / $docSumTotal) * 100, 2) : 0.0;
                        $docTotPctLoket = $docSumTotal > 0 ? round(($docSumLoket / $docSumTotal) * 100, 2) : 0.0;
                        $docTotPctApm = $docSumTotal > 0 ? round(($docSumApm / $docSumTotal) * 100, 2) : 0.0;
                        $docTotPctOnsite = $docSumTotal > 0 ? round(($docSumOnsite / $docSumTotal) * 100, 2) : 0.0;
                        $docTotIsPatuh = $docTotPctJkn >= $threshold;
                    @endphp
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="2" class="text-center">TOTAL TAHUNAN</td>
                        <td class="text-right">{{ number_format($docSumTotal) }}</td>
                        <td class="text-center">-</td>
                        <td class="text-right" style="color: #7c3aed;">{{ number_format($docSumJkn) }}</td>
                        <td class="text-center" style="color: {{ $docTotIsPatuh ? '#059669' : '#dc2626' }};">{{ $docTotPctJkn }}%</td>
                        <td class="text-center">-</td>
                        <td class="text-right">{{ number_format($docSumLoket) }}</td>
                        <td class="text-center">{{ $docTotPctLoket }}%</td>
                        <td class="text-right">{{ number_format($docSumApm) }}</td>
                        <td class="text-center">{{ $docTotPctApm }}%</td>
                        <td class="text-right">{{ number_format($docSumOnsite) }}</td>
                        <td class="text-center">{{ $docTotPctOnsite }}%</td>
                        <td class="text-center">
                            @if ($docTotIsPatuh)
                                <span class="badge-pass">TERCAPAI</span>
                            @else
                                <span class="badge-fail">BELUM</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach

        {{-- REKAPITULASI KUMULATIF SELURUH DOKTER DPJP --}}
        <div style="font-size: 8pt; font-weight: bold; margin-top: 10px; margin-bottom: 2px; color: #0f172a;">
            Rekapitulasi Kumulatif Seluruh Unit Poliklinik & Dokter DPJP (Tahun {{ $year }})
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;" class="text-center">No</th>
                    <th style="width: 20%;">Poliklinik</th>
                    <th style="width: 22%;">Dokter Penanggung Jawab</th>
                    <th style="width: 9%;" class="text-right">Total Setahun</th>
                    <th style="width: 9%;" class="text-right">JKN (B)</th>
                    <th style="width: 7%;" class="text-center">% JKN</th>
                    <th style="width: 8%;" class="text-right">Loket (A)</th>
                    <th style="width: 7%;" class="text-right">APM (D)</th>
                    <th style="width: 8%;" class="text-right">Onsite</th>
                    <th style="width: 7%;" class="text-center">Rata2/Bln</th>
                    <th style="width: 10%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse (array_slice($annualPolyDoc, 0, 20) as $idx => $poli)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td><strong>{{ $poli['nm_poli'] }}</strong></td>
                        <td>{{ $poli['nm_dokter'] }}</td>
                        <td class="text-right">{{ number_format($poli['total']) }}</td>
                        <td class="text-right" style="color: #7c3aed; font-weight: bold;">
                            {{ number_format($poli['jkn']) }}
                        </td>
                        <td class="text-center"
                            style="font-weight: bold; color: {{ $poli['pct_jkn'] >= $threshold ? '#059669' : '#dc2626' }};">
                            {{ $poli['pct_jkn'] }}%
                        </td>
                        <td class="text-right">{{ number_format($poli['loket']) }}</td>
                        <td class="text-right">{{ number_format($poli['apm']) }}</td>
                        <td class="text-right">{{ number_format($poli['onsite']) }}</td>
                        <td class="text-center font-bold">{{ number_format($poli['avg_monthly'], 1) }}</td>
                        <td class="text-center">
                            @if ($poli['is_patuh'])
                                <span class="badge-pass">TERCAPAI</span>
                            @else
                                <span class="badge-fail">BELUM</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 10px; color: #64748b;">Tidak ada data poliklinik untuk tahun ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <div class="section-header">2. Distribusi, Kepatuhan & Pertumbuhan MoM per Poliklinik & Dokter (vs {{ $polyclinicRows[0]['prev_period_label'] ?? 'Bulan Lalu' }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;" class="text-center">No</th>
                    <th style="width: 18%;">Poliklinik</th>
                    <th style="width: 20%;">Dokter Penanggung Jawab</th>
                    <th style="width: 7%;" class="text-right">Total</th>
                    <th style="width: 7%;" class="text-center">MoM Tot</th>
                    <th style="width: 7%;" class="text-right">JKN (B)</th>
                    <th style="width: 6%;" class="text-center">% JKN</th>
                    <th style="width: 7%;" class="text-center">MoM JKN</th>
                    <th style="width: 6%;" class="text-center">&Delta; %</th>
                    <th style="width: 6%;" class="text-right">Loket</th>
                    <th style="width: 6%;" class="text-right">APM</th>
                    <th style="width: 7%;" class="text-right">Onsite</th>
                </tr>
            </thead>
            <tbody>
                @forelse (array_slice($polyclinicRows, 0, 18) as $idx => $poli)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td><strong>{{ $poli['nm_poli'] }}</strong></td>
                        <td>{{ $poli['nm_dokter'] }}</td>
                        <td class="text-right">{{ number_format($poli['total']) }}</td>
                        <td class="text-center">
                            @if ($poli['growth_total'] !== null)
                                <span class="{{ $poli['growth_total'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                    {{ $poli['growth_total'] > 0 ? '+' : '' }}{{ $poli['growth_total'] }}%
                                </span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right" style="color: #7c3aed; font-weight: bold;">
                            {{ number_format($poli['jkn']) }}
                        </td>
                        <td class="text-center"
                            style="font-weight: bold; color: {{ $poli['pct_jkn'] >= $threshold ? '#059669' : '#dc2626' }};">
                            {{ $poli['pct_jkn'] }}%
                        </td>
                        <td class="text-center">
                            @if ($poli['growth_jkn'] !== null)
                                <span class="{{ $poli['growth_jkn'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                    {{ $poli['growth_jkn'] > 0 ? '+' : '' }}{{ $poli['growth_jkn'] }}%
                                </span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($poli['delta_pct_jkn'] !== null)
                                <span class="{{ $poli['delta_pct_jkn'] >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                    {{ $poli['delta_pct_jkn'] > 0 ? '+' : '' }}{{ $poli['delta_pct_jkn'] }}%
                                </span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($poli['loket']) }}</td>
                        <td class="text-right">{{ number_format($poli['apm']) }}</td>
                        <td class="text-right">{{ number_format($poli['onsite']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 10px; color: #64748b;">Tidak ada data
                            poliklinik untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- RINGKASAN AUDIT KESESUAIAN DATA JKN VS SEP --}}
    @if (!empty($summary['conformity']))
        <div class="section-header">3. Ringkasan Audit Kesesuaian Antrean JKN terhadap SEP & Surat Kontrol / Rujukan
        </div>
        <table class="kpi-table" style="margin-bottom: 5px;">
            <tr>
                <td style="width: 20%;">
                    <div class="val">{{ number_format($summary['conformity']['total']) }}</div>
                    <div class="lbl">Total Booking JKN</div>
                </td>
                <td style="width: 20%;">
                    <div class="val" style="color: #059669;">{{ number_format($summary['conformity']['sesuai']) }}
                    </div>
                    <div class="lbl">Data Sesuai ({{ $summary['conformity']['pct_sesuai'] }}%)</div>
                </td>
                <td style="width: 20%;">
                    <div class="val" style="color: #dc2626;">{{ number_format($summary['conformity']['tidak']) }}
                    </div>
                    <div class="lbl">Tidak Sesuai / Anomali</div>
                </td>
                <td style="width: 20%;">
                    <div class="val" style="color: #0284c7;">
                        {{ number_format($summary['conformity']['dengan_sep']) }}</div>
                    <div class="lbl">SEP Terbit Valid ({{ $summary['conformity']['pct_valid_sep'] }}%)</div>
                </td>
                <td style="width: 20%;">
                    <div class="val" style="color: #ea580c;">
                        {{ number_format($summary['conformity']['tanpa_sep']) }}</div>
                    <div class="lbl">Antrean Tanpa SEP</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- TANDA TANGAN FORMAL --}}
    <table class="footer-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <div>Padangsidimpuan, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 3px; font-weight: bold;">Mengetahui / Penanggung Jawab Evaluasi</div>
                <div style="height: 45px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $printedBy }}</div>
                <div style="font-size: 7.5px; color: #64748b;">Tim Pengendalian Mutu & SIMRS</div>
            </td>
        </tr>
    </table>
</body>

</html>
