<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\InmReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class InmTest extends TestCase
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

    public function test_inm_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('mutu.inm'));
        $response->assertStatus(200)
            ->assertSee('Indikator Nasional Mutu (INM)')
            ->assertSee('Permenkes RI No. 30 Tahun 2022')
            ->assertSee('Total Indikator Nasional')
            ->assertSee('Indikator Memenuhi Standar')
            ->assertSee('Waktu Tunggu Rawat Jalan');
    }

    public function test_inm_repository_summary_structure(): void
    {
        $years = InmReportRepository::getAvailableYears();
        $this->assertNotEmpty($years);
        $testYear = $years[0];

        $summary = InmReportRepository::getSummary($testYear);

        $this->assertEquals(13, $summary['total_indicators']);
        $this->assertArrayHasKey('achieved_count', $summary);
        $this->assertArrayHasKey('unachieved_count', $summary);
        $this->assertArrayHasKey('average_score', $summary);
        $this->assertArrayHasKey('indicators', $summary);
        $this->assertCount(13, $summary['indicators']);

        // Check specific indicators
        $this->assertArrayHasKey('waktu_tunggu_rajal', $summary['indicators']);
        $this->assertArrayHasKey('visite_dokter', $summary['indicators']);
        $this->assertArrayHasKey('penundaan_operasi', $summary['indicators']);
        $this->assertArrayHasKey('risiko_jatuh', $summary['indicators']);
        $this->assertArrayHasKey('kkt', $summary['indicators']);
        $this->assertArrayHasKey('apd', $summary['indicators']);

        $waktuTunggu = $summary['indicators']['waktu_tunggu_rajal'];
        $this->assertEquals('INM-05', $waktuTunggu['code']);
        $this->assertIsNumeric($waktuTunggu['rate']);
        $this->assertIsBool($waktuTunggu['is_achieved']);
    }

    public function test_inm_livewire_component_renders_and_interacts(): void
    {
        $this->authenticateUser();

        $years = InmReportRepository::getAvailableYears();
        $testYear = $years[0];

        Livewire::test(\App\Livewire\Inm\Index::class)
            ->assertStatus(200)
            ->set('selectedYear', $testYear)
            ->assertStatus(200)
            ->set('selectedCategory', 'pelayanan_klinis')
            ->assertStatus(200)
            ->assertSee('Waktu Tunggu Rawat Jalan')
            ->call('setCategory', 'ppi_keselamatan')
            ->assertStatus(200)
            ->assertSee('Kepatuhan Kebersihan Tangan')
            ->call('setCategory', 'all')
            ->assertStatus(200)
            ->call('openAuditModal', 'waktu_tunggu_rajal')
            ->assertStatus(200)
            ->assertSet('activeIndicatorModal', 'waktu_tunggu_rajal')
            ->call('closeAuditModal')
            ->assertStatus(200)
            ->assertSet('activeIndicatorModal', null)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    public function test_inm_monthly_trends_and_audit_details(): void
    {
        $years = InmReportRepository::getAvailableYears();
        $testYear = $years[0];

        $trends = InmReportRepository::getMonthlyTrends($testYear, 'waktu_tunggu_rajal');
        $this->assertCount(12, $trends['labels']);
        $this->assertCount(12, $trends['data']);
        $this->assertCount(12, $trends['targets']);

        $audit = InmReportRepository::getIndicatorAuditDetails('waktu_tunggu_rajal', $testYear, null, 10);
        $this->assertIsArray($audit);
    }
}
