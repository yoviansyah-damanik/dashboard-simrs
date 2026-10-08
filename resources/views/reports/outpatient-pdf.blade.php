<!DOCTYPE html>
<html>
<head>
    <title>Data Rawat Jalan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #666; }
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
        @page {
            margin: 1cm 1cm 2cm 1cm;
        }
    </style>
</head>
<body>
    @include('reports.partials.header')

    <div style="text-align: center; margin-bottom: 12px;">
        <h3 style="margin: 0; font-size: 13px; font-weight: bold; color: #0f172a; text-transform: uppercase;">DATA PASIEN RAWAT JALAN</h3>
        <p style="margin: 3px 0 0 0; font-size: 9px; color: #475569;">Periode: {{ $startDate }} s/d {{ $endDate }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Daftar</th>
                <th>No Rawat</th>
                <th>No RM</th>
                <th>Nama Pasien</th>
                <th>Jenis Pasien</th>
                <th>Poliklinik</th>
                <th>Dokter</th>
                <th>Jenis Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $index => $patient)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $patient['pendaftaran']['waktu_pendaftaran'] }}</td>
                    <td>{{ $patient['pendaftaran']['no_rawat'] }}</td>
                    <td>{{ $patient['pendaftaran']['no_rekam_medis'] }}</td>
                    <td>{{ $patient['pasien']['data']['nama'] }}</td>
                    <td>{{ $patient['pasien']['data']['jenis_pasien'] }}</td>
                    <td>{{ $patient['poliklinik']['nama_poliklinik'] }}</td>
                    <td>{{ $patient['dokter']['nama_dokter'] }}</td>
                    <td>{{ $patient['pendaftaran']['jenis_bayar'] }}</td>
                    <td>{{ $patient['pendaftaran']['status_pelayanan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
