<x-content x-data="{ menuVisible: $wire.entangle('mode').live }">
    <x-breadcrumb title="Rekap Pendaftaran" :items="[['title' => 'Rekap Pendaftaran']]" />

    <div class="space-y-9">
        <div class="space-y-6">
            <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Umum</h4>
            <div
                class="grid grid-cols-1 gap-3 justify-stretch sm:grid-cols-2 md:grid-cols-3 lg:grid-flow-col lg:grid-cols-none">
                <x-box wire:click="setStatus('hari_ini')" :isActive="$filter == 'hari_ini'" class="cursor-pointer" title="Pasien Hari Ini"
                    icon="i-ph-users-four" :value="$todaysRecap" />
                <x-box wire:click="setStatus('bulan_ini')" :isActive="$filter == 'bulan_ini'" class="cursor-pointer"
                    title="Pasien Bulan Ini" icon="i-ph-users-four" :value="$recapOfTheMonth" />
                <x-box wire:click="setStatus('tahun_ini')" :isActive="$filter == 'tahun_ini'" class="cursor-pointer"
                    title="Pasien Tahun Ini" icon="i-ph-users-four" :value="$recapOfTheYear" />
                <x-box wire:click="setStatus('keseluruhan')" :isActive="$filter == 'keseluruhan'" class="cursor-pointer"
                    title="Pasien Keseluruhan" icon="i-ph-users-four" :value="$overallRecap" />
            </div>
        </div>

        <div
            class="flex items-center justify-center gap-3 before:inline before:w-full before:h-1 before:bg-primary after:inline after:w-full after:h-1 after:bg-primary">
            @foreach ($modeGroup as $item)
                <button
                    class="relative font-semibold transition duration-300 text-base cursor-pointer py-2.5 px-5 min-w-36"
                    :class="menuVisible == '{{ $item }}' ?
                        'focus:outline-none text-white bg-primary hover:bg-primary/90 focus:ring-4 focus:ring-primary dark:focus:ring-primary rounded-full text-nowrap' :
                        'text-gray-900 focus:outline-none bg-white border border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 rounded-full  text-nowrap'"
                    x-on:click="menuVisible = '{{ $item }}'; $wire.mode = '{{ $item }}';">
                    {{ Str::headline($item) }}
                </button>
            @endforeach
        </div>

        {{-- TAMPILAN DALAM ANGKA --}}
        <div class="space-y-9" x-show="menuVisible == 'dalam_angka'" x-transition>
            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Kelompok Umur</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($ageGroup as $age)
                        <x-box :title="$age['title']" icon="i-ph-users-four" :value="$age['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Jenis Kelamin</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($genders as $gender)
                        <x-box :title="$gender['title']" icon="i-ph-users-four" :value="$gender['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Status Lanjut</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($advanceStatusGroup as $status)
                        <x-box :title="$status['title']" icon="i-ph-users-four" :value="$status['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Jenis Pasien</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($typeGroup as $type)
                        <x-box :title="$type['title']" icon="i-ph-users-four" :value="$type['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Pasien Dinas TNI</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($tniGroups as $tniGroup)
                        <x-box :title="$tniGroup['title']" icon="i-ph-users-four" :value="$tniGroup['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Pasien Dinas Polri</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($polriGroups as $polriGroup)
                        <x-box :title="$polriGroup['title']" icon="i-ph-users-four" :value="$polriGroup['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Status Pelayanan</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($statusGroup as $status)
                        <x-box :title="$status['title']" icon="i-ph-users-four" :value="$status['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Jenis Bayar</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($payTypes as $type)
                        <x-box :title="$type['title']" icon="i-ph-users-four" :value="$type['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per Poliklinik</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($polyclinics as $polyclinic)
                        <x-box :title="$polyclinic['title']" icon="i-ph-users-four" :value="$polyclinic['value']" />
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-lg font-bold text-center uppercase text-primary">Rekap Per DPJP</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($doctors as $doctor)
                        <x-box :title="$doctor['title']" icon="i-ph-users-four" :value="$doctor['value']" />
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TAMPILAN GRAFIK --}}
        <div class="space-y-9" x-show="menuVisible == 'grafik'" x-transition>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Jenis Kelamin</h4>
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientGenders"
                        label="Jenis Kelamin" :data="$genders" key="registeredPatientGenders" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Status Lanjut</h4>
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientAdvanceStatus"
                        label="Status Lanjut" :data="$advanceStatusGroup" key="registeredPatientAdvanceStatus" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Jenis Pasien</h4>
                    <livewire:registered-patient.chart chartType="doughnut" chartId="registeredPatientTypes"
                        label="Jenis Pasien" :data="$typeGroup" key="registeredPatientTypes" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Pasien Dinas TNI</h4>
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredTniGroups"
                        label="Pasien Dinas" :data="$tniGroups" key="registeredTniGroups" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Pasien Dinas Polri</h4>
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPolriGroups"
                        label="Pasien Dinas" :data="$polriGroups" key="registeredPolriGroups" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Kelompok Umur</h4>
                    <livewire:registered-patient.chart chartType="polarArea" chartId="registeredPatientAgeGroup"
                        label="Kelompok Umur" :data="$ageGroup" key="registeredPatientAgeGroup" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="w-full p-6 mx-auto bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Poliklinik</h4>
                    <livewire:registered-patient.chart chartId="registeredPatientPolyclinics" label="Poliklinik"
                        :data="$polyclinics" key="registeredPatientPolyclinics" />
                </div>
                <div class="w-full p-6 mx-auto bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">DPJP</h4>
                    <livewire:registered-patient.chart chartId="registeredPatientDoctors" label="DPJP"
                        :data="$doctors" key="registeredPatientDoctors" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Status Pelayanan</h4>
                    <livewire:registered-patient.chart chartId="registeredPatientStatus" label="Status Pelayanan"
                        :data="$statusGroup" key="registeredPatientStatus" />
                </div>
                <div class="p-6 bg-white dark:bg-boxdark dark:drop-shadow-none drop-shadow-1">
                    <h4 class="mb-6 font-bold text-center text-primary font-lg">Jenis Bayar</h4>
                    <livewire:registered-patient.chart chartType="line" chartId="registeredPatientPayTypes"
                        label="Jenis Bayar" :data="$payTypes" key="registeredPatientPayTypes" />
                </div>
            </div>
        </div>
    </div>
</x-content>
