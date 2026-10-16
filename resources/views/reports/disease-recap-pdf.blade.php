<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Data Penyakit & Morbiditas Pasien</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .filter-meta {
            margin-bottom: 12px;
            padding: 8px 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .filter-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-meta td {
            padding: 2px 4px;
            font-size: 8pt;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .filter-value {
            color: #0f172a;
            width: 32%;
        }
        .summary-boxes {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-box {
            padding: 6px 10px;
            text-align: center;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
        }
        .summary-box .num {
            font-size: 11pt;
            font-weight: bold;
            color: #047857;
        }
        .summary-box .lbl {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        table.data-table tr.subheader th {
            background-color: #334155;
            font-size: 7pt;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; font-size: 8pt; }
        .footer-note {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'profile' => $profile,
        'title' => 'LAPORAN REKAPITULASI DATA PENYAKIT & MORBIDITAS PASIEN'
    ])

    <!-- Parameter Filter Laporan -->
    <div class="filter-meta">
        <table>
            <tr>
                <td class="filter-label">Periode:</td>
                <td class="filter-value">{{ $filterInfo['period_label'] }}</td>
                <td class="filter-label">Status Rawat:</td>
                <td class="filter-value">{{ $filterInfo['service_label'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Prioritas Diagnosa:</td>
                <td class="filter-value">{{ $filterInfo['priority_label'] }}</td>
                <td class="filter-label">Status Kasus:</td>
                <td class="filter-value">{{ $filterInfo['case_label'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Jenis Kelamin:</td>
                <td class="filter-value">{{ $filterInfo['gender_label'] }}</td>
                <td class="filter-label">Status Pasien:</td>
                <td class="filter-value">{{ $filterInfo['dinas_label'] ?? 'Semua Pasien' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Kategori Kasus:</td>
                <td class="filter-value" colspan="3">{{ $filterInfo['special_case_label'] ?? 'Semua Kasus' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Waktu Cetak:</td>
                <td class="filter-value" colspan="3">{{ now()->translatedFormat('d F Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Statistik Singkat -->
    <table class="summary-boxes">
        <tr>
            <td class="summary-box">
                <div class="num">{{ number_format($summary['total_kasus']) }}</div>
                <div class="lbl">Total Kasus</div>
            </td>
            <td class="summary-box">
                <div class="num">{{ number_format($summary['total_penyakit_unik']) }}</div>
                <div class="lbl">Penyakit Unik</div>
            </td>
            <td class="summary-box">
                <div class="num">{{ number_format($summary['kasus_baru']) }}</div>
                <div class="lbl">Kasus Baru</div>
            </td>
            <td class="summary-box">
                <div class="num">{{ number_format($summary['kasus_lama']) }}</div>
                <div class="lbl">Kasus Lama</div>
            </td>
            <td class="summary-box">
                <div class="num">{{ number_format($summary['primer']) }}</div>
                <div class="lbl">Diagnosa Primer</div>
            </td>
        </tr>
        <tr>
            <td class="summary-box" style="background-color: #ecfdf5;">
                <div class="num" style="color: #059669;">{{ number_format($summary['poli']) }}</div>
                <div class="lbl" style="color: #047857; font-weight: bold;">Poliklinik (Poli)</div>
            </td>
            <td class="summary-box" style="background-color: #fff1f2;">
                <div class="num" style="color: #e11d48;">{{ number_format($summary['igd']) }}</div>
                <div class="lbl" style="color: #be123c; font-weight: bold;">Gawat Darurat (IGD)</div>
            </td>
            <td class="summary-box" style="background-color: #eff6ff;">
                <div class="num" style="color: #2563eb;">{{ number_format($summary['ranap']) }}</div>
                <div class="lbl" style="color: #1d4ed8; font-weight: bold;">Rawat Inap (Ranap)</div>
            </td>
            <td class="summary-box">
                <div class="num" style="color: #2563eb;">{{ number_format($summary['pria']) }}</div>
                <div class="lbl">Laki-Laki (L)</div>
            </td>
            <td class="summary-box">
                <div class="num" style="color: #db2777;">{{ number_format($summary['wanita']) }}</div>
                <div class="lbl">Perempuan (P)</div>
            </td>
        </tr>
    </table>

    @if (!empty($isYearly) && !empty($monthlyBreakdown))
        <!-- Rekapitulasi Kasus Penyakit Per Bulan (Khusus Periode Tahunan) -->
        <div style="font-size: 8.5pt; font-weight: bold; color: #0f172a; margin-top: 10px; margin-bottom: 4px; text-transform: uppercase;">
            Rekapitulasi Kasus Penyakit Per Bulan (Tahun {{ $monthlyBreakdown['year'] }})
        </div>
        <table class="data-table" style="margin-bottom: 12px;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 14%;">Bulan</th>
                    <th colspan="2">Kasus</th>
                    <th colspan="2">Diagnosa</th>
                    <th colspan="2">Gender</th>
                    <th colspan="4">Klasifikasi Layanan</th>
                    <th rowspan="2" style="width: 8%;">Penyakit Unik</th>
                    <th rowspan="2" style="width: 8%;">Pasien Unik</th>
                    <th rowspan="2" style="width: 9%;">Total Kasus</th>
                    <th rowspan="2" style="width: 5%;">%</th>
                </tr>
                <tr class="subheader">
                    <th style="width: 5%;">Baru</th>
                    <th style="width: 5%;">Lama</th>
                    <th style="width: 5%;">Primer</th>
                    <th style="width: 5%;">Sekunder</th>
                    <th style="width: 5%;">L</th>
                    <th style="width: 5%;">P</th>
                    <th style="width: 6%;">Poli</th>
                    <th style="width: 6%;">IGD</th>
                    <th style="width: 7%; background-color: #065f46;">Total Ralan</th>
                    <th style="width: 6%;">Ranap</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlyBreakdown['months'] as $m => $item)
                    <tr>
                        <td style="text-align: left; font-weight: bold;">{{ $item['nama_bulan'] }}</td>
                        <td class="text-right">{{ number_format($item['kasus_baru']) }}</td>
                        <td class="text-right">{{ number_format($item['kasus_lama']) }}</td>
                        <td class="text-right">{{ number_format($item['primer']) }}</td>
                        <td class="text-right">{{ number_format($item['sekunder']) }}</td>
                        <td class="text-right">{{ number_format($item['pria']) }}</td>
                        <td class="text-right">{{ number_format($item['wanita']) }}</td>
                        <td class="text-right">{{ number_format($item['poli']) }}</td>
                        <td class="text-right">{{ number_format($item['igd']) }}</td>
                        <td class="text-right font-bold" style="background-color: #f0fdf4;">{{ number_format($item['ralan']) }}</td>
                        <td class="text-right">{{ number_format($item['ranap']) }}</td>
                        <td class="text-center">{{ number_format($item['total_penyakit_unik']) }}</td>
                        <td class="text-center">{{ number_format($item['total_pasien_unik']) }}</td>
                        <td class="text-right font-bold">{{ number_format($item['total_kasus']) }}</td>
                        <td class="text-right font-mono">{{ $item['persentase'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td style="text-align: left;">TOTAL SETAHUN</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['kasus_baru']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['kasus_lama']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['primer']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['sekunder']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['pria']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['wanita']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['poli']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['igd']) }}</td>
                    <td class="text-right font-bold" style="background-color: #dcfce7;">{{ number_format($monthlyBreakdown['totals']['ralan']) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown['totals']['ranap']) }}</td>
                    <td class="text-center">{{ number_format($monthlyBreakdown['totals']['total_penyakit_unik']) }}</td>
                    <td class="text-center">{{ number_format($monthlyBreakdown['totals']['total_pasien_unik']) }}</td>
                    <td class="text-right font-bold">{{ number_format($monthlyBreakdown['totals']['total_kasus']) }}</td>
                    <td class="text-right font-mono">100%</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Tabel Data Morbiditas Penyakit -->
    <div style="font-size: 8.5pt; font-weight: bold; color: #0f172a; margin-top: 10px; margin-bottom: 4px; text-transform: uppercase;">
        Daftar Morbiditas Pasien (Top {{ count($diseases) }} Terbanyak)
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 8%;">Kode ICD</th>
                <th rowspan="2" style="width: 28%;">Nama Diagnosa / Penyakit</th>
                <th colspan="2" style="width: 12%;">Kasus Baru</th>
                <th colspan="2" style="width: 12%;">Kasus Lama</th>
                <th colspan="3" style="width: 22%;">Klasifikasi Layanan</th>
                <th rowspan="2" style="width: 14%;">Total Kasus</th>
            </tr>
            <tr class="subheader">
                <th style="width: 6%;">L</th>
                <th style="width: 6%;">P</th>
                <th style="width: 6%;">L</th>
                <th style="width: 6%;">P</th>
                <th style="width: 7%;">Poli</th>
                <th style="width: 7%;">IGD</th>
                <th style="width: 8%;">Ranap</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($diseases as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center font-bold font-mono">{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-center">{{ number_format($item['baru_pria']) }}</td>
                    <td class="text-center">{{ number_format($item['baru_wanita']) }}</td>
                    <td class="text-center">{{ number_format($item['lama_pria']) }}</td>
                    <td class="text-center">{{ number_format($item['lama_wanita']) }}</td>
                    <td class="text-center font-bold" style="color: #047857;">{{ number_format($item['total_poli']) }}</td>
                    <td class="text-center font-bold" style="color: #be123c;">{{ number_format($item['total_igd']) }}</td>
                    <td class="text-center font-bold" style="color: #1d4ed8;">{{ number_format($item['total_ranap']) }}</td>
                    <td class="text-center font-bold" style="background-color: #f1f5f9;">
                        {{ number_format($item['total_kasus']) }}
                        <span style="font-size: 6.5pt; color: #64748b; display: block;">({{ $item['percent'] }}%)</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada data diagnosa penyakit pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if (!empty($diseases))
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="3" class="text-center">TOTAL KESELURUHAN ({{ count($diseases) }} PENYAKIT)</td>
                    <td class="text-center">{{ number_format(collect($diseases)->sum('baru_pria')) }}</td>
                    <td class="text-center">{{ number_format(collect($diseases)->sum('baru_wanita')) }}</td>
                    <td class="text-center">{{ number_format(collect($diseases)->sum('lama_pria')) }}</td>
                    <td class="text-center">{{ number_format(collect($diseases)->sum('lama_wanita')) }}</td>
                    <td class="text-center" style="color: #047857;">{{ number_format(collect($diseases)->sum('total_poli')) }}</td>
                    <td class="text-center" style="color: #be123c;">{{ number_format(collect($diseases)->sum('total_igd')) }}</td>
                    <td class="text-center" style="color: #1d4ed8;">{{ number_format(collect($diseases)->sum('total_ranap')) }}</td>
                    <td class="text-center" style="background-color: #cbd5e1;">
                        {{ number_format(collect($diseases)->sum('total_kasus')) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer-note">
        * Dokumen ini digenerate secara otomatis oleh SIMRS Dashboard pada {{ now()->translatedFormat('d F Y, H:i:s') }} WIB.
    </div>
</body>
</html>
