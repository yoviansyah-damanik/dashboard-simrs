<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\OutpatientReportRepository;
use Tests\TestCase;

class OutpatientMomGrowthTest extends TestCase
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
     * Memastikan repository MoM Growth dapat menghitung data harian untuk periode bulanan.
     */
    public function test_repository_computes_daily_mom_growth_for_monthly_period(): void
    {
        $result = OutpatientReportRepository::getOutpatientMomGrowth([
            'period' => 'monthly',
            'year' => 2026,
            'month' => 10,
        ]);

        $this->assertEquals('daily', $result['period_type']);
        $this->assertEquals(2026, $result['year']);
        $this->assertEquals(10, $result['month']);
        $this->assertEquals(31, $result['days_in_month']);

        $this->assertArrayHasKey('kpi', $result);
        $this->assertArrayHasKey('total_current', $result['kpi']);
        $this->assertArrayHasKey('poli_current', $result['kpi']);
        $this->assertArrayHasKey('igd_current', $result['kpi']);
        $this->assertArrayHasKey('growth_total', $result['kpi']);

        $this->assertCount(31, $result['timeline']);
        $this->assertArrayHasKey(1, $result['timeline']);
        $this->assertArrayHasKey('dod_growth', $result['timeline'][1]);
        $this->assertArrayHasKey('mom_date_growth', $result['timeline'][1]);

        // Matriks Poliklinik & IGD
        $this->assertArrayHasKey('polyclinic_matrix', $result);
        $this->assertIsArray($result['polyclinic_matrix']['items']);

        // Matriks Dokter DPJP
        $this->assertArrayHasKey('doctor_matrix', $result);
        $this->assertIsArray($result['doctor_matrix']['items']);
    }

    /**
     * Memastikan repository MoM Growth dapat menghitung data bulanan untuk periode tahunan.
     */
    public function test_repository_computes_monthly_mom_growth_for_yearly_period(): void
    {
        $result = OutpatientReportRepository::getOutpatientMomGrowth([
            'period' => 'yearly',
            'year' => 2026,
        ]);

        $this->assertEquals('monthly', $result['period_type']);
        $this->assertEquals(2026, $result['year']);
        $this->assertCount(12, $result['timeline']);

        $this->assertArrayHasKey('kpi', $result);
        $this->assertArrayHasKey('total_current', $result['kpi']);
        $this->assertArrayHasKey('growth_total', $result['kpi']);

        $this->assertArrayHasKey('polyclinic_matrix', $result);
        $this->assertArrayHasKey('doctor_matrix', $result);
    }

    /**
     * Memastikan tab mom_growth pada halaman Laporan Rawat Jalan dapat diakses dan di-render oleh Livewire.
     */
    public function test_authenticated_user_can_access_outpatient_report_mom_tab(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        \Livewire\Livewire::test(\App\Livewire\Outpatient\Report::class)
            ->set('activeTab', 'mom_growth')
            ->assertSet('activeTab', 'mom_growth')
            ->assertSee('MoM Growth', false)
            ->assertSee('Timeline Kunjungan Harian', false)
            ->call('setMomView', 'polyclinic')
            ->assertSet('momView', 'polyclinic')
            ->assertSee('Matriks Pertumbuhan Kunjungan per Poliklinik', false)
            ->call('setMomView', 'doctor')
            ->assertSet('momView', 'doctor')
            ->assertSee('Matriks Pertumbuhan Kunjungan per Dokter DPJP', false);
    }

    /**
     * Memastikan ekspor PDF MoM Growth berhasil menghasilkan file stream.
     */
    public function test_outpatient_mom_growth_pdf_export(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $response = \Livewire\Livewire::test(\App\Livewire\Outpatient\Report::class)
            ->set('activeTab', 'mom_growth')
            ->call('exportMomPdf');

        $this->assertNotNull($response);
    }

    /**
     * Memastikan ekspor Excel MoM Growth berhasil menghasilkan file stream.
     */
    public function test_outpatient_mom_growth_excel_export(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $response = \Livewire\Livewire::test(\App\Livewire\Outpatient\Report::class)
            ->set('activeTab', 'mom_growth')
            ->call('exportMomExcel');

        $this->assertNotNull($response);
    }
}
