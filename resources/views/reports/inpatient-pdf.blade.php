<!DOCTYPE html>
<html>
<head>
    <title>Data Rawat Inap</title>
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
    <footer>
        Data diperoleh melalui {{ config('app.name') }} milik {{ config('app.hospital_name') }} pada {{ now()->format('d/m/Y H:i:s') }}.
    </footer>
    <div class="header">
        <h2>DATA PASIEN RAWAT INAP</h2>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
                <th>No Rawat</th>
                <th>No RM</th>
                <th>Nama Pasien</th>
                <th>Jenis Pasien</th>
                <th>Kamar</th>
                <th>Bangsal</th>
                <th>Dokter</th>
                <th>Jenis Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $index => $patient)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $patient['ranap']['waktu_masuk'] }}</td>
                    <td>{{ $patient['ranap']['waktu_keluar'] }}</td>
                    <td>{{ $patient['pendaftaran']['no_rawat'] }}</td>
                    <td>{{ $patient['pendaftaran']['no_rekam_medis'] }}</td>
                    <td>{{ $patient['pasien']['data']['nama'] }}</td>
                    <td>{{ $patient['pasien']['data']['jenis_pasien'] }}</td>
                    <td>{{ $patient['ranap']['kamar']['kode_kamar'] }}</td>
                    <td>{{ $patient['ranap']['kamar']['bangsal']['nama_bangsal'] }}</td>
                    <td>{{ $patient['dokter']['nama_dokter'] }}</td>
                    <td>{{ $patient['pendaftaran']['jenis_bayar'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
