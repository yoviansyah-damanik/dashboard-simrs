<div class="relative p-6 overflow-hidden bg-white border-l-4 lg:border-l-8 rounded-md drop-shadow-md
    {{ $record['gizi']['status_lanjut'] === 'Ralan' ? 'border-cyan-400' : 'border-violet-400' }}">
    <div class="xl:[column-count:4] sm:[column-count:3] [column-count:2] gap-x-3">
        {{-- Data Pasien --}}
        <x-detail-item icon="i-ph-identification-card" label="No Rekam Medis"
            :value="$record['pasien']['no_rekam_medis']" />
        <x-detail-item icon="i-ph-user" label="Nama Pasien" :value="$record['pasien']['nama']" />
        <x-detail-item icon="i-ph-gender-intersex" label="Jenis Kelamin"
            :value="$record['pasien']['jenis_kelamin']" />
        {{-- Data Gizi --}}
        <x-detail-item icon="i-ph-hash" label="No Rawat" :value="$record['gizi']['no_rawat']" />
        <x-detail-item icon="i-ph-calendar" label="Tanggal" :value="$record['gizi']['tanggal']" />
        <x-detail-item icon="i-ph-fork-knife" label="Pola Makan" :value="$record['gizi']['pola_makan']" />
        <x-detail-item icon="i-ph-notepad" label="Diagnosis" :value="$record['gizi']['diagnosis']" />
        <x-detail-item icon="i-ph-first-aid" label="Intervensi Gizi"
            :value="$record['gizi']['intervensi_gizi']" />
        <x-detail-item icon="i-ph-user-circle" label="Petugas Gizi"
            :value="$record['petugas']['nama']" />
    </div>
    <div @class([
        'absolute top-0 right-0 lg:bottom-0 lg:[writing-mode:vertical-rl] text-center lg:py-3 lg:px-1 py-1 px-3 rounded-bl-md lg:rounded-bl-none text-sm',
        'bg-cyan-100 text-cyan-700' => $record['gizi']['status_lanjut'] === 'Ralan',
        'bg-violet-100 text-violet-700' => $record['gizi']['status_lanjut'] === 'Ranap',
    ])>
        {{ $record['gizi']['status_lanjut'] }}
    </div>
</div>
