<div class=
    "relative px-6 pb-6 pr-12 overflow-hidden bg-white rounded-md pt-9 drop-shadow-md">
    <div class="absolute top-0 left-0 flex items-start">
        <div @class([
            'text-sm text-center py-1 px-3 rounded-br-md inline-flex gap-1',
            StatusHelper::getColor(
                'status_pelayanan',
                $patient['pendaftaran']['status_pelayanan']),
        ])>
            <span class="hidden lg:block">
                Status Pelayanan:
            </span>
            {{ $patient['pendaftaran']['status_pelayanan'] }}
        </div>
        @if (str_contains(strtoupper($patient['pasien']['data']['jenis_pasien']), 'TNI'))
            <div
                class="bg-emerald-600 text-white text-[10px] font-black px-3 py-1 rounded-b-md shadow-sm ml-1 uppercase tracking-tighter">
                Pasien Dinas TNI
            </div>
        @endif
    </div>
    <div class="xl:[column-count:5] sm:[column-count:3] [column-count:2] gap-x-3">
        {{-- Data Pendaftaran --}}
        <x-patient-item title="No Registrasi" :value="$patient['pendaftaran']['no_pendaftaran']" />
        <x-patient-item title="No Rawat" :value="$patient['pendaftaran']['no_rawat']" />
        <x-patient-item title="Waktu Pendaftaran" :value="$patient['pendaftaran']['waktu_pendaftaran']" />
        <x-patient-item title="Status Poli" :value="$patient['pendaftaran']['status_poli']" />
        <x-patient-item title="Jenis Bayar" :value="$patient['pendaftaran']['jenis_bayar']" />
        <x-patient-item title="Status Bayar" :value="$patient['pendaftaran']['status_bayar']" />
        <x-patient-item title="No. Peserta" :value="$patient['pasien']['data']['no_peserta']" />
        {{-- Data Pasien --}}
        <x-patient-item title="No Rekam Medis" :value="$patient['pasien']['data']['no_rekam_medis']" />
        <x-patient-item title="Nama Pasien" :value="$patient['pasien']['data']['nama']" />
        <x-patient-item title="Tempat, Tgl Lahir" :value="$patient['pasien']['data']['tempat_lahir'] . ', ' . $patient['pasien']['data']['tanggal_lahir']" />
        <x-patient-item title="Umur Daftar" :value="$patient['pendaftaran']['umur_mendaftar']" />
        <x-patient-item title="Umur Saat Ini" :value="$patient['pasien']['data']['umur']" />
        <x-patient-item title="Jenis Kelamin" :value="$patient['pasien']['data']['jenis_kelamin']" />
        <x-patient-item title="Alamat" :value="$patient['pasien']['data']['alamat']" />
        <x-patient-item title="Jenis Pasien" :value="$patient['pasien']['data']['jenis_pasien']" />
        {{-- Data Penanggungjawab --}}
        <x-patient-item title="Nama Pj" :value="$patient['pasien']['penanggungjawab']['nama']" />
        <x-patient-item title="Hubungan Pj" :value="$patient['pasien']['penanggungjawab']['status']" />
        {{-- Data Poli dan Dokter --}}
        <x-patient-item title="Poliklinik" :value="$patient['poliklinik']['nama_poliklinik']" />
        <x-patient-item title="DPJP" :value="$patient['dokter']['nama_dokter']" />
        <x-patient-item title="Status Poli" :value="$patient['pendaftaran']['status_poli']" />
    </div>
    <div @class([
        'absolute top-0 right-0 lg:bottom-0 text-sm lg:text-base lg:[writing-mode:vertical-rl] text-center lg:px-1 lg:py-3 py-1 px-3 rounded-bl-md lg:rounded-bl-none',
        'bg-cyan-100 text-cyan-700' =>
            $patient['pendaftaran']['status_lanjut'] == 'Ralan',
        'bg-violet-100 text-violet-700' =>
            $patient['pendaftaran']['status_lanjut'] == 'Ranap',
    ])>
        {{ $patient['pendaftaran']['status_lanjut'] }}
    </div>
</div>
