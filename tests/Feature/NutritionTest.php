<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\NutritionRepository;
use Livewire\Livewire;
use Tests\TestCase;

class NutritionTest extends TestCase
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

    public function test_nutrition_diet_orders_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('nutrition'));
        $response->assertStatus(200)
            ->assertSee('Permintaan Diet Pasien')
            ->assertSee('Buka Rekap Permintaan Diet')
            ->assertSee('Total Permintaan');
    }

    public function test_sidebar_contains_nutrition_submenus(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('nutrition'));
        $response->assertStatus(200)
            ->assertSee('Permintaan Diet')
            ->assertSee('Rekap Permintaan Diet');
    }

    public function test_nutrition_index_filters_and_tabs(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Nutrition\Index::class)
            ->assertStatus(200)
            ->assertSee('Permintaan Diet Pasien')
            ->set('waktu', 'Pagi')
            ->assertStatus(200)
            ->set('waktu', 'Siang')
            ->assertStatus(200)
            ->set('waktu', 'Sore')
            ->assertStatus(200)
            ->set('search', 'MB')
            ->assertStatus(200)
            ->set('period', 'last_7_days')
            ->assertStatus(200)
            ->set('activeTab', 'asuhan')
            ->assertStatus(200)
            ->assertSee('Evaluasi Asuhan Gizi');
    }

    public function test_nutrition_recap_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('nutrition.recap'));
        $response->assertStatus(200)
            ->assertSee('Rekap Permintaan Diet')
            ->assertSee('Total Porsi Diet')
            ->assertSee('Pasien Terlayani');
    }

    public function test_nutrition_recap_livewire_renders_and_switches_views(): void
    {
        $this->authenticateUser();

        $component = Livewire::test(\App\Livewire\Nutrition\Recap::class)
            ->set('period', 'all')
            ->assertStatus(200)
            ->assertSee('Total Porsi Diet')
            ->assertSee('Tren Jumlah Permintaan Diet')
            ->assertSee('Distribusi Waktu Makan')
            ->assertSee('10 Jenis Diet Paling Banyak Diminta')
            ->assertSee('10 Bangsal / Ruangan Penerima Diet Terbanyak')
            ->set('mainView', 'figures')
            ->assertStatus(200)
            ->assertSee('Distribusi Waktu Makan')
            ->assertSee('10 Jenis Diet Paling Banyak Diminta')
            ->assertSee('Sebaran Bangsal Penerima Diet Terbanyak');

        $summary = $component->get('summary');
        $this->assertIsArray($summary);
        $this->assertArrayHasKey('summary', $summary);
        $this->assertArrayHasKey('charts', $summary);
        $this->assertArrayHasKey('tables', $summary);
        $this->assertArrayHasKey('total_porsi', $summary['summary']);
        $this->assertArrayHasKey('total_pasien', $summary['summary']);
        $this->assertArrayHasKey('pagi', $summary['summary']);
        $this->assertArrayHasKey('siang', $summary['summary']);
        $this->assertArrayHasKey('sore', $summary['summary']);
        $this->assertArrayHasKey('trend', $summary['charts']);
        $this->assertArrayHasKey('waktu', $summary['charts']);
        $this->assertArrayHasKey('top_diet', $summary['charts']);
        $this->assertArrayHasKey('bangsal', $summary['charts']);
    }

    public function test_nutrition_repository_diet_queries(): void
    {
        $summary = NutritionRepository::getDietSummary('2026-01-01', '2026-12-31');
        $this->assertIsArray($summary);
        $this->assertArrayHasKey('total_porsi', $summary);
        $this->assertArrayHasKey('total_pasien', $summary);
        $this->assertArrayHasKey('pagi', $summary);
        $this->assertArrayHasKey('siang', $summary);
        $this->assertArrayHasKey('sore', $summary);

        $orders = NutritionRepository::getDietOrders([
            'startDate' => '2026-01-01',
            'endDate' => '2026-12-31',
        ], 10);
        $this->assertNotNull($orders);

        $recap = NutritionRepository::getDietRecap('2026-01-01', '2026-12-31');
        $this->assertIsArray($recap);
        $this->assertArrayHasKey('summary', $recap);
        $this->assertArrayHasKey('charts', $recap);
        $this->assertArrayHasKey('tables', $recap);

        $diets = NutritionRepository::getMasterDiet();
        $this->assertNotEmpty($diets);

        $bangsals = NutritionRepository::getMasterBangsal();
        $this->assertNotEmpty($bangsals);
    }
}
