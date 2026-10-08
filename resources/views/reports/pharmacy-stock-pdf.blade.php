<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat Farmasi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8.5px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 15px; color: #0f172a; text-transform: uppercase; }
        .header h3 { margin: 0 0 4px 0; font-size: 11px; color: #475569; font-weight: normal; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 9px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        .summary-box { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background-color: #f8fafc; }
        .summary-box .val { font-size: 14px; font-weight: bold; color: #0f172a; }
        .summary-box .lbl { font-size: 8.5px; color: #64748b; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 4px 5px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 25px; width: 100%; border-collapse: collapse; }
        .footer td { border: none; font-size: 9px; }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN KETERSEDIAAN DAN STOK OBAT</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Instalasi Farmasi Rumah Sakit</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Status Filter</strong></td>
            <td style="width: 35%;">: {{ ucfirst($statusStok) }}</td>
            <td style="width: 20%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 30%;">: {{ $printedAt }}</td>
        </tr>
        <tr>
            <td><strong>Kategori Depo</strong></td>
            <td>: {{ ucfirst($depo) }}</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="val">{{ number_format($summary['total_items'] ?? ($summary['total_item'] ?? 0)) }}</div>
                <div class="lbl">Total Item Obat</div>
            </td>
            <td>
                <div class="val" style="color: #059669;">{{ number_format($summary['total_aman'] ?? ($summary['aman'] ?? 0)) }}</div>
                <div class="lbl">Stok Aman</div>
            </td>
            <td>
                <div class="val" style="color: #ea580c;">{{ number_format($summary['total_menipis'] ?? ($summary['menipis'] ?? 0)) }}</div>
                <div class="lbl">Stok Menipis</div>
            </td>
            <td>
                <div class="val" style="color: #dc2626;">{{ number_format($summary['total_habis'] ?? ($summary['habis'] ?? 0)) }}</div>
                <div class="lbl">Stok Habis (Kosong)</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 33%;">Nama Obat / Alkes</th>
                <th style="width: 12%;">Satuan</th>
                <th style="width: 13%; text-align: right;">Stok Fisik</th>
                <th style="width: 13%; text-align: right;">Stok Min</th>
                <th style="width: 12%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                @php
                    $stok = $item->total_stok ?? ($item->stok_fisik ?? ($item->stok ?? 0));
                    $min = $item->stokminimal ?? 0;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_brng }}</td>
                    <td><strong>{{ $item->nama_brng }}</strong></td>
                    <td>{{ $item->kode_sat ?? ($item->satuan ?? '-') }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($stok) }}</td>
                    <td style="text-align: right;">{{ number_format($min) }}</td>
                    <td style="text-align: center;">
                        @if ($stok <= 0)
                            <span style="color: #dc2626; font-weight: bold;">HABIS</span>
                        @elseif ($stok <= $min)
                            <span style="color: #ea580c; font-weight: bold;">MENIPIS</span>
                        @else
                            <span style="color: #059669;">AMAN</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b;">Tidak ada data stok obat sesuai kriteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
