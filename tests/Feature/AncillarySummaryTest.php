<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\AncillaryReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class AncillarySummaryTest extends TestCase
{
    /**
     * Otentikasi pengguna untuk pengujian fitur.
     */
    protected function authenticateUser(): User
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->actingAs($user);
        return $user;
    }

    /**
     * Memastikan route ringkasan layanan penunjang dapat diakses dengan sukses.
     */
    public function test_ancillary_summary_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('ancillary.summary'));
        $response->assertStatus(200)
            ->assertSee('Ringkasan Layanan Penunjang')
            ->assertSee('Farmasi')
            ->assertSee('Laboratorium')
            ->assertSee('Radiologi')
            ->assertSee('Gizi');
    }

    /**
     * Memastikan komponen Livewire ringkasan penunjang me-render dan tab dapat berganti.
     */
    public function test_ancillary_summary_livewire_renders_and_switches_tabs(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Ancillary\Summary::class)
            ->assertStatus(200)
            ->assertSee('Ringkasan Layanan Penunjang')
            ->assertSee('Farmasi')
            ->assertSee('Laboratorium')
            ->assertSee('Radiologi')
            ->assertSee('Gizi')
            ->set('activeTab', 'farmasi')
            ->assertStatus(200)
            ->assertSee('Resep Poli (Rawat Jalan)')
            ->assertSee('Resep Biasa')
            ->set('activeTab', 'lab')
            ->assertStatus(200)
            ->assertSee('Patologi Klinik')
            ->set('activeTab', 'rad')
            ->assertStatus(200)
            ->assertSee('Modalitas')
            ->set('activeTab', 'gizi')
            ->assertStatus(200)
            ->assertSee('Evaluasi Asuhan Gizi')
            ->assertSee('Sarapan Pagi');
    }

    /**
     * Memastikan struktur data dari AncillaryReportRepository lengkap dan valid.
     */
    public function test_ancillary_report_repository_data_structure(): void
    {
        $data = AncillaryReportRepository::getSummary(date('Y-m-01'), date('Y-m-t'));

        $this->assertIsArray($data);
        $this->assertArrayHasKey('period', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('farmasi', $data);
        $this->assertArrayHasKey('laboratorium', $data);
        $this->assertArrayHasKey('radiologi', $data);
        $this->assertArrayHasKey('gizi', $data);
        $this->assertArrayHasKey('charts', $data);

        // Validasi metrik utama
        $this->assertArrayHasKey('total_pelayanan', $data['summary']);
        $this->assertArrayHasKey('total_farmasi', $data['summary']);
        $this->assertArrayHasKey('total_lab', $data['summary']);
        $this->assertArrayHasKey('total_rad', $data['summary']);
        $this->assertArrayHasKey('total_gizi', $data['summary']);
        $this->assertArrayHasKey('total_poli', $data['summary']);
        $this->assertArrayHasKey('total_igd', $data['summary']);
        $this->assertArrayHasKey('total_ranap', $data['summary']);

        // Validasi unit penunjang
        $this->assertArrayHasKey('total', $data['farmasi']);
        $this->assertArrayHasKey('total', $data['laboratorium']);
        $this->assertArrayHasKey('total', $data['radiologi']);
        $this->assertArrayHasKey('total', $data['gizi']);
    }

    /**
     * Memastikan pergantian filter periode dan sinkronisasi tanggal bekerja sesuai harapan.
     */
    public function test_ancillary_summary_date_filtering(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Ancillary\Summary::class)
            ->set('period', 'today')
            ->assertSet('startDate', date('Y-m-d'))
            ->assertSet('endDate', date('Y-m-d'))
            ->set('period', 'this_month')
            ->assertSet('startDate', date('Y-m-01'))
            ->assertSet('endDate', date('Y-m-t'));
    }
}
