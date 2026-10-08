<?php

namespace Tests\Feature;

use App\Models\User;
use App\Livewire\Home;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class HomeExecutiveMutuTest extends TestCase
{
    /**
     * Memastikan komponen Home dapat dirender dengan ringkasan Mutu & Akreditasi.
     */
    public function test_home_page_renders_mutu_executive_section(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Mutu & Akreditasi Rumah Sakit', false);
        $response->assertSee('Standar Kemenkes RI', false);
        $response->assertSee('Highlight Indikator Nasional Mutu (INM)', false);
        $response->assertSee('Kepatuhan SPM per Unit Pelayanan', false);
    }

    /**
     * Memastikan Livewire Home memiliki computed property mutuSummary dan method refreshMutuCache.
     */
    public function test_home_livewire_mutu_summary_and_cache_refresh(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        Livewire::test(Home::class)
            ->assertSee('Mutu & Akreditasi Rumah Sakit', false)
            ->call('refreshMutuCache')
            ->assertStatus(200);
    }
}
