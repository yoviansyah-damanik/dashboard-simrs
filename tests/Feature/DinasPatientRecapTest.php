<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\InpatientReportRepository;
use App\Repository\MedicalServicesReportRepository;
use App\Repository\OutpatientReportRepository;
use Tests\TestCase;

class DinasPatientRecapTest extends TestCase
{
    protected function getAuthUser(): User
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        return $user;
    }

    /**
     * Memastikan MedicalServicesReportRepository mengembalikan struktur metrik pasien dinas.
     */
    public function test_medical_services_repository_includes_dinas_metrics(): void
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
        $reportData = MedicalServicesReportRepository::getSummary($startDate, $endDate);
        $this->assertArrayHasKey('dinas', $reportData);
        $dinas = $reportData['dinas'];
        $this->assertArrayHasKey('total', $dinas);
        $this->assertArrayHasKey('poli', $dinas);
        $this->assertArrayHasKey('igd', $dinas);
        $this->assertArrayHasKey('ranap', $dinas);
        $this->assertArrayHasKey('tni', $dinas);
        $this->assertArrayHasKey('polri', $dinas);

        $this->assertArrayHasKey('categories', $dinas);
        $this->assertArrayHasKey('satuan', $dinas);
    }

    /**
     * Memastikan OutpatientReportRepository mengembalikan rekapitulasi pasien dinas rawat jalan.
     */
    public function test_outpatient_repository_includes_dinas_breakdown(): void
    {
        $breakdown = OutpatientReportRepository::getDinasBreakdown();
        $this->assertArrayHasKey('summary', $breakdown);
        $this->assertArrayHasKey('polyclinics', $breakdown);
        $this->assertArrayHasKey('categories', $breakdown);
        $this->assertArrayHasKey('satuan', $breakdown);
    }

    /**
     * Memastikan InpatientReportRepository mengembalikan rekapitulasi pasien dinas rawat inap.
     */
    public function test_inpatient_repository_includes_dinas_breakdown(): void
    {
        $summary = InpatientReportRepository::getSummary();
        $this->assertArrayHasKey('total_dinas', $summary);
        $this->assertArrayHasKey('total_tni', $summary);
        $this->assertArrayHasKey('total_polri', $summary);

        $breakdown = InpatientReportRepository::getDinasBreakdown();
        $this->assertArrayHasKey('summary', $breakdown);
        $this->assertArrayHasKey('wards', $breakdown);
        $this->assertArrayHasKey('categories', $breakdown);
        $this->assertArrayHasKey('satuan', $breakdown);
    }

    /**
     * Memastikan halaman laporan ralan dan ranap dapat diakses dan merender tab dinas.
     */
    public function test_authenticated_user_can_access_dinas_tabs(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        // Rawat Jalan
        $resRalan = $this->get(route('outpatient.report'));
        $resRalan->assertStatus(200);
        $resRalan->assertSee('Rekap Pasien Dinas', false);

        // Rawat Inap
        $resRanap = $this->get(route('inpatient.report'));
        $resRanap->assertStatus(200);
        $resRanap->assertSee('Rekap Pasien Dinas', false);

        // Ringkasan Pelayanan Medis
        $resMed = $this->get(route('medical-services.summary'));
        $resMed->assertStatus(200);
        $resMed->assertSee('Pasien Dinas', false);
    }

    /**
     * Memastikan komponen Livewire dapat berganti tab ke rekap_dinas tanpa error stdClass.
     */
    public function test_livewire_can_switch_to_rekap_dinas_tab(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        \Livewire\Livewire::test(\App\Livewire\Inpatient\Report::class)
            ->assertStatus(200)
            ->call('switchTab', 'rekap_dinas')
            ->assertStatus(200)
            ->assertSee('Sebaran Pasien Dinas per Bangsal');

        \Livewire\Livewire::test(\App\Livewire\Outpatient\Report::class)
            ->assertStatus(200)
            ->call('switchTab', 'rekap_dinas')
            ->assertStatus(200)
            ->assertSee('Rekapitulasi Kunjungan Pasien Dinas per Poliklinik');
    }
}
