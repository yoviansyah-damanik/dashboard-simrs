<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegisteredPatient;
use App\Helpers\FilterHelper;
use App\Repository\EmergencyReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class EmergencyTest extends TestCase
{
    protected function authenticateUser(): User
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->actingAs($user);
        return $user;
    }

    public function test_emergency_menu_and_routes_accessible(): void
    {
        $this->authenticateUser();

        $responseIndex = $this->get(route('emergency'));
        $responseIndex->assertStatus(200)
            ->assertSee('Gawat Darurat')
            ->assertSee('Data Pasien');

        $responseRecap = $this->get(route('emergency.recap'));
        $responseRecap->assertStatus(200)
            ->assertSee('Gawat Darurat')
            ->assertSee('Rekap');

        $responseReport = $this->get(route('emergency.report'));
        $responseReport->assertStatus(200)
            ->assertSee('Laporan Gawat Darurat')
            ->assertSee('Tindak Lanjut');
    }

    public function test_sidebar_contains_gawat_darurat_menu_items(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('home'));
        $response->assertStatus(200)
            ->assertSee('Gawat Darurat')
            ->assertSee('Data Pasien')
            ->assertSee('Rekap')
            ->assertSee('Laporan');
    }

    public function test_emergency_livewire_components_render(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Emergency\Index::class)
            ->assertStatus(200)
            ->assertSee('Status Pelayanan')
            ->assertSee('Jenis Bayar');

        Livewire::test(\App\Livewire\Emergency\Recap::class)
            ->assertStatus(200)
            ->assertSee('Total Kunjungan IGD')
            ->assertSee('Tindak Lanjut Pasien IGD')
            ->set('mainView', 'chart')
            ->assertStatus(200)
            ->set('mainView', 'list')
            ->assertStatus(200);

        Livewire::test(\App\Livewire\Emergency\Report::class)
            ->assertStatus(200)
            ->assertSee('Total Pasien IGD')
            ->assertSee('Dirawat ke Ranap')
            ->set('activeTab', 'rekap_dokter')
            ->assertStatus(200)
            ->set('activeTab', 'rekap_bayar')
            ->assertStatus(200)
            ->set('activeTab', 'data_pasien')
            ->assertStatus(200);
    }

    public function test_igd_separation_from_outpatient(): void
    {
        // Pastikan dropdown poli dengan excludeIgd tidak memuat IGDK
        $polyclinicsWithIgd = FilterHelper::getPolyclinics(excludeIgd: false);
        $polyclinicsWithoutIgd = FilterHelper::getPolyclinics(excludeIgd: true);

        $hasIgdkInWithout = collect($polyclinicsWithoutIgd)->contains(fn($p) => $p['value'] === RegisteredPatient::KODE_IGD);
        $this->assertFalse($hasIgdkInWithout, 'FilterHelper::getPolyclinics(excludeIgd: true) must NOT contain IGDK');

        // Pastikan repository EmergencyReportRepository berjalan
        $summary = EmergencyReportRepository::getSummary(
            startDate: date('Y-01-01'),
            endDate: date('Y-12-31')
        );

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('total_pasien', $summary);
        $this->assertArrayHasKey('total_ranap', $summary);
        $this->assertArrayHasKey('total_ralan', $summary);
    }
}
