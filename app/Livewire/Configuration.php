<?php

namespace App\Livewire;

use App\Helpers\ReportHelper;
use App\Models\Configuration as ConfigurationModel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Configuration extends Component
{
    // Konfigurasi Master Data SIMRS Khanza
    public bool $withInactivePolyclinic = false;
    public bool $withInactivePersonResponsibility = false;
    public bool $withInactiveDoctor = false;
    public bool $withInactiveRoom = false;
    public bool $withInactiveWard = false;

    // Feedback Notifikasi Pesan
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    // Tab Navigasi Halaman Pengaturan
    public string $activeTab = 'filter';

    public function mount(): void
    {
        $this->loadConfigurations();
    }

    public function loadConfigurations(): void
    {
        $configs = ConfigurationModel::all()->pluck('value', 'attribute')->toArray();

        $this->withInactivePolyclinic = !empty($configs['WITH_INACTIVE_POLYCLINIC']) && $configs['WITH_INACTIVE_POLYCLINIC'] !== '0';
        $this->withInactivePersonResponsibility = !empty($configs['WITH_INACTIVE_PERSON_RESPONSIBILITY']) && $configs['WITH_INACTIVE_PERSON_RESPONSIBILITY'] !== '0';
        $this->withInactiveDoctor = !empty($configs['WITH_INACTIVE_DOCTOR']) && $configs['WITH_INACTIVE_DOCTOR'] !== '0';
        $this->withInactiveRoom = !empty($configs['WITH_INACTIVE_ROOM']) && $configs['WITH_INACTIVE_ROOM'] !== '0';
        $this->withInactiveWard = !empty($configs['WITH_INACTIVE_WARD']) && $configs['WITH_INACTIVE_WARD'] !== '0';
    }

    public function toggleConfig(string $attribute): void
    {
        $map = [
            'WITH_INACTIVE_POLYCLINIC' => 'withInactivePolyclinic',
            'WITH_INACTIVE_PERSON_RESPONSIBILITY' => 'withInactivePersonResponsibility',
            'WITH_INACTIVE_DOCTOR' => 'withInactiveDoctor',
            'WITH_INACTIVE_ROOM' => 'withInactiveRoom',
            'WITH_INACTIVE_WARD' => 'withInactiveWard',
        ];

        if (!isset($map[$attribute])) {
            return;
        }

        $prop = $map[$attribute];
        $this->$prop = !$this->$prop;

        ConfigurationModel::updateOrCreate(
            ['attribute' => $attribute],
            ['value' => $this->$prop ? '1' : '0']
        );

        Cache::flush();

        $labels = [
            'WITH_INACTIVE_POLYCLINIC' => 'Poliklinik Nonaktif',
            'WITH_INACTIVE_PERSON_RESPONSIBILITY' => 'Penjamin/Penjab Nonaktif',
            'WITH_INACTIVE_DOCTOR' => 'Dokter DPJP Nonaktif',
            'WITH_INACTIVE_ROOM' => 'Kamar Perawatan Nonaktif',
            'WITH_INACTIVE_WARD' => 'Bangsal/Ruangan Nonaktif',
        ];

        $status = $this->$prop ? 'disertakan dalam filter' : 'disembunyikan (hanya data aktif)';
        $this->successMessage = "Pengaturan '{$labels[$attribute]}' berhasil diperbarui: {$status}.";
        $this->errorMessage = null;
    }

    public function resetToDefault(): void
    {
        $keys = [
            'WITH_INACTIVE_POLYCLINIC',
            'WITH_INACTIVE_PERSON_RESPONSIBILITY',
            'WITH_INACTIVE_DOCTOR',
            'WITH_INACTIVE_ROOM',
            'WITH_INACTIVE_WARD',
        ];

        foreach ($keys as $k) {
            ConfigurationModel::updateOrCreate(['attribute' => $k], ['value' => '0']);
        }

        $this->withInactivePolyclinic = false;
        $this->withInactivePersonResponsibility = false;
        $this->withInactiveDoctor = false;
        $this->withInactiveRoom = false;
        $this->withInactiveWard = false;

        Cache::flush();
        $this->successMessage = "Seluruh filter master data berhasil direset ke konfigurasi standar (hanya memuat data aktif).";
        $this->errorMessage = null;
    }

    public function clearSystemCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Cache::flush();
            $this->successMessage = "Cache aplikasi, query cache, dan compiled view berhasil dibersihkan secara tuntas.";
            $this->errorMessage = null;
        } catch (\Throwable $e) {
            $this->errorMessage = "Gagal membersihkan cache: " . $e->getMessage();
            $this->successMessage = null;
        }
    }

    public function dismissAlert(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    public function getSimrsStatusProperty(): array
    {
        $startTime = microtime(true);
        $connected = false;
        $version = '-';
        $host = config('database.connections.simrs.host', '-');
        $database = config('database.connections.simrs.database', '-');
        $error = null;

        try {
            $result = DB::connection('simrs')->select('SELECT VERSION() as ver');
            $connected = true;
            $version = $result[0]->ver ?? 'Unknown';
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $latency = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'connected' => $connected,
            'host' => $host,
            'database' => $database,
            'version' => $version,
            'latency' => $latency,
            'error' => $error,
        ];
    }

    public function getHospitalProfileProperty(): array
    {
        return ReportHelper::getHospitalProfile();
    }

    public function render()
    {
        return view('pages.configuration', [
            'simrsStatus' => $this->simrsStatus,
            'hospitalProfile' => $this->hospitalProfile,
        ]);
    }
}
