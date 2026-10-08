<?php

namespace App\Livewire\IndicatorMatrix;

use App\Helpers\SirsHelper;
use App\Repository\SirsFacilityRepository;
use App\Services\HospitalIndicatorService;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url]
    public int $tahun = 0;

    #[Url]
    public string $standard = 'depkes'; // 'depkes' | 'barber_johnson'

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
        if (!in_array($this->standard, ['depkes', 'barber_johnson'])) {
            $this->standard = 'depkes';
        }
    }

    public function setStandard(string $standard)
    {
        if (in_array($standard, ['depkes', 'barber_johnson'])) {
            $this->standard = $standard;
        }
    }

    public function render()
    {
        $matrix = SirsFacilityRepository::getYearlyIndicatorMatrix($this->tahun);

        return view('pages.indicator-matrix.index', [
            'matrix' => $matrix,
            'profil' => SirsHelper::getProfilRS(),
            'tahun' => $this->tahun,
            'standard' => $this->standard,
            'chartData' => $this->prepareChartData($matrix),
            'standardsComparison' => $this->getStandardsComparison($matrix),
        ])->title('Matriks Indikator Tahunan');
    }

    /**
     * Ekspor data matriks indikator tahunan ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $matrix = SirsFacilityRepository::getYearlyIndicatorMatrix($this->tahun);
        $profil = SirsHelper::getProfilRS();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.indicator-matrix-pdf', [
            'matrix' => $matrix,
            'profil' => $profil,
            'tahun' => $this->tahun,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Rekam Medis SIMRS',
        ])->setPaper('a4', 'landscape');

        $filename = 'matriks-indikator-rawat-inap-' . $this->tahun . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }

    /**
     * Menyiapkan payload dataset dan benchmark indikator untuk visualisasi grafik.
     */
    protected function prepareChartData(array $matrix): array
    {
        $months = [];
        $shortMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = SirsHelper::getMonthName($i);
            $months[] = $monthName;
            $shortMonths[] = substr($monthName, 0, 3);
        }

        $config = [
            'bor' => [
                'name' => 'BOR (Bed Occupancy Rate)',
                'shortName' => 'BOR',
                'unit' => '%',
                'decimals' => 1,
                'color' => '#10B981',
                'bgColor' => 'rgba(16, 185, 129, 0.12)',
                'description' => 'Persentase pemanfaatan tempat tidur rawat inap',
                'standards' => [
                    'depkes' => [
                        'min' => 60,
                        'max' => 85,
                        'targetLabel' => '60 - 85%',
                        'yearlyTargetLabel' => '60 - 85%',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 75,
                        'max' => 85,
                        'targetLabel' => '75 - 85%',
                        'yearlyTargetLabel' => '75 - 85%',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
            'alos' => [
                'name' => 'ALOS (Average Length of Stay)',
                'shortName' => 'ALOS',
                'unit' => ' Hari',
                'decimals' => 1,
                'color' => '#3B82F6',
                'bgColor' => 'rgba(59, 130, 246, 0.12)',
                'description' => 'Rata-rata lama perawatan pasien rawat inap',
                'standards' => [
                    'depkes' => [
                        'min' => 6,
                        'max' => 9,
                        'targetLabel' => '6 - 9 Hari',
                        'yearlyTargetLabel' => '6 - 9 Hari',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 3,
                        'max' => 12,
                        'targetLabel' => '3 - 12 Hari',
                        'yearlyTargetLabel' => '3 - 12 Hari',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
            'bto' => [
                'name' => 'BTO (Bed Turn Over)',
                'shortName' => 'BTO',
                'unit' => ' Kali',
                'decimals' => 1,
                'color' => '#F59E0B',
                'bgColor' => 'rgba(245, 158, 11, 0.12)',
                'description' => 'Frekuensi pemakaian tempat tidur per bulan',
                'standards' => [
                    'depkes' => [
                        'min' => 2,
                        'max' => 4,
                        'targetLabel' => '2 - 4 Kali/Bulan',
                        'yearlyTargetLabel' => '40 - 50 Kali/Thn',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 2.5,
                        'max' => null,
                        'targetLabel' => '≥ 2.5 Kali/Bulan',
                        'yearlyTargetLabel' => '≥ 30 Kali/Thn',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
            'toi' => [
                'name' => 'TOI (Turn Over Interval)',
                'shortName' => 'TOI',
                'unit' => ' Hari',
                'decimals' => 1,
                'color' => '#8B5CF6',
                'bgColor' => 'rgba(139, 92, 246, 0.12)',
                'description' => 'Rata-rata hari tempat tidur kosong sebelum diisi kembali',
                'standards' => [
                    'depkes' => [
                        'min' => 1,
                        'max' => 3,
                        'targetLabel' => '1 - 3 Hari',
                        'yearlyTargetLabel' => '1 - 3 Hari',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 1,
                        'max' => 3,
                        'targetLabel' => '1 - 3 Hari',
                        'yearlyTargetLabel' => '1 - 3 Hari',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
            'ndr' => [
                'name' => 'NDR (Net Death Rate)',
                'shortName' => 'NDR',
                'unit' => '‰',
                'decimals' => 1,
                'color' => '#EC4899',
                'bgColor' => 'rgba(236, 72, 153, 0.12)',
                'description' => 'Kematian rawat inap ≥ 48 jam perawatan per 1000 penderita keluar',
                'standards' => [
                    'depkes' => [
                        'min' => 0,
                        'max' => 25,
                        'targetLabel' => '< 25‰',
                        'yearlyTargetLabel' => '< 25‰',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 0,
                        'max' => 25,
                        'targetLabel' => '< 25‰',
                        'yearlyTargetLabel' => '< 25‰',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
            'gdr' => [
                'name' => 'GDR (Gross Death Rate)',
                'shortName' => 'GDR',
                'unit' => '‰',
                'decimals' => 1,
                'color' => '#EF4444',
                'bgColor' => 'rgba(239, 68, 68, 0.12)',
                'description' => 'Kematian umum rawat inap per 1000 penderita keluar',
                'standards' => [
                    'depkes' => [
                        'min' => 0,
                        'max' => 45,
                        'targetLabel' => '< 45‰',
                        'yearlyTargetLabel' => '< 45‰',
                        'name' => 'Standar Depkes RI',
                    ],
                    'barber_johnson' => [
                        'min' => 0,
                        'max' => 45,
                        'targetLabel' => '< 45‰',
                        'yearlyTargetLabel' => '< 45‰',
                        'name' => 'Standar Barber-Johnson',
                    ],
                ],
            ],
        ];

        $indicators = [];
        foreach ($config as $key => $meta) {
            $values = [];
            $activeMonths = [];

            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($matrix[$m][$key] ?? 0);
                $rounded = round($val, $meta['decimals']);
                $values[] = $rounded;

                if ($val > 0) {
                    $activeMonths[] = [
                        'value' => $rounded,
                        'month' => $months[$m - 1],
                    ];
                }
            }

            // Hitung min, max, dan rata-rata
            if (!empty($activeMonths)) {
                $maxItem = collect($activeMonths)->sortByDesc('value')->first();
                $minItem = collect($activeMonths)->sortBy('value')->first();
                $avgVal = round(collect($activeMonths)->avg('value'), $meta['decimals']);
            } else {
                $maxItem = ['value' => 0, 'month' => '-'];
                $minItem = ['value' => 0, 'month' => '-'];
                $avgVal = 0;
            }

            $yearlyVal = round((float) ($matrix['tahun'][$key] ?? 0), $meta['decimals']);

            // Hitung kepatuhan untuk kedua standar
            $isIdealDepkes = $key === 'bto'
                ? HospitalIndicatorService::isWithinRange($key, $avgVal, true, 'depkes')
                : HospitalIndicatorService::isWithinRange($key, $yearlyVal, false, 'depkes');

            $isIdealBarberJohnson = $key === 'bto'
                ? HospitalIndicatorService::isWithinRange($key, $avgVal, true, 'barber_johnson')
                : HospitalIndicatorService::isWithinRange($key, $yearlyVal, false, 'barber_johnson');

            $standards = $meta['standards'];
            $standards['depkes']['isIdeal'] = $isIdealDepkes;
            $standards['barber_johnson']['isIdeal'] = $isIdealBarberJohnson;

            // Standar aktif saat ini
            $activeStandardKey = $this->standard === 'barber_johnson' ? 'barber_johnson' : 'depkes';
            $activeMeta = $standards[$activeStandardKey];

            $indicators[$key] = array_merge($meta, [
                'data' => $values,
                'yearly' => $yearlyVal,
                'avg' => $avgVal,
                'max' => $maxItem,
                'min' => $minItem,
                'minTarget' => $activeMeta['min'],
                'maxTarget' => $activeMeta['max'],
                'targetLabel' => $activeMeta['targetLabel'],
                'yearlyTargetLabel' => $activeMeta['yearlyTargetLabel'],
                'isIdeal' => $activeMeta['isIdeal'],
                'standards' => $standards,
            ]);
        }

        return [
            'labels' => $shortMonths,
            'fullLabels' => $months,
            'indicators' => $indicators,
            'activeStandard' => $this->standard,
        ];
    }

    /**
     * Menyiapkan ringkasan komparasi 2 standar (Depkes RI vs Barber-Johnson).
     */
    protected function getStandardsComparison(array $matrix): array
    {
        $keys = [
            'bor' => ['name' => 'BOR (Bed Occupancy Rate)', 'short' => 'BOR', 'unit' => '%', 'note' => 'Barber-Johnson mensyaratkan BOR minimal 75% untuk efisiensi tinggi.'],
            'alos' => ['name' => 'ALOS (Average Length of Stay)', 'short' => 'ALOS', 'unit' => ' Hari', 'note' => 'Barber-Johnson menetapkan rentang luas 3-12 hari (sesuai efisiensi RS saat ini).'],
            'bto' => ['name' => 'BTO (Bed Turn Over)', 'short' => 'BTO', 'unit' => ' Kali', 'note' => 'Barber-Johnson mensyaratkan frekuensi TT minimal 30 kali/thn (≥ 2.5 kali/bulan).'],
            'toi' => ['name' => 'TOI (Turn Over Interval)', 'short' => 'TOI', 'unit' => ' Hari', 'note' => 'Kedua standar sepakat bahwa interval kosong 1-3 hari adalah waktu ideal sanitasi TT.'],
            'ndr' => ['name' => 'NDR (Net Death Rate)', 'short' => 'NDR', 'unit' => '‰', 'note' => 'Indikator mutu medis spesifik dari standar Depkes RI (< 25‰).'],
            'gdr' => ['name' => 'GDR (Gross Death Rate)', 'short' => 'GDR', 'unit' => '‰', 'note' => 'Indikator mortalitas umum spesifik dari standar Depkes RI (< 45‰).'],
        ];

        $comparison = [];
        foreach ($keys as $key => $info) {
            $yearlyVal = round((float) ($matrix['tahun'][$key] ?? 0), 1);

            $activeVals = [];
            for ($m = 1; $m <= 12; $m++) {
                $val = (float) ($matrix[$m][$key] ?? 0);
                if ($val > 0) {
                    $activeVals[] = $val;
                }
            }
            $avgVal = !empty($activeVals) ? round(collect($activeVals)->avg(), 1) : 0;

            $depkesIdeal = $key === 'bto'
                ? HospitalIndicatorService::isWithinRange($key, $avgVal, true, 'depkes')
                : HospitalIndicatorService::isWithinRange($key, $yearlyVal, false, 'depkes');

            $bjIdeal = $key === 'bto'
                ? HospitalIndicatorService::isWithinRange($key, $avgVal, true, 'barber_johnson')
                : HospitalIndicatorService::isWithinRange($key, $yearlyVal, false, 'barber_johnson');

            $depkesLabel = match ($key) {
                'bor' => '60 - 85%',
                'alos' => '6 - 9 Hari',
                'bto' => '2 - 4 Kali/Bln (40-50/Thn)',
                'toi' => '1 - 3 Hari',
                'ndr' => '< 25‰',
                'gdr' => '< 45‰',
            };

            $bjLabel = match ($key) {
                'bor' => '75 - 85%',
                'alos' => '3 - 12 Hari',
                'bto' => '≥ 2.5 Kali/Bln (≥ 30/Thn)',
                'toi' => '1 - 3 Hari',
                'ndr' => '< 25‰ (Depkes)',
                'gdr' => '< 45‰ (Depkes)',
            };

            $comparison[$key] = [
                'name' => $info['name'],
                'short' => $info['short'],
                'unit' => $info['unit'],
                'note' => $info['note'],
                'yearlyVal' => $yearlyVal,
                'avgVal' => $avgVal,
                'depkes' => [
                    'label' => $depkesLabel,
                    'isIdeal' => $depkesIdeal,
                ],
                'barber_johnson' => [
                    'label' => $bjLabel,
                    'isIdeal' => $bjIdeal,
                ],
            ];
        }

        return $comparison;
    }
}
