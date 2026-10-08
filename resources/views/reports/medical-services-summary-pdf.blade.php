<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ringkasan Layanan Medis RS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; margin: 0; padding: 0; }
        .meta-info { width: 100%; margin-bottom: 12px; font-size: 8.5px; border-collapse: collapse; }
        .meta-info td { padding: 2px 0; border: none; }
        .summary-box { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .summary-box td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; background-color: #f8fafc; }
        .summary-box .val { font-size: 13px; font-weight: bold; color: #0f172a; }
        .summary-box .lbl { font-size: 8px; color: #64748b; text-transform: uppercase; margin-top: 2px; }
        .section-title { background-color: #ccfbf1; color: #115e59; font-weight: bold; padding: 4px 8px; font-size: 9.5px; margin-top: 10px; border: 1px solid #99f6e4; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 10px;">
        <h3 style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase;">RINGKASAN EKSEKUTIF PELAYANAN MEDIS</h3>
        <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">Bidang Pelayanan Medik dan Keperawatan</div>
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
                <div class="val">{{ number_format($summary['total_layanan'] ?? 0) }}</div>
                <div class="lbl">Total Kunjungan Medis</div>
            </td>
            <td>
                <div class="val" style="color: #0284c7;">{{ number_format($summary['poli_kunjungan'] ?? 0) }}</div>
                <div class="lbl">Rawat Jalan (Poli)</div>
            </td>
            <td>
                <div class="val" style="color: #e11d48;">{{ number_format($summary['igd_kunjungan'] ?? 0) }}</div>
                <div class="lbl">Gawat Darurat (IGD)</div>
            </td>
            <td>
                <div class="val" style="color: #7c3aed;">{{ number_format($summary['ranap_kunjungan'] ?? 0) }}</div>
                <div class="lbl">Rawat Inap</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. DISTRIBUSI KUNJUNGAN RAWAT JALAN (POLIKLINIK TERATAS)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 50%;">Nama Poliklinik</th>
                <th style="width: 22%; text-align: right;">Total Kunjungan</th>
                <th style="width: 22%; text-align: right;">Jumlah Pasien Unik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($outpatient['top_clinics'] ?? [] as $idx => $poli)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $poli->nm_poli ?? ($poli['nm_poli'] ?? '-') }}</strong></td>
                    <td style="text-align: right;">{{ number_format($poli->total ?? ($poli['total'] ?? 0)) }}</td>
                    <td style="text-align: right;">{{ number_format($poli->pasien ?? ($poli['pasien'] ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">Belum ada data layanan rawat jalan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. PELAYANAN GAWAT DARURAT (IGD) & KELUARAN KLINIS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%; text-align: center;">Total Kunjungan</th>
                <th style="width: 20%; text-align: center;">Pulang / Selesai</th>
                <th style="width: 20%; text-align: center;">Alih Rawat Inap</th>
                <th style="width: 20%; text-align: center;">Dirujuk</th>
                <th style="width: 20%; text-align: center;">Meninggal di IGD</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ number_format($summary['igd_kunjungan'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($emergency['pulang'] ?? 0) }}</td>
                <td style="text-align: center; font-weight: bold; color: #7c3aed;">{{ number_format($emergency['dirawat'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($emergency['dirujuk'] ?? 0) }}</td>
                <td style="text-align: center; color: #dc2626;">{{ number_format($emergency['meninggal'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. DISTRIBUSI RAWAT INAP BERDASARKAN RUANG / BANGSAL</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 50%;">Nama Bangsal / Ruangan</th>
                <th style="width: 22%; text-align: right;">Pasien Masuk (Admisi)</th>
                <th style="width: 22%; text-align: right;">Pasien Unik Dirawat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inpatient['top_wards'] ?? [] as $idx => $ward)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $ward->nm_bangsal ?? ($ward['nm_bangsal'] ?? '-') }}</strong></td>
                    <td style="text-align: right;">{{ number_format($ward->total ?? ($ward['total'] ?? 0)) }}</td>
                    <td style="text-align: right;">{{ number_format($ward->pasien ?? ($ward['pasien'] ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">Belum ada data rawat inap.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">4. SEBARAN KUNJUNGAN MENURUT PENJAMIN / CARA BAYAR TERBANYAK</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 44%;">Jenis Penjamin / Asuransi</th>
                <th style="width: 25%; text-align: right;">Total Kunjungan</th>
                <th style="width: 25%; text-align: right;">Proporsi (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totLayanan = $summary['total_layanan'] ?? 0;
            @endphp
            @forelse ($caraBayar ?? [] as $idx => $cb)
                @php
                    $cbNama = is_object($cb) ? $cb->cara_bayar : ($cb['cara_bayar'] ?? $cb['nama'] ?? '-');
                    $cbTotal = is_object($cb) ? $cb->total : ($cb['total'] ?? $cb['jumlah'] ?? 0);
                    $proporsi = $totLayanan > 0 ? round(($cbTotal / $totLayanan) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $cbNama }}</strong></td>
                    <td style="text-align: right;">{{ number_format($cbTotal) }}</td>
                    <td style="text-align: right;">{{ $proporsi }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">Belum ada data cara bayar.</td>
                </tr>
            @endforelse
    <div class="section-title">5. REKAPITULASI PELAYANAN PASIEN DINAS (TNI / POLRI)</div>
    <table class="data-table" style="margin-bottom: 8px;">
        <thead>
            <tr>
                <th style="width: 28%;">Entitas Pasien Dinas</th>
                <th style="width: 18%; text-align: center;">Rawat Jalan (Poli)</th>
                <th style="width: 18%; text-align: center;">Gawat Darurat (IGD)</th>
                <th style="width: 18%; text-align: center;">Rawat Inap (Ranap)</th>
                <th style="width: 18%; text-align: right;">Total Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Pasien Dinas TNI</strong></td>
                <td style="text-align: center;">{{ number_format($dinas['tni_poli'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($dinas['tni_igd'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($dinas['tni_ranap'] ?? 0) }}</td>
                <td style="text-align: right; font-weight: bold; color: #059669;">{{ number_format($dinas['tni'] ?? 0) }}</td>
            </tr>
            <tr>
                <td><strong>Pasien Dinas POLRI</strong></td>
                <td style="text-align: center;">{{ number_format($dinas['polri_poli'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($dinas['polri_igd'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($dinas['polri_ranap'] ?? 0) }}</td>
                <td style="text-align: right; font-weight: bold; color: #0284c7;">{{ number_format($dinas['polri'] ?? 0) }}</td>
            </tr>
            <tr style="background-color: #f3e8ff;">
                <td><strong>TOTAL PASIEN DINAS</strong></td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($dinas['poli'] ?? 0) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($dinas['igd'] ?? 0) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($dinas['ranap'] ?? 0) }}</td>
                <td style="text-align: right; font-weight: bold; color: #7c3aed;">{{ number_format($dinas['total'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 32%;">Kategori Personel</th>
                <th style="width: 11%; text-align: center;">Poli</th>
                <th style="width: 11%; text-align: center;">IGD</th>
                <th style="width: 11%; text-align: center;">Ranap</th>
                <th style="width: 11%; text-align: center;">TNI</th>
                <th style="width: 11%; text-align: center;">POLRI</th>
                <th style="width: 13%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dinas['categories'] ?? [] as $cat)
                <tr>
                    <td><strong>{{ is_object($cat) ? $cat->kategori : ($cat['kategori'] ?? '-') }}</strong></td>
                    <td style="text-align: center;">{{ number_format(is_object($cat) ? $cat->poli : ($cat['poli'] ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format(is_object($cat) ? $cat->igd : ($cat['igd'] ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format(is_object($cat) ? $cat->ranap : ($cat['ranap'] ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format(is_object($cat) ? $cat->tni : ($cat['tni'] ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format(is_object($cat) ? $cat->polri : ($cat['polri'] ?? 0)) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format(is_object($cat) ? $cat->total : ($cat['total'] ?? 0)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b;">Belum ada data pasien dinas pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
