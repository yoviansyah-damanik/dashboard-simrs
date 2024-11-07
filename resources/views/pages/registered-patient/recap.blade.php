<x-content x-data="{ menuVisible: $wire.entangle('mode').live }">
    <x-breadcrumb title="Rekap Pendaftaran" :items="[['title' => 'Rekap Pendaftaran']]" />

    <div class="space-y-9">
        <div class="space-y-6">
            <h4 class="text-lg font-bold text-center uppercase text-primary-500">Rekap Umum</h4>
            <div
                class="grid grid-cols-1 gap-3 justify-stretch sm:grid-cols-2 md:grid-cols-3 lg:grid-flow-col lg:grid-cols-none">
                <x-box wire:click="setStatus('hari_ini')" :isActive="$filter == 'hari_ini'" class="cursor-pointer" title="Pasien Hari Ini"
                    icon="i-ph-users-four" :value="GeneralHelper::numberFormat($todaysRecap)" />
                <x-box wire:click="setStatus('bulan_ini')" :isActive="$filter == 'bulan_ini'" class="cursor-pointer"
                    title="Pasien Bulan Ini" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($recapOfTheMonth)" />
                <x-box wire:click="setStatus('tahun_ini')" :isActive="$filter == 'tahun_ini'" class="cursor-pointer"
                    title="Pasien Tahun Ini" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($recapOfTheYear)" />
                <x-box wire:click="setStatus('keseluruhan')" :isActive="$filter == 'keseluruhan'" class="cursor-pointer"
                    title="Pasien Keseluruhan" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($overallRecap)" />
                <x-box :isActive="$filter == 'custom'" class="sm:col-span-2 lg:grid-cols-1" title="Kustom" icon="i-ph-users-four"
                    :value="GeneralHelper::numberFormat($customRecap)">
                    <div class="flex flex-col gap-1 sm:flex-row">
                        <x-form.input size="sm" type="date" max="{{ now()->format('Y-m-d') }}"
                            wire:model.defer="startDate" />
                        <x-form.input size="sm" type="date" max="{{ now()->format('Y-m-d') }}"
                            wire:model.defer="endDate" />
                        <x-button size="sm" color="secondary" icon="i-ph-magnifying-glass"
                            wire:click="setStatus('custom')" wire:loading.attr="disabled" />
                    </div>
                </x-box>
            </div>
        </div>

        <div
            class="flex items-center justify-center gap-3 before:inline before:w-full before:h-1 before:bg-primary-500 after:inline after:w-full after:h-1 after:bg-primary-500">
            @foreach ($modeGroup as $item)
                <button
                    class="relative font-semibold transition duration-300 text-base cursor-pointer py-2.5 px-5 min-w-36"
                    :class="menuVisible == '{{ $item }}' ?
                        'focus:outline-none text-white bg-primary-500 hover:bg-primary-500/90 focus:ring-4 focus:ring-primary-500 dark:focus:ring-primary-500 rounded-full text-nowrap' :
                        'text-gray-900 focus:outline-none bg-white border border-gray-200 hover:bg-gray-100 hover:text-primary-500 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 rounded-full  text-nowrap'"
                    x-on:click="menuVisible = '{{ $item }}'; $wire.mode = '{{ $item }}';">
                    {{ Str::headline($item) }}
                </button>
            @endforeach
        </div>

        {{-- TAMPILAN DALAM ANGKA --}}
        <div class="space-y-9" x-show="menuVisible == 'dalam_angka'" x-transition>
            <x-recap.in-figures title="Rekap Per Status Poli">
                @foreach ($statuses as $status)
                    <x-box :title="$status['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($status['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Kelompok Umur">
                @foreach ($ageGroup as $age)
                    <x-box :title="$age['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($age['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Jenis Kelamin">
                @foreach ($genders as $gender)
                    <x-box :title="$gender['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($gender['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Status Lanjut">
                @foreach ($advanceStatusGroup as $status)
                    <x-box :title="$status['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($status['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Status Mobile JKN">
                @foreach ($mobileJknGroup as $status)
                    <x-box :title="$status['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($status['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Jenis Pasien">
                @foreach ($typeGroup as $type)
                    <x-box :title="$type['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($type['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Golongan TNI">
                @foreach ($tniGroup as $tni)
                    <x-box :title="$tni['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($tni['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Satuan TNI">
                @foreach ($tniUnit as $tni)
                    <x-box :title="$tni['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($tni['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Golongan Polri">
                @foreach ($polriGroup as $polri)
                    <x-box :title="$polri['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($polri['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Satuan Polri">
                @foreach ($polriUnit as $polri)
                    <x-box :title="$polri['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($polri['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Status Pelayanan">
                @foreach ($serviceStatuses as $status)
                    <x-box :title="$status['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($status['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Jenis Bayar">
                @foreach ($payTypes as $type)
                    <x-box :title="$type['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($type['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per Poliklinik">
                @foreach ($polyclinics as $polyclinic)
                    <x-box :title="$polyclinic['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($polyclinic['value'])" />
                @endforeach
            </x-recap.in-figures>

            <x-recap.in-figures title="Rekap Per DPJP">
                @foreach ($doctors as $doctor)
                    <x-box :title="$doctor['title']" icon="i-ph-users-four" :value="GeneralHelper::numberFormat($doctor['value'])" />
                @endforeach
            </x-recap.in-figures>
        </div>

        {{-- TAMPILAN GRAFIK --}}
        <div class="space-y-9" x-show="menuVisible == 'grafik'" x-transition>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5">
                <x-recap.chart title="Status Poli">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientStatus"
                        label="Status Poli" :data="$statuses" key="registeredPatientStatus" />
                </x-recap.chart>
                <x-recap.chart title="Kelompok Umur">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientAgeGroup"
                        label="Kelompok Umur" :data="$ageGroup" key="registeredPatientAgeGroup" />
                </x-recap.chart>
                <x-recap.chart title="Jenis Kelamin">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientGenders"
                        label="Jenis Kelamin" :data="$genders" key="registeredPatientGenders" />
                </x-recap.chart>
                <x-recap.chart title="Status Lanjut">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientAdvanceStatus"
                        label="Status Lanjut" :data="$advanceStatusGroup" key="registeredPatientAdvanceStatus" />
                </x-recap.chart>
                <x-recap.chart title="Jenis Pasien">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPatientTypes"
                        label="Jenis Pasien" :data="$typeGroup" key="registeredPatientTypes" />
                </x-recap.chart>
                <x-recap.chart title="Status Mobile JKN">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredMobileJknGroup"
                        label="Status Mobile JKN" :data="$mobileJknGroup" key="registeredMobileJknGroup" />
                </x-recap.chart>
            </div>

            <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                <x-recap.chart title="Golongan TNI">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredTniGroup" label="Golongan"
                        :data="$tniGroup" key="registeredTniGroup" />
                </x-recap.chart>
                <x-recap.chart title="Satuan TNI" class="lg:col-span-3">
                    <livewire:registered-patient.chart chartType="bar" chartId="registeredTniUnit" label="Satuan"
                        :data="$tniUnit" key="registeredTniUnit" />
                </x-recap.chart>
            </div>

            <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                <x-recap.chart title="Golongan Polri">
                    <livewire:registered-patient.chart chartType="pie" chartId="registeredPolriGroup"
                        label="Golongan" :data="$polriGroup" key="registeredPolriGroup" />
                </x-recap.chart>
                <x-recap.chart title="Satuan Polri" class="col-span-3">
                    <livewire:registered-patient.chart chartType="bar" chartId="registeredPolriUnit" label="Satuan"
                        :data="$polriUnit" key="registeredPolriUnit" />
                </x-recap.chart>
            </div>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <x-recap.chart title="Poliklinik">
                    <livewire:registered-patient.chart chartId="registeredPatientPolyclinics" label="Poliklinik"
                        :data="$polyclinics" key="registeredPatientPolyclinics" />
                </x-recap.chart>
                <x-recap.chart title="DPJP">
                    <livewire:registered-patient.chart chartId="registeredPatientDoctors" label="DPJP"
                        :data="$doctors" key="registeredPatientDoctors" />
                </x-recap.chart>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <x-recap.chart title="Status Pelayanan">
                    <livewire:registered-patient.chart chartId="registeredPatientServiceStatus"
                        label="Status Pelayanan" :data="$serviceStatuses" key="registeredPatientServiceStatus" />
                </x-recap.chart>
                <x-recap.chart title="Jenis Bayar">
                    <livewire:registered-patient.chart chartType="bar" chartId="registeredPatientPayTypes"
                        label="Jenis Bayar" :data="$payTypes" key="registeredPatientPayTypes" />
                </x-recap.chart>
            </div>
        </div>
    </div>
</x-content>
