<x-content>
    <x-breadcrumb title="RL 1.1 - Data Dasar Rumah Sakit" :items="[['title' => 'SIRS Online', 'href' => route('sirs.index')], ['title' => 'RL 1.1']]" />

    <x-sirs.report-header title="RL 1.1 - Data Dasar Rumah Sakit" subtitle="Profil dan identitas rumah sakit"
        :profil="$profil" bulan="" :tahun="now()->year" />

    <div class="bg-white dark:bg-boxdark rounded-3xl border border-stroke dark:border-strokedark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach ([
        'Kode Registrasi RS' => $data['kode_registrasi'],
        'Nama Rumah Sakit' => $data['nama_rs'],
        'Alamat' => $data['alamat'],
        'Kabupaten/Kota' => $data['kabupaten_kota'],
        'Provinsi' => $data['provinsi'],
        'Telepon' => $data['telepon'],
        'Email' => $data['email'],
        'Jenis RS' => $data['jenis_rs'],
        'Kelas RS' => $data['kelas_rs'],
        'Kepemilikan/Penyelenggara' => $data['kepemilikan'],
    ] as $label => $value)
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4">
                            <td class="px-4 py-3 w-1/3 font-bold text-gray-500 dark:text-gray-400">{{ $label }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-content>
