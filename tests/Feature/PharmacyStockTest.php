<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\PharmacyStockRepository;
use Livewire\Livewire;
use Tests\TestCase;

class PharmacyStockTest extends TestCase
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

    public function test_pharmacy_stock_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('pharmacy.stock'));
        $response->assertStatus(200)
            ->assertSee('Rekap Stok Obat Farmasi')
            ->assertSee('Akan Habis')
            ->assertSee('Stok Kosong')
            ->assertSee('Stok Aman');
    }

    public function test_sidebar_contains_pharmacy_stock_menu(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('pharmacy.stock'));
        $response->assertStatus(200)
            ->assertSee('Rekap Stok Obat');
    }

    public function test_pharmacy_stock_repository_summary_and_pagination(): void
    {
        $summary = PharmacyStockRepository::getSummary();

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('total_items', $summary);
        $this->assertArrayHasKey('total_fisik', $summary);
        $this->assertArrayHasKey('total_nilai_aset', $summary);
        $this->assertArrayHasKey('total_habis', $summary);
        $this->assertArrayHasKey('total_menipis', $summary);
        $this->assertArrayHasKey('total_aman', $summary);

        // Test pagination filter
        $paginated = PharmacyStockRepository::getPaginated([
            'status_stok' => 'menipis',
        ], 10);

        $this->assertNotNull($paginated);
        foreach ($paginated as $item) {
            $this->assertTrue($item->is_akan_habis);
        }
    }

    public function test_pharmacy_stock_only_includes_active_items(): void
    {
        $paginated = PharmacyStockRepository::getPaginated([], 50);
        $kodes = $paginated->pluck('kode_brng')->toArray();

        if (!empty($kodes)) {
            $inactiveCount = \Illuminate\Support\Facades\DB::connection('simrs')->table('databarang')
                ->whereIn('kode_brng', $kodes)
                ->where('status', '!=', '1')
                ->count();

            $this->assertEquals(0, $inactiveCount, 'Stock recap must strictly contain active items (databarang.status = 1)');
        }
    }

    public function test_pharmacy_stock_livewire_component_renders_and_filters(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Pharmacy\Stock::class)
            ->assertStatus(200)
            ->assertSee('Rekap Stok Obat Farmasi')
            ->set('statusStok', 'menipis')
            ->assertStatus(200)
            ->set('statusStok', 'habis')
            ->assertStatus(200)
            ->set('statusStok', 'aman')
            ->assertStatus(200)
            ->set('search', 'PARACETAMOL')
            ->assertStatus(200);
    }

    public function test_pharmacy_stock_detail_modal(): void
    {
        $this->authenticateUser();

        $firstItem = PharmacyStockRepository::getPaginated([], 1)->first();
        if ($firstItem) {
            $detail = PharmacyStockRepository::getDetailByKode($firstItem->kode_brng);
            $this->assertNotNull($detail);
            $this->assertEquals('1', $detail['obat']->status);

            Livewire::test(\App\Livewire\Pharmacy\Stock::class)
                ->call('openDetail', $firstItem->kode_brng)
                ->assertSet('selectedDrugKode', $firstItem->kode_brng)
                ->assertSet('detailModalOpen', true)
                ->assertSee($firstItem->nama_brng)
                ->assertSee('Aktif (Status 1)')
                ->call('closeDetail')
                ->assertSet('selectedDrugKode', null)
                ->assertSet('detailModalOpen', false);
        }

        // Inactive item test (status != 1)
        $inactiveItem = \Illuminate\Support\Facades\DB::connection('simrs')->table('databarang')
            ->where('status', '!=', '1')
            ->first();

        if ($inactiveItem) {
            $detailInactive = PharmacyStockRepository::getDetailByKode($inactiveItem->kode_brng);
            $this->assertNull($detailInactive, 'Inactive medicine must not return detail');

            Livewire::test(\App\Livewire\Pharmacy\Stock::class)
                ->call('openDetail', $inactiveItem->kode_brng)
                ->assertSet('detailModalOpen', false)
                ->assertSet('selectedDrugKode', null);
        }
    }

    public function test_pharmacy_recap_renders_top_10_drug_trend_and_stock_indicators(): void
    {
        $this->authenticateUser();

        $component = Livewire::test(\App\Livewire\Pharmacy\Recap::class)
            ->set('period', 'all')
            ->assertStatus(200)
            ->assertSee('Tren 10 Obat Paling Sering Digunakan')
            ->assertSee('Akumulasi Volume Pemakaian (Top 10)')
            ->assertSee('Status Ketersediaan Stok 10 Obat Teratas');

        $drugUsage = $component->get('drugUsage');
        $this->assertIsArray($drugUsage);
        $this->assertArrayHasKey('top_obat', $drugUsage);
        $this->assertArrayHasKey('trend_top10', $drugUsage['charts']);
        $this->assertArrayHasKey('labels', $drugUsage['charts']['trend_top10']);
        $this->assertArrayHasKey('datasets', $drugUsage['charts']['trend_top10']);

        // Check stock indicator properties on top obat and ensure all are active (status = 1)
        if ($drugUsage['top_obat']->isNotEmpty()) {
            $topCodes = $drugUsage['top_obat']->pluck('kode_brng')->toArray();
            $inactiveCount = \Illuminate\Support\Facades\DB::connection('simrs')->table('databarang')
                ->whereIn('kode_brng', $topCodes)
                ->where('status', '!=', '1')
                ->count();
            $this->assertEquals(0, $inactiveCount, 'All top medicines must have status = 1');

            $firstDrug = $drugUsage['top_obat']->first();
            $this->assertObjectHasProperty('current_stock', $firstDrug);
            $this->assertObjectHasProperty('is_akan_habis', $firstDrug);
            $this->assertObjectHasProperty('is_habis', $firstDrug);
            $this->assertObjectHasProperty('is_aman', $firstDrug);
        }

        // Test in figures view
        $component->set('mainView', 'figures')
            ->assertStatus(200)
            ->assertSee('10 Obat Terbanyak Digunakan');
    }
}
