<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class Home extends Component
{
    #[Computed]
    public function outpatientToday()
    {
        return DB::connection('simrs')
            ->table('reg_periksa')
            ->whereDate('tgl_registrasi', Carbon::today())
            ->count();
    }

    #[Computed]
    public function inpatientActive()
    {
        return DB::connection('simrs')
            ->table('kamar_inap')
            ->where('stts_pulang', '-')
            ->count();
    }

    #[Computed]
    public function roomStats()
    {
        $stats = DB::connection('simrs')
            ->table('kamar')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->selectRaw("SUM(CASE WHEN status = 'KOSONG' THEN 1 ELSE 0 END) as tersedia")
            ->selectRaw("SUM(CASE WHEN status = 'ISI' THEN 1 ELSE 0 END) as terisi")
            ->first();
            
        return [
            'available' => $stats->tersedia ?? 0,
            'filled' => $stats->terisi ?? 0,
            'total' => ($stats->tersedia ?? 0) + ($stats->terisi ?? 0)
        ];
    }

    #[Computed]
    public function polyclinicCount()
    {
        return DB::connection('simrs')
            ->table('poliklinik')
            ->where('status', '1')
            ->whereNotIn('kd_poli', ['-', 'TES', 'TEST'])
            ->count();
    }

    #[Computed]
    public function inpatientTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $masuk = DB::connection('simrs')
            ->table('kamar_inap')
            ->selectRaw('tgl_masuk as tanggal, count(*) as total')
            ->where('tgl_masuk', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_masuk')
            ->pluck('total', 'tanggal');

        $keluar = DB::connection('simrs')
            ->table('kamar_inap')
            ->selectRaw('tgl_keluar as tanggal, count(*) as total')
            ->where('tgl_keluar', '>=', Carbon::today()->subDays(14))
            ->where('tgl_keluar', '!=', '0000-00-00')
            ->groupBy('tgl_keluar')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Pasien Masuk',
                    'data' => $days->map(fn($d) => (int)$masuk->get($d, 0))->values()->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'Pasien Keluar',
                    'data' => $days->map(fn($d) => (int)$keluar->get($d, 0))->values()->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function outpatientTrend()
    {
        $days = collect();
        for ($i = 14; $i >= 0; $i--) {
            $days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $visits = DB::connection('simrs')
            ->table('reg_periksa')
            ->selectRaw('tgl_registrasi as tanggal, count(*) as total')
            ->where('tgl_registrasi', '>=', Carbon::today()->subDays(14))
            ->groupBy('tgl_registrasi')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Kunjungan Rawat Jalan',
                    'data' => $days->map(fn($d) => (int)$visits->get($d, 0))->values()->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    #[Computed]
    public function clinicalIndicators()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now();
        $daysInMonth = $startOfMonth->diffInDays($today) + 1;
        
        $totalBed = DB::connection('simrs')
            ->table('kamar')
            ->where('kd_bangsal', '!=', 'TRANS')
            ->count();
        
        // HP (Hari Perawatan) & LOS (Length of Stay)
        // Filter out records where the room belongs to TRANS ward
        $inpatientData = DB::connection('simrs')
            ->table('kamar_inap')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->where('kamar.kd_bangsal', '!=', 'TRANS')
            ->whereBetween('kamar_inap.tgl_masuk', [$startOfMonth, $today])
            ->selectRaw('SUM(kamar_inap.lama) as total_hp, count(*) as total_keluar')
            ->first();

        $hp = $inpatientData->total_hp ?? 0;
        $keluar = $inpatientData->total_keluar ?? 1; // Avoid division by zero

        $bor = ($totalBed > 0) ? ($hp / ($totalBed * $daysInMonth)) * 100 : 0;
        $alos = $hp / $keluar;
        $toi = (($totalBed * $daysInMonth) - $hp) / $keluar;
        $bto = $keluar / ($totalBed ?: 1);

        return [
            'bor' => round($bor, 2),
            'alos' => round($alos, 2),
            'toi' => round($toi, 2),
            'bto' => round($bto, 2),
            'beds' => $totalBed
        ];
    }

    public function render()
    {
        return view('pages.home');
    }
}
