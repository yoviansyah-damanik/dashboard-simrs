<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pertumbuhan Pasien Rawat Jalan (MoM Growth)</title>
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
        .growth-up { color: #047857; } /* Hijau untuk peningkatan volume kunjungan pasien */
        .growth-down { color: #b91c1c; } /* Merah untuk penurunan volume */
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
                @if (!empty($profil['logo']))
                    <td style="width: 60px; vertical-align: middle; text-align: left;">
                        <img src="{{ $profil['logo'] }}" style="height: 50px; width: auto;" alt="Logo RS">
                    </td>
                @endif
                <td style="vertical-align: middle; text-align: left; padding-left: 6px;">
                    <div class="hospital-name">{{ $profil['nama_instansi'] ?? config('app.hospital_name', 'RUMAH SAKIT') }}</div>
                    <div class="hospital-meta">
                        {{ $profil['alamat_instansi'] ?? 'Jl. Merdeka No. 1' }}
                        @if (!empty($profil['kontak_instansi'])) | Telp: {{ $profil['kontak_instansi'] }} @endif
                        @if (!empty($profil['email_instansi'])) | Email: {{ $profil['email_instansi'] }} @endif
                    </div>
                </td>
                <td style="width: 140px; vertical-align: middle; text-align: right;">
                    <div style="font-size: 6.5pt; color: #64748b;">KODE REGISTRASI RS:</div>
                    <div style="font-size: 9pt; font-weight: bold; font-family: monospace;">{{ $profil['kode_rs'] ?? 'SIMRS-KHANZA' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL LAPORAN -->
    @php
        $isDaily = ($momData['period_type'] === 'daily');
    @endphp
    <div class="report-title">
        <h2>LAPORAN ANALISIS PERTUMBUHAN RAWAT JALAN (MoM &amp; DoD GROWTH)</h2>
        <p>
            @if ($isDaily)
                Analisis Kunjungan Harian Pasien Rawat Jalan &amp; Pertumbuhan Month-over-Month Periode {{ $momData['period_label'] }}
            @else
                Analisis Kunjungan 12 Bulan Pasien Rawat Jalan &amp; Pertumbuhan Month-over-Month Tahun {{ $momData['year'] }}
            @endif
        </p>
    </div>

    <!-- METADATA FILTER -->
    <div class="filter-meta">
        <table>
            <tr>
                <td class="filter-label">Periode Analisis:</td>
                <td class="filter-value">{{ $momData['period_label'] }}</td>
                <td class="filter-label">Poliklinik / Unit:</td>
                <td class="filter-value">{{ $poly !== 'semua' ? $poly : 'Semua Poliklinik & IGD' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Penjamin:</td>
                <td class="filter-value">{{ $payType !== 'semua' ? $payType : 'Semua Penjamin' }}</td>
                <td class="filter-label">Dokter DPJP:</td>
                <td class="filter-value">{{ $doctor !== 'semua' ? $doctor : 'Semua Dokter' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Status Pasien:</td>
                <td class="filter-value">{{ $dinasFilter !== 'semua' ? $dinasFilter : 'Semua Kelompok Pasien' }}</td>
                <td class="filter-label">Waktu Cetak:</td>
                <td class="filter-value">{{ now()->translatedFormat('l, d F Y - H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- KPI CARDS EKSEKUTIF -->
    <table class="kpi-cards">
        <tr>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">Total Pasien Ralan</div>
                <div class="kpi-value">{{ number_format($momData['kpi']['total_current']) }}</div>
                <div class="kpi-growth {{ $momData['kpi']['growth_total'] > 0 ? 'growth-up' : ($momData['kpi']['growth_total'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                    MoM: {{ $momData['kpi']['growth_total'] > 0 ? '+' : '' }}{{ $momData['kpi']['growth_total'] }}%
                </div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">Poliklinik (Poli)</div>
                <div class="kpi-value" style="color: #047857;">{{ number_format($momData['kpi']['poli_current']) }}</div>
                <div class="kpi-growth {{ $momData['kpi']['growth_poli'] > 0 ? 'growth-up' : ($momData['kpi']['growth_poli'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                    MoM: {{ $momData['kpi']['growth_poli'] > 0 ? '+' : '' }}{{ $momData['kpi']['growth_poli'] }}%
                </div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">Gawat Darurat (IGD)</div>
                <div class="kpi-value" style="color: #b91c1c;">{{ number_format($momData['kpi']['igd_current']) }}</div>
                <div class="kpi-growth {{ $momData['kpi']['growth_igd'] > 0 ? 'growth-up' : ($momData['kpi']['growth_igd'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                    MoM: {{ $momData['kpi']['growth_igd'] > 0 ? '+' : '' }}{{ $momData['kpi']['growth_igd'] }}%
                </div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">{{ $isDaily ? 'Rata-rata Pasien / Hari' : 'Rata-rata Pasien / Bulan' }}</div>
                <div class="kpi-value">{{ number_format($isDaily ? $momData['kpi']['avg_per_day'] : $momData['kpi']['avg_per_month'], 1) }}</div>
                <div style="font-size: 6pt; color: #64748b;">
                    Baru: {{ number_format($momData['kpi']['baru_current']) }} | Lama: {{ number_format($momData['kpi']['lama_current']) }}
                </div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-title">Kunjungan Puncak (Peak)</div>
                <div class="kpi-value" style="color: #0369a1;">{{ number_format($momData['kpi']['peak_point']['total']) }}</div>
                <div style="font-size: 6pt; color: #64748b;">
                    {{ $isDaily ? ($momData['kpi']['peak_point']['date'] ? \Carbon\Carbon::parse($momData['kpi']['peak_point']['date'])->translatedFormat('d M (D)') : '-') : ($momData['kpi']['peak_point']['name'] ?? '-') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- TABEL 1: TIMELINE TREN KUNJUNGAN RS -->
    <div class="section-header">
        1. {{ $isDaily ? 'Rekapitulasi Kunjungan Harian & Laju Pertumbuhan Rumah Sakit' : 'Rekapitulasi Kunjungan 12 Bulan & Laju Pertumbuhan Rumah Sakit' }}
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">No</th>
                <th style="width: {{ $isDaily ? '75px' : '85px' }};">{{ $isDaily ? 'Tanggal & Hari' : 'Bulan' }}</th>
                <th style="background-color: #047857;">Poliklinik (Poli)</th>
                <th style="background-color: #991b1b;">Gawat Darurat (IGD)</th>
                <th>Pasien Baru</th>
                <th>Pasien Lama</th>
                <th>Laki-laki</th>
                <th>Perempuan</th>
                <th>BPJS</th>
                <th style="background-color: #047857;">Total Pasien</th>
                <th style="background-color: #047857; width: 65px;">{{ $isDaily ? 'DoD Growth' : 'MoM Growth' }}</th>
                <th style="width: 65px;">{{ $isDaily ? 'MoM Date %' : 'Share %' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($momData['timeline'] as $item)
                <tr>
                    <td class="text-center font-mono">{{ $isDaily ? $item['day'] : $item['month'] }}</td>
                    <td class="font-bold">
                        @if ($isDaily)
                            {{ $item['short_date'] }} ({{ substr($item['day_name'], 0, 3) }})
                        @else
                            {{ $item['name'] }}
                        @endif
                    </td>
                    <td class="text-right font-bold" style="color: #047857;">{{ number_format($item['poli']) }}</td>
                    <td class="text-right font-bold" style="color: #b91c1c;">{{ number_format($item['igd']) }}</td>
                    <td class="text-right">{{ number_format($item['baru']) }}</td>
                    <td class="text-right">{{ number_format($item['lama']) }}</td>
                    <td class="text-right">{{ number_format($item['pria']) }}</td>
                    <td class="text-right">{{ number_format($item['wanita']) }}</td>
                    <td class="text-right">{{ number_format($item['bpjs']) }}</td>
                    <td class="text-right font-bold" style="color: #047857;">{{ number_format($item['total']) }}</td>
                    <td class="text-center font-bold font-mono">
                        @php $growth = $isDaily ? $item['dod_growth'] : $item['mom_growth']; @endphp
                        @if ($growth !== null)
                            <span class="{{ $growth > 0 ? 'growth-up' : ($growth < 0 ? 'growth-down' : 'growth-neutral') }}">
                                {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
                            </span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="text-center font-mono">
                        @if ($isDaily)
                            @if ($item['mom_date_growth'] !== null)
                                <span class="{{ $item['mom_date_growth'] > 0 ? 'growth-up' : ($item['mom_date_growth'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                                    {{ $item['mom_date_growth'] > 0 ? '+' : '' }}{{ $item['mom_date_growth'] }}%
                                </span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        @else
                            {{ $item['share_pct'] }}%
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-right font-bold" style="color: #047857;">{{ number_format($momData['kpi']['poli_current']) }}</td>
                <td class="text-right font-bold" style="color: #b91c1c;">{{ number_format($momData['kpi']['igd_current']) }}</td>
                <td class="text-right">{{ number_format($momData['kpi']['baru_current']) }}</td>
                <td class="text-right">{{ number_format($momData['kpi']['lama_current']) }}</td>
                <td class="text-right">{{ number_format($momData['kpi']['pria_current']) }}</td>
                <td class="text-right">{{ number_format($momData['kpi']['wanita_current']) }}</td>
                <td class="text-right">{{ number_format($momData['kpi']['bpjs_current']) }}</td>
                <td class="text-right font-bold" style="color: #047857;">{{ number_format($momData['kpi']['total_current']) }}</td>
                <td class="text-center font-mono">
                    <span class="{{ $momData['kpi']['growth_total'] > 0 ? 'growth-up' : ($momData['kpi']['growth_total'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                        {{ $momData['kpi']['growth_total'] > 0 ? '+' : '' }}{{ $momData['kpi']['growth_total'] }}%
                    </span>
                </td>
                <td class="text-center font-mono">100%</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 2: MATRIKS POLIKLINIK & IGD -->
    <div class="report-title" style="margin-top: 0;">
        <h2>MATRIKS PERTUMBUHAN KUNJUNGAN PER POLIKLINIK &amp; IGD</h2>
        <p>Pola Distribusi dan Volume Kunjungan Pasien Rawat Jalan per Poliklinik &amp; IGD</p>
    </div>

    <div class="section-header">2. Matriks Poliklinik &amp; IGD (Volume &amp; Pertumbuhan MoM)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 40px;">Kode</th>
                <th style="text-align: left;">Nama Poliklinik / Ruangan</th>
                <th style="width: 35px;">Layanan</th>
                @foreach ($momData['polyclinic_matrix']['columns'] as $c)
                    <th style="width: {{ $isDaily ? '18px' : '28px' }};">
                        {{ $isDaily ? $c : ($momData['column_names'][$c - 1] ?? $c) }}
                    </th>
                @endforeach
                <th style="width: 45px; background-color: #047857;">Total</th>
                <th style="width: 45px; background-color: #047857;">MoM %</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($momData['polyclinic_matrix']['items'] as $idx => $pl)
                <tr>
                    <td class="text-center font-mono">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono font-bold">{{ $pl['kd_poli'] }}</td>
                    <td class="font-bold">{{ $pl['nm_poli'] }}</td>
                    <td class="text-center">
                        <span style="color: {{ $pl['jenis_layanan'] === 'IGD' ? '#b91c1c' : '#047857' }}; font-weight: bold;">
                            {{ $pl['jenis_layanan'] }}
                        </span>
                    </td>
                    @foreach ($momData['polyclinic_matrix']['columns'] as $c)
                        @php $val = $pl['points'][$c] ?? 0; @endphp
                        <td class="text-right font-mono {{ $val > 0 ? '' : 'text-slate-300' }}">
                            {{ $val > 0 ? number_format($val) : '-' }}
                        </td>
                    @endforeach
                    <td class="text-right font-bold font-mono" style="color: #047857;">
                        {{ number_format($pl['total']) }}
                    </td>
                    <td class="text-center font-bold font-mono">
                        <span class="{{ $pl['growth_pct'] > 0 ? 'growth-up' : ($pl['growth_pct'] < 0 ? 'growth-down' : 'growth-neutral') }}">
                            {{ $pl['growth_pct'] > 0 ? '+' : '' }}{{ $pl['growth_pct'] }}%
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + count($momData['polyclinic_matrix']['columns']) }}" class="text-center" style="padding: 10px; color: #94a3b8;">
                        Tidak ada data poliklinik ditemukan.
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
                    * Data rawat jalan diekstrak secara otomatis dari SIMRS Khanza dengan status periksa valid (bukan 'Batal' dan bukan 'Belum').<br>
                    * Pemisahan eksplisit antara Pelayanan Rawat Jalan Poliklinik (Poli) dan Pelayanan Kegawatdaruratan (IGD) sesuai Pedoman Standar Pelaporan Rumah Sakit.
                </div>
            </td>
            <td style="width: 30%; text-align: center;">
                <div>Padangsidimpuan, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 2px;">Penanggung Jawab Pelayanan Rawat Jalan</div>
                <div style="height: 40px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $profil['nama_karumkit'] ?? '(..........................................)' }}</div>
                <div style="font-size: 7pt; color: #64748b;">NRP / NIP. {{ $profil['nip_karumkit'] ?? '........................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>

