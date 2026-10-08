<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kelengkapan Rekam Medis (KLPCM) - {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #7c3aed; padding-bottom: 8px; }
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
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN KELENGKAPAN PENGISIAN CATATAN MEDIS (KLPCM)</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Unit Rekam Medis & Manajemen Informasi Kesehatan (RMIK)</div>
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
            <td>: Permenkes RI No. 24 Tahun 2022</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ number_format($summary['total_berkas'] ?? 0) }}</div>
                <div class="lbl">Total Pasien Pulang</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ number_format($summary['berkas_lengkap'] ?? 0) }}</div>
                <div class="lbl">Resume Terisi Lengkap</div>
            </td>
            <td>
                <div class="val" style="color: #dc2626;">{{ number_format($summary['berkas_klpcm'] ?? 0) }}</div>
                <div class="lbl">Tidak Lengkap (KLPCM)</div>
            </td>
            <td>
                <div class="val" style="color: #7c3aed;">{{ $summary['angka_klpcm'] ?? 0 }}%</div>
                <div class="lbl">Angka KLPCM</div>
            </td>
        </tr>
    </table>

    <h4 style="margin: 12px 0 4px 0; font-size: 10px; color: #0f172a;">Kepatuhan Pengisian Berkas per Dokter Penanggung Jawab Pelayanan (DPJP)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 44%;">Nama Dokter (DPJP)</th>
                <th style="width: 16%; text-align: right;">Total Pasien Pulang</th>
                <th style="width: 16%; text-align: right;">Resume Terisi Lengkap</th>
                <th style="width: 18%; text-align: right;">Kepatuhan (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($doctorCompliance as $idx => $doc)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $doc['nm_dokter'] ?? '-' }}</strong></td>
                    <td style="text-align: right;">{{ number_format($doc['total_berkas'] ?? 0) }}</td>
                    <td style="text-align: right;">{{ number_format($doc['berkas_lengkap'] ?? 0) }}</td>
                    <td style="text-align: right; font-weight: bold; color: {{ ($doc['persen_lengkap'] ?? 0) >= 80 ? '#059669' : '#dc2626' }};">
                        {{ $doc['persen_lengkap'] ?? 0 }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;">Belum ada data kepatuhan resume medis DPJP pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
