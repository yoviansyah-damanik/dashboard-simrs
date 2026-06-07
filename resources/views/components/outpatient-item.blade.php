<div
    class=
    "relative p-6 pr-12 overflow-hidden bg-white border-l-4 lg:border-l-8 rounded-md drop-shadow-md {{ StatusHelper::getColor('status_pelayanan', $patient['pendaftaran']['status_pelayanan']) }}">
    @if (str_contains(strtoupper($patient['pasien']['data']['jenis_pasien'] ?? $patient['pasien']['jenis_pasien']), 'TNI'))
        <div class="absolute top-0 left-0">
            <div
                class="bg-emerald-600 text-white text-[10px] font-black px-3 py-1 rounded-br-md shadow-sm uppercase tracking-tighter">
                Pasien Dinas TNI
            </div>
        </div>
    @endif
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
        <x-patient-item title="No Rekam Medis" :value="$patient['pasien']['no_rekam_medis']" />
        <x-patient-item title="Nama Pasien" :value="$patient['pasien']['nama']" />
        <x-patient-item title="Tempat, Tgl Lahir" :value="$patient['pasien']['tempat_lahir'] . ', ' . $patient['pasien']['tanggal_lahir']" />
        <x-patient-item title="Umur Daftar" :value="$patient['pendaftaran']['umur_mendaftar']" />
        <x-patient-item title="Jenis Kelamin" :value="$patient['pasien']['jenis_kelamin']" />
        <x-patient-item title="Alamat" :value="$patient['pasien']['alamat']" />
        <x-patient-item title="Jenis Pasien" :value="$patient['pasien']['jenis_pasien']" />
        {{-- Data Poli dan Dokter --}}
        <x-patient-item title="Poliklinik" :value="$patient['poliklinik']['nama_poliklinik']" />
        <x-patient-item title="Dokter" :value="$patient['dokter']['nama_dokter']" />
    </div>
    <div @class([
        'absolute top-0 right-0 lg:bottom-0 lg:[writing-mode:vertical-rl] text-center lg:py-3 lg:px-1 py-1 px-3 rounded-bl-md lg:rounded-bl-none',
        'bg-cyan-100 text-cyan-700' =>
            $patient['pendaftaran']['status_lanjut'] == 'Ralan',
        'bg-violet-100 text-violet-700' =>
            $patient['pendaftaran']['status_lanjut'] == 'Ranap',
    ])>
        {{ $patient['pendaftaran']['status_lanjut'] }}
    </div>
</div>
