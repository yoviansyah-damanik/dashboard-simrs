<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Matriks 12 Bulan & Pertumbuhan MoM Kasus Penyakit</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            color: #1e293b;
            line-height: 1.25;
        }
        .header-kop {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }
        .hospital-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .hospital-meta {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }
        .report-title {
            text-align: center;
            margin: 8px 0;
        }
        .report-title h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #64748b;
        }
        .filter-meta {
            margin-bottom: 8px;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .filter-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-meta td {
            padding: 2px 4px;
            font-size: 7pt;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 13%;
        }
        .filter-value {
            color: #0f172a;
            width: 37%;
        }
        .kpi-cards {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            background-color: #f1f5f9;
            padding: 5px 8px;
            text-align: center;
            border-radius: 4px;
        }
        .kpi-title {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .kpi-value {
            font-size: 10pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 1px;
        }
        .kpi-growth {
            font-size: 6.5pt;
            font-weight: bold;
        }
        .growth-up { color: #b91c1c; } /* Naik: Merah untuk kasus penyakit */
        .growth-down { color: #15803d; } /* Turun: Hijau membaik */
        .growth-neutral { color: #64748b; }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #94a3b8;
            padding: 3px 4px;
            font-size: 7pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 6.5pt;
        }
        table.data-table th.th-sub {
            background-color: #1e293b;
            font-size: 6pt;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; font-size: 6.5pt; }
        .section-header {
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin: 10px 0 4px 0;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
        }
        .page-break { page-break-after: always; }
        .footer-sign {
            margin-top: 16px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-sign td {
            font-size: 7.5pt;
            padding: 4px;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT RESMI -->
    <div class="header-kop">
        <table>
            <tr>
                @if (!empty($profile['logo']))
                    <td style="width: 60px; vertical-align: middle; text-align: left;">
                        <img src="{{ $profile['logo'] }}" style="height: 50px; width: auto;" alt="Logo RS">
                    </td>
                @endif
                <td style="vertical-align: middle; text-align: left; padding-left: 6px;">
                    <div class="hospital-name">{{ $profile['nama_instansi'] ?? config('app.hospital_name', 'RUMKIT TK. IV 01.08.03 PADANGSIDIMPUAN') }}</div>
                    <div class="hospital-meta">
                        {{ $profile['alamat_instansi'] ?? 'Jl. Losung Batu No. 1, Padangsidimpuan' }}
                        @if (!empty($profile['kontak_instansi'])) | Telp: {{ $profile['kontak_instansi'] }} @endif
                        @if (!empty($profile['email_instansi'])) | Email: {{ $profile['email_instansi'] }} @endif
                    </div>
                </td>
                <td style="width: 140px; vertical-align: middle; text-align: right;">
                    <div style="font-size: 6.5pt; color: #64748b;">KODE REGISTRASI RS:</div>
                    <div style="font-size: 9pt; font-weight: bold; font-family: monospace;">{{ $profile['kode_rs'] ?? '1277011' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="report-title">
        <h2>MATRIKS 12 BULAN & PERTUMBUHAN MONTH-OVER-MONTH (MoM) KASUS PENYAKIT</h2>
        <p>Analisis Tren Morbiditas, Lonjakan Kasus Klinis, dan Pertumbuhan Bulanan Poliklinik & IGD Tahun {{ $year }}</p>
    </div>

    <!-- METADATA FILTER -->
    <div class="filter-meta">
        <table>
            <tr>
                <td class="filter-label">Tahun Analisis:</td>
                <td class="filter-value">{{ $year }}</td>
                <td class="filter-label">Klasifikasi Layanan:</td>
                <td class="filter-value">{{ $filterInfo['service_label'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Kasus Khusus:</td>
                <td class="filter-value">{{ $filterInfo['special_case_label'] }}</td>
                <td class="filter-label">Prioritas Diagnosa:</td>
                <td class="filter-value">{{ $filterInfo['priority_label'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Kelompok Pasien:</td>
                <td class="filter-value">{{ $filterInfo['dinas_label'] }}</td>
                <td class="filter-label">Waktu Cetak:</td>
                <td class="filter-value">{{ now()->translatedFormat('l, d F Y - H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- RINGKASAN KPI MoM EKSEKUTIF -->
    @if (!empty($momKpi))
        <table class="kpi-cards">
            <tr>
                <td class="kpi-card" style="width: 20%;">
                    <div class="kpi-title">Total Kasus ({{ $momKpi['current_month_name'] }})</div>
                    <div class="kpi-value">{{ number_format($momKpi['current_total']) }}</div>
                    <div class="kpi-growth {{ $momKpi['growth_total'] > 0 ? 'growth-up' : ($momKpi['growth_total'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        MoM: {{ $momKpi['growth_total'] > 0 ? '+' : '' }}{{ $momKpi['growth_total'] }}% vs {{ $momKpi['prev_month_name'] }}
                    </div>
                </td>
                <td class="kpi-card" style="width: 20%;">
                    <div class="kpi-title">Kasus Baru ({{ $momKpi['current_month_name'] }})</div>
                    <div class="kpi-value">{{ number_format($momKpi['current_baru']) }}</div>
                    <div class="kpi-growth {{ $momKpi['growth_baru'] > 0 ? 'growth-up' : ($momKpi['growth_baru'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        MoM: {{ $momKpi['growth_baru'] > 0 ? '+' : '' }}{{ $momKpi['growth_baru'] }}%
                    </div>
                </td>
                <td class="kpi-card" style="width: 20%;">
                    <div class="kpi-title">Poliklinik (Poli)</div>
                    <div class="kpi-value">{{ number_format($momKpi['current_poli']) }}</div>
                    <div class="kpi-growth {{ $momKpi['growth_poli'] > 0 ? 'growth-up' : ($momKpi['growth_poli'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        MoM: {{ $momKpi['growth_poli'] > 0 ? '+' : '' }}{{ $momKpi['growth_poli'] }}%
                    </div>
                </td>
                <td class="kpi-card" style="width: 20%;">
                    <div class="kpi-title">Gawat Darurat (IGD)</div>
                    <div class="kpi-value">{{ number_format($momKpi['current_igd']) }}</div>
                    <div class="kpi-growth {{ $momKpi['growth_igd'] > 0 ? 'growth-up' : ($momKpi['growth_igd'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        MoM: {{ $momKpi['growth_igd'] > 0 ? '+' : '' }}{{ $momKpi['growth_igd'] }}%
                    </div>
                </td>
                <td class="kpi-card" style="width: 20%;">
                    <div class="kpi-title">Rawat Inap (Ranap)</div>
                    <div class="kpi-value">{{ number_format($momKpi['current_ranap']) }}</div>
                    <div class="kpi-growth {{ $momKpi['growth_ranap'] > 0 ? 'growth-up' : ($momKpi['growth_ranap'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        MoM: {{ $momKpi['growth_ranap'] > 0 ? '+' : '' }}{{ $momKpi['growth_ranap'] }}%
                    </div>
                </td>
            </tr>
        </table>
    @endif

    <!-- TABEL 1: REKAPITULASI TREN MoM BULANAN RUMAH SAKIT -->
    <div class="section-header">1. Rekapitulasi Tren Bulanan & Pertumbuhan MoM Rumah Sakit (Tahun {{ $year }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">No</th>
                <th style="width: 65px;">Bulan</th>
                <th>Kasus Baru</th>
                <th>Kasus Lama</th>
                <th>Poli</th>
                <th>IGD</th>
                <th>Total Ralan</th>
                <th>Ranap</th>
                <th>Penyakit Unik</th>
                <th>Pasien Unik</th>
                <th style="background-color: #047857;">Total Kasus</th>
                <th style="background-color: #047857; width: 60px;">MoM Growth</th>
                <th style="width: 35px;">Share %</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthlyBreakdown['months'] as $m => $item)
                <tr>
                    <td class="text-center font-mono">{{ $m }}</td>
                    <td class="font-bold">{{ $item['nama_bulan'] }}</td>
                    <td class="text-right">
                        {{ number_format($item['kasus_baru']) }}
                        @if ($item['growth_baru'] !== null)
                            <div style="font-size: 5.5pt;" class="{{ $item['growth_baru'] > 0 ? 'growth-up' : ($item['growth_baru'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_baru'] > 0 ? '+' : '' }}{{ $item['growth_baru'] }}%
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($item['kasus_lama']) }}
                        @if ($item['growth_lama'] !== null)
                            <div style="font-size: 5.5pt;" class="{{ $item['growth_lama'] > 0 ? 'growth-up' : ($item['growth_lama'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_lama'] > 0 ? '+' : '' }}{{ $item['growth_lama'] }}%
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($item['poli']) }}
                        @if ($item['growth_poli'] !== null)
                            <div style="font-size: 5.5pt;" class="{{ $item['growth_poli'] > 0 ? 'growth-up' : ($item['growth_poli'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_poli'] > 0 ? '+' : '' }}{{ $item['growth_poli'] }}%
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($item['igd']) }}
                        @if ($item['growth_igd'] !== null)
                            <div style="font-size: 5.5pt;" class="{{ $item['growth_igd'] > 0 ? 'growth-up' : ($item['growth_igd'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_igd'] > 0 ? '+' : '' }}{{ $item['growth_igd'] }}%
                            </div>
                        @endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($item['ralan']) }}</td>
                    <td class="text-right">
                        {{ number_format($item['ranap']) }}
                        @if ($item['growth_ranap'] !== null)
                            <div style="font-size: 5.5pt;" class="{{ $item['growth_ranap'] > 0 ? 'growth-up' : ($item['growth_ranap'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_ranap'] > 0 ? '+' : '' }}{{ $item['growth_ranap'] }}%
                            </div>
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ number_format($item['total_penyakit_unik']) }}</td>
                    <td class="text-center font-mono">{{ number_format($item['total_pasien_unik']) }}</td>
                    <td class="text-right font-bold" style="color: #047857;">{{ number_format($item['total_kasus']) }}</td>
                    <td class="text-center font-bold font-mono">
                        @if ($item['growth_total'] !== null)
                            <span class="{{ $item['growth_total'] > 0 ? 'growth-up' : ($item['growth_total'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $item['growth_total'] > 0 ? '+' : '' }}{{ $item['growth_total'] }}%
                            </span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ $item['persentase'] }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL SETAHUN</td>
                <td class="text-right">{{ number_format($monthlyBreakdown['totals']['kasus_baru']) }}</td>
                <td class="text-right">{{ number_format($monthlyBreakdown['totals']['kasus_lama']) }}</td>
                <td class="text-right">{{ number_format($monthlyBreakdown['totals']['poli']) }}</td>
                <td class="text-right">{{ number_format($monthlyBreakdown['totals']['igd']) }}</td>
                <td class="text-right font-bold">{{ number_format($monthlyBreakdown['totals']['ralan']) }}</td>
                <td class="text-right">{{ number_format($monthlyBreakdown['totals']['ranap']) }}</td>
                <td class="text-center font-mono">{{ number_format($monthlyBreakdown['totals']['total_penyakit_unik']) }}</td>
                <td class="text-center font-mono">{{ number_format($monthlyBreakdown['totals']['total_pasien_unik']) }}</td>
                <td class="text-right font-bold" style="color: #047857;">{{ number_format($monthlyBreakdown['totals']['total_kasus']) }}</td>
                <td class="text-center font-mono">-</td>
                <td class="text-center font-mono">100%</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    <!-- KOP HALAMAN KEDUA -->
    <div class="report-title" style="margin-top: 0;">
        <h2>MATRIKS 12 BULAN PER DIAGNOSA PENYAKIT (ICD-10)</h2>
        <p>Pola Distribusi 12 Bulan dan Pertumbuhan MoM Terkini per Kasus Diagnosa</p>
    </div>

    <!-- TABEL 2: MATRIKS 12 BULAN PER PENYAKIT -->
    <div class="section-header">2. Matriks 12 Bulan & Pertumbuhan MoM per Diagnosa Penyakit (Top ICD-10)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 45px;">Kode</th>
                <th style="text-align: left;">Nama Diagnosa ICD-10</th>
                <th style="width: 25px;">Jan</th>
                <th style="width: 25px;">Feb</th>
                <th style="width: 25px;">Mar</th>
                <th style="width: 25px;">Apr</th>
                <th style="width: 25px;">Mei</th>
                <th style="width: 25px;">Jun</th>
                <th style="width: 25px;">Jul</th>
                <th style="width: 25px;">Ags</th>
                <th style="width: 25px;">Sep</th>
                <th style="width: 25px;">Okt</th>
                <th style="width: 25px;">Nov</th>
                <th style="width: 25px;">Des</th>
                <th style="width: 40px; background-color: #047857;">Total</th>
                <th style="width: 50px; background-color: #047857;">MoM %</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($diseaseMatrix['items'] as $idx => $d)
                <tr>
                    <td class="text-center font-mono">{{ $idx + 1 }}</td>
                    <td class="font-mono font-bold">{{ $d['kd_penyakit'] }}</td>
                    <td>{{ $d['nm_penyakit'] }}</td>
                    @for ($m = 1; $m <= 12; $m++)
                        <td class="text-right font-mono {{ ($d['months'][$m] ?? 0) > 0 ? '' : 'text-slate-300' }}">
                            {{ ($d['months'][$m] ?? 0) > 0 ? number_format($d['months'][$m]) : '-' }}
                        </td>
                    @endfor
                    <td class="text-right font-bold font-mono" style="color: #047857;">
                        {{ number_format($d['total_kasus']) }}
                    </td>
                    <td class="text-center font-bold font-mono">
                        <span class="{{ $d['growth_pct'] > 0 ? 'growth-up' : ($d['growth_pct'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                            {{ $d['growth_pct'] > 0 ? '+' : '' }}{{ $d['growth_pct'] }}%
                        </span>
                        @if ($d['is_surge'])
                            <span style="color: #b91c1c; font-size: 5.5pt; display: block;">[LONJAKAN]</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center" style="padding: 10px; color: #94a3b8;">
                        Tidak ada data diagnosa penyakit ditemukan pada filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN FORMAL -->
    <table class="footer-sign">
        <tr>
            <td style="width: 70%;">
                <div style="font-size: 6.5pt; color: #64748b;">
                    * Catatan Surveilans: Kolom MoM % menunjukkan laju pertumbuhan kasus terhadap bulan komparasi acuan.<br>
                    * Indikator [LONJAKAN] menandai sinyal kenaikan kasus klinis &ge; 35% dengan pertambahan minimal 15 pasien untuk kewaspadaan dini rumah sakit.
                </div>
            </td>
            <td style="width: 30%; text-align: center;">
                <div>Padangsidimpuan, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 2px;">Penanggung Jawab Rekam Medis & Surveilans</div>
                <div style="height: 40px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $profile['nama_karumkit'] ?? '(..........................................)' }}</div>
                <div style="font-size: 7pt; color: #64748b;">NRP / NIP. {{ $profile['nip_karumkit'] ?? '........................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>

