<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelayanan IGD</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #e11d48; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        .summary-box { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background-color: #f8fafc; }
        .summary-box .val { font-size: 14px; font-weight: bold; color: #0f172a; }
        .summary-box .lbl { font-size: 8.5px; color: #64748b; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; font-size: 8.5px; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 25px; width: 100%; border-collapse: collapse; }
        .footer td { border: none; font-size: 9px; }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN KINERJA PELAYANAN GAWAT DARURAT</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Gawat Darurat (IGD)</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Periode</strong></td>
            <td style="width: 35%;">: {{ $startDate }} s/d {{ $endDate }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Sumber Data</strong></td>
            <td>: Database SIMRS Terpadu</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ number_format($summary['total_pasien'] ?? 0) }}</div>
                <div class="lbl">Total Pasien IGD</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ number_format($summary['total_tni'] ?? 0) }}</div>
                <div class="lbl">Pasien Dinas TNI</div>
            </td>
            <td>
                <div class="val" style="color: #0284c7;">{{ number_format($summary['total_polri'] ?? 0) }}</div>
                <div class="lbl">Pasien Dinas POLRI</div>
            </td>
            <td>
                <div class="val" style="color: #7c3aed;">{{ number_format($summary['total_ranap'] ?? ($summary['dirawat'] ?? 0)) }}</div>
                <div class="lbl">Alih Rawat Inap</div>
            </td>
        </tr>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">1. Distribusi Status Pulang Pasien IGD</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="width: 50%;">Status Pulang / Keluar</th>
                <th style="width: 20%; text-align: right;">Jumlah Pasien</th>
                <th style="width: 20%; text-align: right;">Proporsi (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statusBreakdown['by_status'] ?? [] as $stName => $stCount)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $stName }}</strong></td>
                    <td style="text-align: right;">{{ number_format($stCount) }}</td>
                    <td style="text-align: right;">{{ ($summary['total_pasien'] ?? 0) > 0 ? round(($stCount / $summary['total_pasien']) * 100, 1) : 0 }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">Belum ada data status pulang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">2. Pelayanan per Dokter Jaga IGD</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="width: 60%;">Nama Dokter</th>
                <th style="width: 30%; text-align: right;">Jumlah Pelayanan</th>
            </tr>
        </thead>
        <tbody>
            @forelse (array_slice($doctorBreakdown ?? [], 0, 10) as $doc)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $doc['nm_dokter'] ?? ($doc['dokter'] ?? '-') }}</strong></td>
                    <td style="text-align: right;">{{ number_format($doc['total'] ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b;">Belum ada data pelayanan dokter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if (!empty($dinasBreakdown['categories']))
        <h4 style="margin: 14px 0 4px 0; font-size: 10px; color: #0f172a;">3. Rekapitulasi Pasien Dinas (TNI / POLRI) IGD</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%; text-align: center;">No</th>
                    <th style="width: 40%;">Kategori Personel</th>
                    <th style="width: 14%; text-align: right;">TNI</th>
                    <th style="width: 14%; text-align: right;">POLRI</th>
                    <th style="width: 12%; text-align: right;">Ranap</th>
                    <th style="width: 12%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dinasBreakdown['categories'] as $cat)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $cat['kategori'] }}</strong></td>
                        <td style="text-align: right;">{{ number_format($cat['tni']) }}</td>
                        <td style="text-align: right;">{{ number_format($cat['polri']) }}</td>
                        <td style="text-align: right;">{{ number_format($cat['ranap']) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($cat['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($diagnosisBreakdown))
        <h4 style="margin: 14px 0 4px 0; font-size: 10px; color: #0f172a;">4. Top 15 Diagnosa Penyakit Terbanyak (ICD-10) IGD</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%; text-align: center;">Rank</th>
                    <th style="width: 12%; text-align: center;">Kode ICD</th>
                    <th style="width: 44%;">Nama Penyakit</th>
                    <th style="width: 12%; text-align: right;">Primer</th>
                    <th style="width: 12%; text-align: right;">Sekunder</th>
                    <th style="width: 14%; text-align: right;">Total Kasus</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($diagnosisBreakdown, 0, 15) as $diag)
                    <tr>
                        <td style="text-align: center;">{{ $diag['rank'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #e11d48;">{{ $diag['kd_penyakit'] }}</td>
                        <td>{{ $diag['nm_penyakit'] }}</td>
                        <td style="text-align: right;">{{ number_format($diag['primer']) }}</td>
                        <td style="text-align: right;">{{ number_format($diag['sekunder']) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($diag['total_kasus']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
