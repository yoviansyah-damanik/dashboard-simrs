<div class="relative p-6 overflow-hidden bg-white border-l-4 lg:border-l-8 rounded-md drop-shadow-md
    {{ $record['resep']['status'] === 'Ralan' ? 'border-cyan-400' : 'border-violet-400' }}">
    <div class="xl:[column-count:4] sm:[column-count:3] [column-count:2] gap-x-3">
        {{-- Data Pasien --}}
        <x-detail-item icon="i-ph-identification-card" label="No Rekam Medis"
            :value="$record['pasien']['no_rekam_medis']" />
        <x-detail-item icon="i-ph-user" label="Nama Pasien" :value="$record['pasien']['nama']" />
        <x-detail-item icon="i-ph-gender-intersex" label="Jenis Kelamin"
            :value="$record['pasien']['jenis_kelamin']" />
        {{-- Data Resep --}}
        <x-detail-item icon="i-ph-hash" label="No Resep" :value="$record['resep']['no_resep']" />
        <x-detail-item icon="i-ph-hash" label="No Rawat" :value="$record['resep']['no_rawat']" />
        <x-detail-item icon="i-ph-pill" label="Jenis Resep" :value="$record['resep']['jenis_resep']" />
        <x-detail-item icon="i-ph-calendar" label="Tgl Perawatan"
            :value="$record['resep']['tgl_perawatan']" />
        <x-detail-item icon="i-ph-clock" label="Jam" :value="$record['resep']['jam']" />
        <x-detail-item icon="i-ph-calendar-check" label="Tgl Peresepan"
            :value="$record['resep']['tgl_peresepan'] . ' ' . $record['resep']['jam_peresepan']" />
        <x-detail-item icon="i-ph-calendar-dots" label="Tgl Penyerahan"
            :value="$record['resep']['tgl_penyerahan'] . ' ' . $record['resep']['jam_penyerahan']" />
        <x-detail-item icon="i-ph-stethoscope" label="Dokter" :value="$record['dokter']['nama_dokter']" />
    </div>
    <div @class([
        'absolute top-0 right-0 lg:bottom-0 lg:[writing-mode:vertical-rl] text-center lg:py-3 lg:px-1 py-1 px-3 rounded-bl-md lg:rounded-bl-none text-sm',
        'bg-cyan-100 text-cyan-700' => $record['resep']['status'] === 'Ralan',
        'bg-violet-100 text-violet-700' => $record['resep']['status'] === 'Ranap',
    ])>
        {{ $record['resep']['status'] }}
    </div>
</div>
