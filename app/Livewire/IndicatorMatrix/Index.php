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

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        $matrix = SirsFacilityRepository::getYearlyIndicatorMatrix($this->tahun);

        return view('pages.indicator-matrix.index', [
            'matrix' => $matrix,
            'profil' => SirsHelper::getProfilRS(),
            'chartData' => $this->prepareChartData($matrix),
        ])->title('Matriks Indikator Tahunan');
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
                'minTarget' => 60,
                'maxTarget' => 85,
                'targetLabel' => '60 - 85%',
                'description' => 'Persentase pemanfaatan tempat tidur rawat inap',
            ],
            'alos' => [
                'name' => 'ALOS (Average Length of Stay)',
                'shortName' => 'ALOS',
                'unit' => ' Hari',
                'decimals' => 1,
                'color' => '#3B82F6',
                'bgColor' => 'rgba(59, 130, 246, 0.12)',
                'minTarget' => 6,
                'maxTarget' => 9,
                'targetLabel' => '6 - 9 Hari',
                'description' => 'Rata-rata lama perawatan pasien rawat inap',
            ],
            'bto' => [
                'name' => 'BTO (Bed Turn Over)',
                'shortName' => 'BTO',
                'unit' => ' Kali',
                'decimals' => 1,
                'color' => '#F59E0B',
                'bgColor' => 'rgba(245, 158, 11, 0.12)',
                'minTarget' => 40,
                'maxTarget' => 50,
                'targetLabel' => '40 - 50 Kali/Thn',
                'description' => 'Frekuensi pemakaian tempat tidur dalam setahun',
            ],
            'toi' => [
                'name' => 'TOI (Turn Over Interval)',
                'shortName' => 'TOI',
                'unit' => ' Hari',
                'decimals' => 1,
                'color' => '#8B5CF6',
                'bgColor' => 'rgba(139, 92, 246, 0.12)',
                'minTarget' => 1,
                'maxTarget' => 3,
                'targetLabel' => '1 - 3 Hari',
                'description' => 'Rata-rata hari tempat tidur kosong sebelum diisi kembali',
            ],
            'ndr' => [
                'name' => 'NDR (Net Death Rate)',
                'shortName' => 'NDR',
                'unit' => '‰',
                'decimals' => 1,
                'color' => '#EC4899',
                'bgColor' => 'rgba(236, 72, 153, 0.12)',
                'minTarget' => 0,
                'maxTarget' => 25,
                'targetLabel' => '< 25‰',
                'description' => 'Kematian rawat inap ≥ 48 jam perawatan per 1000 penderita keluar',
            ],
            'gdr' => [
                'name' => 'GDR (Gross Death Rate)',
                'shortName' => 'GDR',
                'unit' => '‰',
                'decimals' => 1,
                'color' => '#EF4444',
                'bgColor' => 'rgba(239, 68, 68, 0.12)',
                'minTarget' => 0,
                'maxTarget' => 45,
                'targetLabel' => '< 45‰',
                'description' => 'Kematian umum rawat inap per 1000 penderita keluar',
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
            $isIdeal = HospitalIndicatorService::isWithinRange($key, $yearlyVal);

            $indicators[$key] = array_merge($meta, [
                'data' => $values,
                'yearly' => $yearlyVal,
                'avg' => $avgVal,
                'max' => $maxItem,
                'min' => $minItem,
                'isIdeal' => $isIdeal,
            ]);
        }

        return [
            'labels' => $shortMonths,
            'fullLabels' => $months,
            'indicators' => $indicators,
        ];
    }
}
