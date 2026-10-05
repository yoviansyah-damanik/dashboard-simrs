<?php

namespace App\Livewire\Polyclinic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url(history: true)]
    public $search = '';
    public $excludeList = [];

    public function mount()
    {
        $this->excludeList = config('app.exclude_polys');
    }

    #[Computed]
    public function poliklinik()
    {
        $startMonth = now()->startOfMonth()->toDateString();
        $endMonth = now()->endOfMonth()->toDateString();

        return DB::connection('simrs')
            ->table('poliklinik as p')
            ->whereNotIn('p.nm_poli', $this->excludeList)
            ->whereNotIn('p.kd_poli', $this->excludeList)
            ->where('p.status', 1)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('p.nm_poli', 'like', '%' . $this->search . '%')
                        ->orWhere('p.kd_poli', 'like', '%' . $this->search . '%');
                });
            })
            ->leftJoin('jadwal as j', 'p.kd_poli', '=', 'j.kd_poli')
            ->leftJoin('reg_periksa as rp', function ($join) use ($startMonth, $endMonth) {
                $join->on('p.kd_poli', '=', 'rp.kd_poli')
                    ->whereBetween('rp.tgl_registrasi', [$startMonth, $endMonth]);
            })
            ->select(
                'p.kd_poli',
                'p.nm_poli',
                'p.status',
                DB::raw('COUNT(DISTINCT j.kd_dokter) as total_dokter'),
                DB::raw('COUNT(DISTINCT rp.no_rawat) as total_pasien_bulan_ini')
            )
            ->groupBy('p.kd_poli', 'p.nm_poli', 'p.status')
            ->orderBy('p.nm_poli')
            ->get()
            ->map(function ($item) {
                $item->theme = self::getPoliTheme($item->kd_poli, $item->nm_poli);
                return $item;
            });
    }

    /**
     * Menentukan tema visual (ikon, warna aksen, dan efek hover) berdasarkan jenis poliklinik.
     */
    public static function getPoliTheme(string $kdPoli, string $nmPoli): array
    {
        $kd = strtoupper(trim($kdPoli));
        $name = strtolower($nmPoli);

        if (str_contains($kd, 'ANA') || str_contains($name, 'anak') || str_contains($name, 'pediatri')) {
            return [
                'icon' => 'icon-[solar--baby-bold-duotone]',
                'bg' => 'bg-pink-500/10 text-pink-600 dark:text-pink-400',
                'color' => 'text-pink-500',
                'border_hover' => 'hover:border-pink-400/50',
            ];
        }

        if (str_contains($kd, 'BED') || str_contains($name, 'bedah') || str_contains($name, 'operasi')) {
            return [
                'icon' => 'icon-[solar--shield-warning-bold-duotone]',
                'bg' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'color' => 'text-amber-500',
                'border_hover' => 'hover:border-amber-400/50',
            ];
        }

        if (str_contains($kd, 'OBG') || str_contains($name, 'kandungan') || str_contains($name, 'kebidanan') || str_contains($name, 'obgyn')) {
            return [
                'icon' => 'icon-[solar--heart-angle-bold-duotone]',
                'bg' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
                'color' => 'text-purple-500',
                'border_hover' => 'hover:border-purple-400/50',
            ];
        }

        if (str_contains($kd, 'KAR') || str_contains($name, 'jantung') || str_contains($name, 'kardio')) {
            return [
                'icon' => 'icon-[solar--heart-pulse-bold-duotone]',
                'bg' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
                'color' => 'text-rose-500',
                'border_hover' => 'hover:border-rose-400/50',
            ];
        }

        if (str_contains($kd, 'INT') || str_contains($name, 'dalam') || str_contains($name, 'internis')) {
            return [
                'icon' => 'icon-[solar--stethoscope-bold-duotone]',
                'bg' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                'color' => 'text-blue-500',
                'border_hover' => 'hover:border-blue-400/50',
            ];
        }

        if (str_contains($kd, 'MAT') || str_contains($name, 'mata')) {
            return [
                'icon' => 'icon-[solar--eye-bold-duotone]',
                'bg' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400',
                'color' => 'text-teal-500',
                'border_hover' => 'hover:border-teal-400/50',
            ];
        }

        if (str_contains($kd, 'PAR') || str_contains($name, 'paru')) {
            return [
                'icon' => 'icon-[solar--waterdrop-bold-duotone]',
                'bg' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
                'color' => 'text-sky-500',
                'border_hover' => 'hover:border-sky-400/50',
            ];
        }

        if (str_contains($kd, 'SAR') || str_contains($name, 'saraf') || str_contains($name, 'neuro')) {
            return [
                'icon' => 'icon-[solar--atom-bold-duotone]',
                'bg' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
                'color' => 'text-indigo-500',
                'border_hover' => 'hover:border-indigo-400/50',
            ];
        }

        if (str_contains($kd, 'PTD') || str_contains($kd, 'GIG') || str_contains($name, 'gigi') || str_contains($name, 'prosthodonti')) {
            return [
                'icon' => 'icon-[solar--smile-circle-bold-duotone]',
                'bg' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'color' => 'text-emerald-500',
                'border_hover' => 'hover:border-emerald-400/50',
            ];
        }

        if (str_contains($kd, 'JIW') || str_contains($name, 'jiwa') || str_contains($name, 'psikiatri')) {
            return [
                'icon' => 'icon-[solar--ghost-bold-duotone]',
                'bg' => 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
                'color' => 'text-violet-500',
                'border_hover' => 'hover:border-violet-400/50',
            ];
        }

        if (str_contains($kd, 'IGD') || str_contains($name, 'darurat')) {
            return [
                'icon' => 'icon-[solar--danger-bold-duotone]',
                'bg' => 'bg-red-500/10 text-red-600 dark:text-red-400',
                'color' => 'text-red-500',
                'border_hover' => 'hover:border-red-400/50',
            ];
        }

        return [
            'icon' => 'icon-[solar--hospital-bold-duotone]',
            'bg' => 'bg-primary/10 text-primary',
            'color' => 'text-primary',
            'border_hover' => 'hover:border-primary/40',
        ];
    }

    public function render()
    {
        return view('pages.polyclinic.index', [
            'poliklinik' => $this->poliklinik(),
        ]);
    }
}
