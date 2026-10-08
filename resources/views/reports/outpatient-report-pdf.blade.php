<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pasien Rawat Jalan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #00923f;
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
            font-weight: normal;
            color: #475569;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #00923f;
            margin-top: 14px;
            margin-bottom: 4px;
            text-transform: uppercase;
            border-left: 3px solid #00923f;
            padding-left: 6px;
        }
        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5px;
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
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-pria { background-color: #dbeafe; color: #1e40af; }
        .badge-wanita { background-color: #fce7f3; color: #9d174d; }
        .badge-bpjs { background-color: #d1fae5; color: #065f46; }
        .badge-umum { background-color: #e0e7ff; color: #3730a3; }
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
        @page {
            margin: 1cm 0.8cm 1.5cm 0.8cm;
        }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 12px;">
        <h3 style="margin: 0; font-size: 13px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN REKAPITULASI & DATA PASIEN RAWAT JALAN</h3>
    </div>

    <div class="meta-info">
        <div class="meta-row">
            <div class="meta-cell" style="width: 50%;">
                <strong>Periode Registrasi:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </div>
            <div class="meta-cell" style="width: 50%; text-align: right;">
                <strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">
                <strong>Total Kunjungan:</strong> {{ number_format($summary['total_pasien'], 0, ',', '.') }} Pasien 
                (Laki-laki: {{ number_format($summary['total_pria'], 0, ',', '.') }}, Perempuan: {{ number_format($summary['total_wanita'], 0, ',', '.') }})
            </div>
            <div class="meta-cell" style="text-align: right;">
                <strong>Kategori:</strong> Baru: {{ number_format($summary['total_baru'], 0, ',', '.') }} | Lama: {{ number_format($summary['total_lama'], 0, ',', '.') }} | TNI: {{ number_format($summary['total_tni'] ?? 0, 0, ',', '.') }} | POLRI: {{ number_format($summary['total_polri'] ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Ringkasan Rekap Poliklinik -->
    <div class="section-title">1. Rekapitulasi Kunjungan per Poliklinik / Unit</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th>Poliklinik / Unit</th>
                <th style="width: 45px; text-align: center;">Total</th>
                <th style="width: 35px; text-align: center;">%</th>
                <th style="width: 40px; text-align: center;">Laki-laki</th>
                <th style="width: 45px; text-align: center;">Perempuan</th>
                <th style="width: 35px; text-align: center;">Baru</th>
                <th style="width: 35px; text-align: center;">Lama</th>
                <th style="width: 40px; text-align: center;">BPJS</th>
                <th style="width: 35px; text-align: center;">Umum</th>
                <th style="width: 32px; text-align: center;">TNI</th>
                <th style="width: 32px; text-align: center;">POLRI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($polyBreakdown as $index => $poly)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $poly['nm_poli'] }}</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($poly['total'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $poly['percent'] }}%</td>
                    <td style="text-align: center;">{{ number_format($poly['pria'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['wanita'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['baru'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['lama'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['bpjs'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['umum'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['tni'] ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($poly['polri'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; color: #94a3b8;">Tidak ada data poliklinik ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Ringkasan Rekap Jenis Bayar -->
    <div class="section-title">2. Rekapitulasi Berdasarkan Jenis Bayar / Penjamin</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th>Jenis Bayar / Penjamin</th>
                <th style="width: 60px; text-align: center;">Total Kunjungan</th>
                <th style="width: 45px; text-align: center;">Persentase</th>
                <th style="width: 50px; text-align: center;">Laki-laki</th>
                <th style="width: 50px; text-align: center;">Perempuan</th>
                <th style="width: 45px; text-align: center;">Pasien Baru</th>
                <th style="width: 45px; text-align: center;">Pasien Lama</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payTypeBreakdown as $index => $pay)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $pay['png_jawab'] }}</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($pay['total'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $pay['percent'] }}%</td>
                    <td style="text-align: center;">{{ number_format($pay['pria'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($pay['wanita'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($pay['baru'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($pay['lama'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8;">Tidak ada data jenis bayar ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rekapitulasi Demografi & Kelompok Umur -->
    <div class="section-title">3. Rekapitulasi Demografi & Kelompok Umur (SIRS Standar)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 50px;">Kode</th>
                <th>Kelompok Umur</th>
                <th style="width: 60px; text-align: center;">Total Pasien</th>
                <th style="width: 55px; text-align: center;">Distribusi (%)</th>
                <th style="width: 55px; text-align: center;">Laki-laki</th>
                <th style="width: 55px; text-align: center;">Perempuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ageGroupBreakdown as $index => $age)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $age['kode'] }}</td>
                    <td><strong>{{ $age['nama'] }}</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($age['total'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $age['percent'] }}%</td>
                    <td style="text-align: center;">{{ number_format($age['pria'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($age['wanita'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8;">Tidak ada data demografi kelompok umur ditemukan.</td>
                </tr>
            @endforelse
    <!-- Rekapitulasi Pasien Dinas (TNI / POLRI) -->
    @if (!empty($dinasBreakdown))
        <div class="section-title">4. Rekapitulasi Pasien Dinas Rawat Jalan (TNI / POLRI)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th>Poliklinik / Unit</th>
                    <th style="width: 55px; text-align: center;">Total Dinas</th>
                    <th style="width: 45px; text-align: center;">Proporsi</th>
                    <th style="width: 50px; text-align: center;">TNI</th>
                    <th style="width: 50px; text-align: center;">POLRI</th>
                    <th style="width: 45px; text-align: center;">Laki-laki</th>
                    <th style="width: 45px; text-align: center;">Perempuan</th>
                    <th style="width: 40px; text-align: center;">Baru</th>
                    <th style="width: 40px; text-align: center;">Lama</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dinasBreakdown['polyclinics'] ?? [] as $index => $dp)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $dp['nm_poli'] }}</strong> ({{ $dp['kd_poli'] }})</td>
                        <td style="text-align: center; font-weight: bold; color: #7c3aed;">{{ number_format($dp['total'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $dp['percent'] }}%</td>
                        <td style="text-align: center;">{{ number_format($dp['tni'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($dp['polri'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($dp['pria'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($dp['wanita'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($dp['baru'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ number_format($dp['lama'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: #94a3b8;">Tidak ada data pasien dinas ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
