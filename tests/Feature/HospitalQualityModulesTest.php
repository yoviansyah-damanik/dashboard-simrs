<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\IkpReportRepository;
use App\Repository\InmReportRepository;
use App\Repository\KlpcmReportRepository;
use App\Repository\PpiReportRepository;
use App\Repository\SpmReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class HospitalQualityModulesTest extends TestCase
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

    public function test_all_mutu_routes_accessible(): void
    {
        $this->authenticateUser();

        // 1. INM
        $this->get(route('mutu.inm'))
            ->assertStatus(200)
            ->assertSee('Indikator Nasional Mutu (INM)');

        // 2. IKP
        $this->get(route('mutu.ikp'))
            ->assertStatus(200)
            ->assertSee('Insiden Keselamatan Pasien (IKP)')
            ->assertSee('Matriks Grading Risiko');

        // 3. PPI
        $this->get(route('mutu.ppi'))
            ->assertStatus(200)
            ->assertSee('Surveilans PPI')
            ->assertSee('Infeksi Nosokomial')
            ->assertSee('Infeksi Saluran Kemih');

        // 4. SPM
        $this->get(route('mutu.spm'))
            ->assertStatus(200)
            ->assertSee('Standar Pelayanan Minimal (SPM) Rumah Sakit')
            ->assertSee('Instalasi Gawat Darurat (IGD)');

        // 5. KLPCM
        $this->get(route('mutu.klpcm'))
            ->assertStatus(200)
            ->assertSee('Kelengkapan Rekam Medis')
            ->assertSee('KLPCM')
            ->assertSee('Analisis Kuantitatif');
    }

    public function test_sidebar_contains_active_mutu_pillars_and_hides_klpcm(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('mutu.inm'));
        $response->assertStatus(200)
            ->assertSee('Indikator Mutu (INM)')
            ->assertSee('Keselamatan Pasien (IKP)')
            ->assertSee('Surveilans PPI')
            ->assertSee('Standar Pelayanan (SPM)')
            ->assertDontSee('Rekam Medis (KLPCM)');
    }

    public function test_ikp_repository_and_livewire(): void
    {
        $this->authenticateUser();

        $summary = IkpReportRepository::getSummary(2026);
        $this->assertArrayHasKey('counts', $summary);
        $this->assertArrayHasKey('grading', $summary);

        Livewire::test(\App\Livewire\Mutu\Ikp::class)
            ->assertStatus(200)
            ->call('filterJenis', 'ktd')
            ->assertStatus(200)
            ->call('filterJenis', 'all')
            ->assertStatus(200);
    }

    public function test_ppi_repository_and_livewire(): void
    {
        $this->authenticateUser();

        $summary = PpiReportRepository::getSummary(2026);
        $this->assertArrayHasKey('indicators', $summary);
        $this->assertArrayHasKey('isk', $summary['indicators']);
        $this->assertArrayHasKey('pleb', $summary['indicators']);

        Livewire::test(\App\Livewire\Mutu\Ppi::class)
            ->assertStatus(200)
            ->call('loadTrends')
            ->assertStatus(200);
    }

    public function test_spm_repository_and_livewire(): void
    {
        $this->authenticateUser();

        $summary = SpmReportRepository::getSummary(2026);
        $this->assertArrayHasKey('sections', $summary);
        $this->assertArrayHasKey('farmasi', $summary['sections']);

        Livewire::test(\App\Livewire\Mutu\Spm::class)
            ->assertStatus(200)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    public function test_klpcm_repository_and_livewire(): void
    {
        $this->authenticateUser();

        $summary = KlpcmReportRepository::getSummary(2026);
        $this->assertArrayHasKey('total_berkas', $summary);
        $this->assertArrayHasKey('angka_klpcm', $summary);

        $docList = KlpcmReportRepository::getDoctorComplianceList(2026);
        $this->assertIsArray($docList);

        Livewire::test(\App\Livewire\Mutu\KlpcmReport::class)
            ->assertStatus(200);
    }

    public function test_taskid_helper_and_bpjs_waiting_time_evaluations(): void
    {
        $this->authenticateUser();

        // Check helper availability
        $this->assertTrue(\App\Helpers\TaskidHelper::isAvailable());

        // Check task intervals on real data
        $poliTunggu = \App\Helpers\TaskidHelper::getWaktuTungguPoli(2025);
        $this->assertArrayHasKey('total', $poliTunggu);
        $this->assertArrayHasKey('tepat', $poliTunggu);
        $this->assertArrayHasKey('rate', $poliTunggu);
        $this->assertArrayHasKey('avg_menit', $poliTunggu);

        $poliLayan = \App\Helpers\TaskidHelper::getWaktuPelayananPoli(2025);
        $this->assertArrayHasKey('rate', $poliLayan);

        $farmasiTunggu = \App\Helpers\TaskidHelper::getWaktuTungguFarmasi(2025);
        $this->assertArrayHasKey('rate', $farmasiTunggu);

        $farmasiLayan = \App\Helpers\TaskidHelper::getWaktuPelayananFarmasi(2025);
        $this->assertArrayHasKey('rate', $farmasiLayan);

        $admisiTunggu = \App\Helpers\TaskidHelper::getWaktuTungguAdmisi(2025);
        $this->assertArrayHasKey('rate', $admisiTunggu);

        $admisiLayan = \App\Helpers\TaskidHelper::getWaktuPelayananAdmisi(2025);
        $this->assertArrayHasKey('rate', $admisiLayan);

        // Verify SPM sections contain admisi, ralan, and farmasi
        $spm = SpmReportRepository::getSummary(2025);
        $this->assertArrayHasKey('admisi', $spm['sections']);
        $this->assertArrayHasKey('ralan', $spm['sections']);
        $this->assertArrayHasKey('farmasi', $spm['sections']);

        // Check drilldown logs
        $logs = \App\Helpers\TaskidHelper::getDetailLogs('3', '4', 2025, null, 60, 5);
        $this->assertIsArray($logs);
        if (!empty($logs)) {
            $this->assertArrayHasKey('no_rawat', $logs[0]);
            $this->assertArrayHasKey('nilai', $logs[0]);
            $this->assertArrayHasKey('is_patuh', $logs[0]);
        }
    }

    public function test_all_report_pdf_exports_functional(): void
    {
        $this->authenticateUser();

        // 1. PPI PDF
        Livewire::test(\App\Livewire\Mutu\Ppi::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 2. IKP PDF
        Livewire::test(\App\Livewire\Mutu\Ikp::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 3. KLPCM PDF
        Livewire::test(\App\Livewire\Mutu\KlpcmReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 4. Layanan Medis PDF
        Livewire::test(\App\Livewire\MedicalServices\Summary::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 5. Layanan Penunjang PDF
        Livewire::test(\App\Livewire\Ancillary\Summary::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 6. Matriks Indikator Tahunan PDF
        Livewire::test(\App\Livewire\IndicatorMatrix\Index::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 7. Matriks Penunjang Tahunan PDF
        Livewire::test(\App\Livewire\Ancillary\YearlyMatrix::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 8. Matriks Farmasi Tahunan PDF
        Livewire::test(\App\Livewire\Pharmacy\YearlyMatrix::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 9. IGD PDF
        Livewire::test(\App\Livewire\Emergency\Report::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 10. Pasien Kunjungan PDF
        Livewire::test(\App\Livewire\PatientReport\Index::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        // 11. Stok Farmasi PDF
        Livewire::test(\App\Livewire\Pharmacy\Stock::class)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }
}
