<?php

namespace Tests\Feature;

use App\Models\User;
use App\Livewire\Icd\Recap;
use App\Repository\DiseaseReportRepository;
use App\View\Components\Sidebar;
use Livewire\Livewire;
use Tests\TestCase;

class DiseaseRecapTest extends TestCase
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
     * Memastikan halaman rekap data penyakit dapat diakses dengan sukses di URL /laporan-data-penyakit (HTTP 200).
     */
    public function test_authenticated_user_can_access_disease_recap_page(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $this->assertStringContainsString('/laporan-data-penyakit', route('icd'));

        $response = $this->get('/laporan-data-penyakit');
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Data Penyakit', false);
        $response->assertSee('Filter Data Penyakit', false);
        $response->assertSee('10 Besar Penyakit Terbanyak', false);
        $response->assertSee('Daftar Rekapitulasi Morbiditas Penyakit', false);
        $response->assertSee('Cetak PDF Data', false);
    }

    /**
     * Memastikan rute alias legacy /icd dan /rekap-penyakit mengarahkan ke halaman /laporan-data-penyakit.
     */
    public function test_disease_recap_alias_route_redirects(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $legacyResponse = $this->get('/icd');
        $legacyResponse->assertRedirect(route('icd'));

        $response = $this->get(route('disease.recap'));
        $response->assertRedirect(route('icd'));
    }

    /**
     * Memastikan menu sidebar Laporan memuat Rekap Data Penyakit.
     */
    public function test_sidebar_contains_disease_recap_menu(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $sidebar = (new Sidebar())->render();
        $menus = $sidebar->getData()['menus'] ?? [];

        $laporan = collect($menus)->firstWhere('title', 'Laporan');
        $this->assertNotNull($laporan, 'Grup menu Laporan harus ada di sidebar.');

        $items = collect($laporan['items']);
        $diseaseMenu = $items->firstWhere('title', 'Rekap Data Penyakit');
        $this->assertNotNull($diseaseMenu, 'Menu Rekap Data Penyakit harus terdaftar di sidebar.');
        $this->assertEquals('i-ph-first-aid', $diseaseMenu['icon']);
    }

    /**
     * Memastikan DiseaseReportRepository mengembalikan data ringkasan dan top penyakit yang valid.
     */
    public function test_disease_repository_computes_data(): void
    {
        $years = DiseaseReportRepository::getAvailableYears();
        $this->assertIsArray($years);
        $this->assertNotEmpty($years);

        $summary = DiseaseReportRepository::getSummary([
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
        ]);

        $this->assertArrayHasKey('total_kasus', $summary);
        $this->assertArrayHasKey('total_penyakit_unik', $summary);
        $this->assertArrayHasKey('kasus_baru', $summary);
        $this->assertArrayHasKey('kasus_lama', $summary);
        $this->assertArrayHasKey('poli', $summary);
        $this->assertArrayHasKey('igd', $summary);
        $this->assertArrayHasKey('ralan', $summary);
        $this->assertArrayHasKey('ranap', $summary);

        $top = DiseaseReportRepository::getTopDiseases([
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
        ], 5);

        $this->assertArrayHasKey('labels', $top);
        $this->assertArrayHasKey('items', $top);
        if (!empty($top['items'])) {
            $this->assertArrayHasKey('poli', $top['items'][0]);
            $this->assertArrayHasKey('igd', $top['items'][0]);
            $this->assertArrayHasKey('ranap', $top['items'][0]);
        }
    }

    /**
     * Memastikan interaksi modal drilldown kasus pasien pada Livewire berjalan benar.
     */
    public function test_disease_recap_drilldown_modal_interaction(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->assertSet('showDetailModal', false)
            ->call('openDetail', 'I10', 'Hipertensi Primer')
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedDiseaseCode', 'I10')
            ->assertSet('selectedDiseaseName', 'Hipertensi Primer')
            ->call('closeDetail')
            ->assertSet('showDetailModal', false);
    }

    /**
     * Memastikan ekspor PDF data server-side berfungsi dan menghasilkan file unduhan stream.
     */
    public function test_disease_recap_export_pdf(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    /**
     * Memastikan filter kasus khusus (ISK dan penyakit surveilans lain) tersedia dan dapat memfilter data.
     */
    public function test_special_case_filtering_including_uti_isk(): void
    {
        $specialCases = DiseaseReportRepository::getSpecialCases();
        $this->assertArrayHasKey('isk', $specialCases);
        $this->assertArrayHasKey('ispa', $specialCases);
        $this->assertArrayHasKey('tb', $specialCases);
        $this->assertArrayHasKey('dbd', $specialCases);
        $this->assertArrayHasKey('jantung', $specialCases);
        $this->assertContains('N39.0%', $specialCases['isk']['patterns']);
        $this->assertContains('I20%', $specialCases['jantung']['patterns']);
        $this->assertContains('I50%', $specialCases['jantung']['patterns']);

        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->set('specialCase', 'isk')
            ->assertSee('Infeksi Saluran Kemih', false)
            ->assertSet('specialCase', 'isk')
            ->set('specialCase', 'jantung')
            ->assertSee('Penyakit Jantung', false)
            ->assertSet('specialCase', 'jantung')
            ->set('specialCase', 'all')
            ->assertSet('specialCase', 'all');
    }

    /**
     * Memastikan pergantian mode periode menyinkronkan tanggal dan mengirimkan event pembaruan grafik.
     */
    public function test_period_type_switching_syncs_dates_and_dispatches_chart_event(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->assertSet('periodType', 'month')
            ->call('setPeriodType', 'range')
            ->assertSet('periodType', 'range')
            ->assertDispatched('disease-chart-updated')
            ->call('setPeriodType', 'month')
            ->assertSet('periodType', 'month')
            ->assertDispatched('disease-chart-updated');
    }

    /**
     * Memastikan DiseaseReportRepository::getMonthlyBreakdown mengembalikan 12 bulan dan agregasi data lengkap.
     */
    public function test_monthly_breakdown_repository_computes_twelve_months_and_totals(): void
    {
        $breakdown = DiseaseReportRepository::getMonthlyBreakdown([
            'year' => (int) date('Y'),
        ]);

        $this->assertArrayHasKey('months', $breakdown);
        $this->assertArrayHasKey('totals', $breakdown);
        $this->assertCount(12, $breakdown['months']);

        $firstMonth = $breakdown['months'][1];
        $this->assertEquals('Januari', $firstMonth['nama_bulan']);
        $this->assertArrayHasKey('poli', $firstMonth);
        $this->assertArrayHasKey('igd', $firstMonth);
        $this->assertArrayHasKey('ralan', $firstMonth);
        $this->assertArrayHasKey('ranap', $firstMonth);
        $this->assertArrayHasKey('kasus_baru', $firstMonth);
        $this->assertArrayHasKey('kasus_lama', $firstMonth);
        $this->assertArrayHasKey('total_kasus', $firstMonth);

        $totals = $breakdown['totals'];
        $this->assertArrayHasKey('total_kasus', $totals);
        $this->assertArrayHasKey('avg_kasus_per_bulan', $totals);
        $this->assertArrayHasKey('poli', $totals);
        $this->assertArrayHasKey('igd', $totals);
        $this->assertArrayHasKey('ralan', $totals);
        $this->assertArrayHasKey('ranap', $totals);
    }

    /**
     * Memastikan mode tahunan (semua bulan) merender tabel rekapitulasi data penyakit per bulan.
     */
    public function test_yearly_mode_renders_monthly_breakdown_table(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->set('periodType', 'month')
            ->set('month', '')
            ->assertSet('isYearly', true)
            ->assertSee('Rekapitulasi Data Penyakit Per Bulan', false)
            ->assertSee('Total Setahun', false)
            ->call('selectMonth', 3)
            ->assertSet('month', 3)
            ->assertSet('isYearly', false)
            ->assertDontSee('Rekapitulasi Data Penyakit Per Bulan', false);
    }
}
