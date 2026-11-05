<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rincian Pasien Diagnosa {{ $diseaseCode }}</title>
    <style>
        @page {
            margin: 12mm 12mm 12mm 12mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .filter-meta {
            margin-bottom: 10px;
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
            font-size: 7.5pt;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 15%;
        }
        .filter-value {
            color: #0f172a;
            width: 35%;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #94a3b8;
            padding: 4px 5px;
            font-size: 7.5pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7pt;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; font-size: 7.5pt; }
        .badge-sensor {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .badge-nosensor {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .footer-note {
            margin-top: 12px;
            font-size: 7pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'profile' => $profile,
        'title' => 'RINCIAN DATA KUNJUNGAN PASIEN KASUS DIAGNOSA ' . $diseaseCode
    ])

    <!-- Parameter Filter Laporan -->
    <div class="filter-meta">
        <table>
            <tr>
                <td class="filter-label">Kode & Diagnosa:</td>
                <td class="filter-value" colspan="3"><strong>{{ $diseaseCode }}</strong> &mdash; {{ $diseaseName }}</td>
            </tr>
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
                <td class="filter-label">Mode Sensor Privasi:</td>
                <td class="filter-value" colspan="3">
                    @if ($isMasked)
                        <span class="badge-sensor">AKTIF (No. RM disensor 3 digit belakang, Nama disensor bagian tengah)</span>
                    @else
                        <span class="badge-nosensor">NONAKTIF (Data Identitas Pasien Ditampilkan Lengkap)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="filter-label">Waktu Cetak:</td>
                <td class="filter-value" colspan="3">{{ now()->translatedFormat('d F Y H:i') }} WIB &bull; Petugas: {{ auth()->user()->name ?? 'System' }}</td>
            </tr>
        </table>
    </div>

    <div style="font-size: 8pt; font-weight: bold; color: #0f172a; margin-top: 6px; margin-bottom: 3px; text-transform: uppercase;">
        Daftar Pasien Diagnosa {{ $diseaseCode }} (Total {{ count($patients) }} Data)
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">No. RM</th>
                <th style="width: 20%;">Nama Pasien</th>
                <th style="width: 4%;">L/P</th>
                <th style="width: 5%;">Umur</th>
                <th style="width: 8%;">Tgl Kunjungan</th>
                <th style="width: 8%;">Layanan</th>
                <th style="width: 6%;">Prioritas</th>
                <th style="width: 6%;">Kasus</th>
                <th style="width: 8%;">Status Dinas</th>
                <th style="width: 14%;">Dokter DPJP</th>
                <th style="width: 10%;">Penjamin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($patients as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono font-bold">
                        {{ $isMasked ? $p['no_rkm_medis_masked'] : $p['no_rkm_medis'] }}
                    </td>
                    <td class="font-bold">
                        {{ $isMasked ? $p['nama_masked'] : $p['nama'] }}
                    </td>
                    <td class="text-center font-bold" style="color: {{ $p['jk'] === 'L' ? '#2563eb' : '#db2777' }};">
                        {{ $p['jk'] }}
                    </td>
                    <td class="text-center">{{ $p['umur'] !== '-' ? $p['umur'] . ' th' : '-' }}</td>
                    <td class="text-center">{{ $p['tanggal'] }}</td>
                    <td class="text-center font-bold" style="color: {{ $p['layanan'] === 'Ranap' ? '#1d4ed8' : ($p['layanan'] === 'IGD' ? '#be123c' : '#047857') }};">
                        {{ $p['layanan'] }}
                    </td>
                    <td class="text-center">{{ $p['prioritas'] }}</td>
                    <td class="text-center">{{ $p['kasus'] }}</td>
                    <td class="text-center">{{ $p['status_dinas'] ?? 'UMUM' }}</td>
                    <td>{{ $p['dokter'] }}</td>
                    <td>{{ $p['penjamin'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada data rincian pasien ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        * Dokumen dicetak secara otomatis oleh SIMRS Dashboard pada {{ now()->translatedFormat('d F Y, H:i:s') }} WIB.
        @if ($isMasked)
            Identitas dilindungi dengan kebijakan sensor masking parsial untuk menjaga kerahasiaan data medis.
        @else
            Dokumen memuat data identitas lengkap pasien untuk kepentingan audit dan verifikasi internal medis.
        @endif
    </div>
</body>
</html>

