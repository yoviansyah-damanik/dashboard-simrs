<div class=
    "relative px-6 pb-6 pr-12 overflow-hidden bg-white rounded-md pt-9 drop-shadow-md">
    <div @class([
        'absolute top-0 text-sm left-0 text-center py-1 px-3 rounded-br-md inline-flex gap-1',
        'bg-green-100 text-green-700' =>
            $patient['ranap']['status_ranap'] == 'Masa Perawatan',
        'bg-red-100 text-red-700' =>
            $patient['ranap']['status_ranap'] != 'Masa Perawatan',
    ])>
        <span class="hidden lg:block">
            Status Ranap:
        </span>
        {{ $patient['ranap']['status_ranap'] }}
    </div>
    <div class="xl:[column-count:5] sm:[column-count:3] [column-count:2] gap-x-3">
        {{-- Data Pendaftaran --}}
        <x-patient-item title="No Registrasi" :value="$patient['pendaftaran']['no_pendaftaran']" />
        <x-patient-item title="No Rawat" :value="$patient['pendaftaran']['no_rawat']" />
        <x-patient-item title="Waktu Pendaftaran" :value="$patient['pendaftaran']['waktu_pendaftaran']" />
        <x-patient-item title="Status Pelayanan" :value="$patient['pendaftaran']['status_pelayanan']" />
        <x-patient-item title="Status Poli" :value="$patient['pendaftaran']['status_poli']" />
        <x-patient-item title="Jenis Bayar" :value="$patient['pendaftaran']['jenis_bayar']" />
        <x-patient-item title="Status Bayar" :value="$patient['pendaftaran']['status_bayar']" />
        <x-patient-item title="No. Peserta" :value="$patient['pasien']['data']['no_peserta']" />
        {{-- Data Pasien --}}
        <x-patient-item title="No Rekam Medis" :value="$patient['pasien']['data']['no_rekam_medis']" />
        <x-patient-item title="Nama Pasien" :value="$patient['pasien']['data']['nama']" />
        <x-patient-item title="Tempat, Tgl Lahir" :value="$patient['pasien']['data']['tempat_lahir'] . ', ' . $patient['pasien']['data']['tanggal_lahir']" />
        <x-patient-item title="Umur Daftar" :value="$patient['pendaftaran']['umur_mendaftar']" />
        <x-patient-item title="Jenis Kelamin" :value="$patient['pasien']['data']['jenis_kelamin']" />
        <x-patient-item title="Alamat" :value="$patient['pasien']['data']['alamat']" />
        <x-patient-item title="Jenis Pasien" :value="$patient['pasien']['data']['jenis_pasien']" />
        {{-- Data Penanggungjawab --}}
        <x-patient-item title="Nama Pj" :value="$patient['pasien']['penanggungjawab']['nama']" />
        <x-patient-item title="Hubungan Pj" :value="$patient['pasien']['penanggungjawab']['status']" />
        {{-- Data Kamar dan Dokter --}}
        <x-patient-item title="Kamar" :value="$patient['ranap']['kamar']['kode_kamar']" />
        {{-- <x-patient-item title="Bangsal" :value="$patient['ranap']['kamar']['bangsal']['nama_bangsal']" /> --}}
        <x-patient-item title="DPJP" :value="$patient['dokter']['nama_dokter']" />
        {{-- Data Diagnosa --}}
        <x-patient-item title="Waktu Masuk" :value="$patient['ranap']['waktu_masuk']" />
        <x-patient-item title="Diagnosa Awal" :value="$patient['ranap']['diagnosa_awal']" />
        <x-patient-item title="ICD X" :value="$patient['ranap']['icd10']
            ? $patient['ranap']['icd10']['kd_penyakit'] . ' - ' . $patient['ranap']['icd10']['nm_penyakit']
            : '-'" />
        <x-patient-item title="ICD IX" :value="$patient['ranap']['icd9']
            ? $patient['ranap']['icd9']['kode'] . ' - ' . $patient['ranap']['icd9']['deskripsi_panjang']
            : '-'" />
        <x-patient-item title="Lama Perawatan" :value="$patient['ranap']['lama'] . ' Hari'" />
        <x-patient-item title="Lama Perawatan (Sistem)" :value="$patient['ranap']['lama_sistem'] . ' Hari'" />
        @if ($patient['ranap']['status_pulang'] != '-')
            <x-patient-item title="Status Pulang" :value="$patient['ranap']['status_pulang']" />
            <x-patient-item title="Waktu Keluar" :value="$patient['ranap']['waktu_keluar']" />
            <x-patient-item title="Diagnosa Akhir" :value="$patient['ranap']['diagnosa_akhir']" />
        @endif
    </div>
</div>
