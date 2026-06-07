<div class="relative p-6 overflow-hidden bg-white border-l-4 lg:border-l-8 rounded-md drop-shadow-md
    {{ $record['layanan']['status'] === 'Ralan' ? 'border-cyan-400' : 'border-violet-400' }}">
    <div class="xl:[column-count:4] sm:[column-count:3] [column-count:2] gap-x-3">
        {{-- Data Pasien --}}
        <x-detail-item icon="i-ph-identification-card" label="No Rekam Medis"
            :value="$record['pasien']['no_rekam_medis']" />
        <x-detail-item icon="i-ph-user" label="Nama Pasien" :value="$record['pasien']['nama']" />
        <x-detail-item icon="i-ph-gender-intersex" label="Jenis Kelamin"
            :value="$record['pasien']['jenis_kelamin']" />
        {{-- Data Layanan --}}
        <x-detail-item icon="i-ph-hash" label="No Rawat" :value="$record['layanan']['no_rawat']" />
        <x-detail-item icon="i-ph-radioactive" label="Jenis Pemeriksaan"
            :value="$record['layanan']['jenis_pemeriksaan']" />
        <x-detail-item icon="i-ph-calendar" label="Tanggal Periksa"
            :value="$record['layanan']['tgl_periksa']" />
        <x-detail-item icon="i-ph-clock" label="Jam" :value="$record['layanan']['jam']" />
        <x-detail-item icon="i-ph-stethoscope" label="Dokter" :value="$record['dokter']['nama_dokter']" />
        <x-detail-item icon="i-ph-money" label="Biaya"
            :value="'Rp ' . number_format($record['layanan']['biaya'], 0, ',', '.')" />
    </div>
    <div @class([
        'absolute top-0 right-0 lg:bottom-0 lg:[writing-mode:vertical-rl] text-center lg:py-3 lg:px-1 py-1 px-3 rounded-bl-md lg:rounded-bl-none text-sm',
        'bg-cyan-100 text-cyan-700' => $record['layanan']['status'] === 'Ralan',
        'bg-violet-100 text-violet-700' => $record['layanan']['status'] === 'Ranap',
    ])>
        {{ $record['layanan']['status'] }}
    </div>
</div>
