<?php

namespace Tests\Feature;

use App\Models\User;
use App\Livewire\Icd\ProcedureRecap;
use App\Repository\ProcedureReportRepository;
use App\View\Components\Sidebar;
use Livewire\Livewire;
use Tests\TestCase;

class ProcedureRecapTest extends TestCase
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
     * Memastikan halaman rekap data tindakan dapat diakses dengan sukses di URL /laporan-data-tindakan (HTTP 200).
     */
    public function test_authenticated_user_can_access_procedure_recap_page(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $this->assertStringContainsString('/laporan-data-tindakan', route('icd.procedure'));

        $response = $this->get('/laporan-data-tindakan');
        $response->assertStatus(200);
        $response->assertSee('Rekap Data Tindakan & Prosedur Medis', false);
        $response->assertSee('Parameter Filter Laporan', false);
        $response->assertSee('10 Besar Tindakan Terbanyak', false);
        $response->assertSee('Tabel Rekapitulasi Prosedur Medis', false);
        $response->assertSee('Cetak PDF Data', false);
    }

    /**
     * Memastikan rute alias legacy /icd/icd-9 dan /rekap-tindakan mengarahkan ke halaman /laporan-data-tindakan.
     */
    public function test_procedure_recap_alias_route_redirects(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $response1 = $this->get('/icd/icd-9');
        $response1->assertRedirect(route('icd.procedure'));

        $response2 = $this->get('/rekap-tindakan');
        $response2->assertRedirect(route('icd.procedure'));
    }

    /**
     * Memastikan menu sidebar memuat Rekap Data Tindakan.
     */
    public function test_sidebar_contains_procedure_recap_menu(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $sidebar = (new Sidebar())->render();
        $menus = $sidebar->getData()['menus'] ?? [];

        $laporan = collect($menus)->firstWhere('title', 'Laporan');
        $this->assertNotNull($laporan, 'Grup menu Laporan harus ada di sidebar.');

        $items = collect($laporan['items']);
        $procedureMenu = $items->firstWhere('title', 'Rekap Data Tindakan');
        $this->assertNotNull($procedureMenu, 'Menu Rekap Data Tindakan harus terdaftar di sidebar.');
        $this->assertEquals(route('icd.procedure'), $procedureMenu['href']);
        $this->assertEquals('i-ph-syringe', $procedureMenu['icon']);
    }

    /**
     * Memastikan agregasi ProcedureReportRepository bekerja dan memisahkan Poli serta IGD.
     */
    public function test_procedure_report_repository_aggregations_and_ralan_separation(): void
    {
        $summary = ProcedureReportRepository::getSummary([
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
        ]);

        $this->assertArrayHasKey('total_tindakan', $summary);
        $this->assertArrayHasKey('total_prosedur_unik', $summary);
        $this->assertArrayHasKey('total_pasien_unik', $summary);
        $this->assertArrayHasKey('utama', $summary);
        $this->assertArrayHasKey('sekunder', $summary);
        $this->assertArrayHasKey('poli', $summary);
        $this->assertArrayHasKey('igd', $summary);
        $this->assertArrayHasKey('ralan', $summary);
        $this->assertArrayHasKey('ranap', $summary);

        $top = ProcedureReportRepository::getTopProcedures([
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
        ], 5);

        $this->assertArrayHasKey('labels', $top);
        $this->assertArrayHasKey('items', $top);
        if (!empty($top['items'])) {
            $this->assertArrayHasKey('poli', $top['items'][0]);
            $this->assertArrayHasKey('igd', $top['items'][0]);
            $this->assertArrayHasKey('ranap', $top['items'][0]);
            $this->assertArrayHasKey('utama', $top['items'][0]);
            $this->assertArrayHasKey('sekunder', $top['items'][0]);
        }
    }

    /**
     * Memastikan interaksi modal drilldown kasus pasien pada Livewire berjalan benar.
     */
    public function test_procedure_recap_drilldown_modal_interaction(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(ProcedureRecap::class)
            ->assertSet('showDetailModal', false)
            ->call('openDetail', '23.70', 'Root canal, not otherwise specified')
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedCode', '23.70')
            ->assertSet('selectedDescription', 'Root canal, not otherwise specified')
            ->call('closeDetail')
            ->assertSet('showDetailModal', false);
    }

    /**
     * Memastikan ekspor PDF data server-side berfungsi dan menghasilkan file unduhan stream.
     */
    public function test_procedure_recap_export_pdf(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(ProcedureRecap::class)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    /**
     * Memastikan filter kategori bab ICD-9 dan status rawat dapat diset pada Livewire.
     */
    public function test_procedure_recap_filtering(): void
    {
        $categories = ProcedureReportRepository::getProcedureCategories();
        $this->assertArrayHasKey('gigi', $categories);
        $this->assertArrayHasKey('injeksi', $categories);
        $this->assertArrayHasKey('radiologi', $categories);
        $this->assertArrayHasKey('obgyn', $categories);

        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(ProcedureRecap::class)
            ->set('category', 'gigi')
            ->assertSet('category', 'gigi')
            ->set('serviceStatus', 'Poli')
            ->assertSet('serviceStatus', 'Poli')
            ->set('priority', '1')
            ->assertSet('priority', '1')
            ->set('gender', 'L')
            ->assertSet('gender', 'L')
            ->call('resetFilters')
            ->assertSet('category', 'all')
            ->assertSet('serviceStatus', 'all')
            ->assertSet('priority', 'all')
            ->assertSet('gender', 'all');
    }

    /**
     * Memastikan pergantian mode periode menyinkronkan rentang tanggal dan mengirim event pembaruan grafik.
     */
    public function test_period_type_switching_syncs_dates_and_dispatches_chart_event(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(ProcedureRecap::class)
            ->assertSet('periodType', 'month')
            ->call('setPeriodType', 'range')
            ->assertSet('periodType', 'range')
            ->assertDispatched('procedure-chart-updated')
            ->call('setPeriodType', 'month')
            ->assertSet('periodType', 'month')
            ->assertDispatched('procedure-chart-updated');
    }
}
