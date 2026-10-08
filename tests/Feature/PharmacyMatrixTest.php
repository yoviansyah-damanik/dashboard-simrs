<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\PharmacyReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class PharmacyMatrixTest extends TestCase
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

    public function test_pharmacy_yearly_matrix_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('pharmacy.yearly-matrix'));
        $response->assertRedirect(route('ancillary.yearly-matrix', ['activeTab' => 'farmasi']));

        $followed = $this->followRedirects($response);
        $followed->assertStatus(200)
            ->assertSee('Matriks Indikator Penunjang')
            ->assertSee('Indikator Farmasi')
            ->assertSee('Total Lembar Resep')
            ->assertSee('Pasien Terlayani');
    }

    public function test_pharmacy_yearly_matrix_livewire_renders_and_switches_tabs(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Pharmacy\YearlyMatrix::class)
            ->assertStatus(200)
            ->assertSee('Total Lembar Resep')
            ->assertSee('Pasien Terlayani')
            ->assertSee('Asal Peresepan')
            ->assertSee('Ringkasan Utama')
            ->set('activeTab', 'care_setting')
            ->assertStatus(200)
            ->assertSee('Kategori Perawatan')
            ->assertSee('Resep Rawat Jalan (Ralan)')
            ->assertSee('Resep Rawat Inap (Ranap)')
            ->assertSee('Ranap Harian (Selama Dirawat)')
            ->assertSee('Resep Pulang (Pasien Keluar)')
            ->set('activeTab', 'prescription_type')
            ->assertStatus(200)
            ->assertSee('Jenis / Klasifikasi Resep')
            ->assertSee('Resep Biasa (Reguler)')
            ->assertSee('Resep Kronis (Obat Rutin)')
            ->set('activeTab', 'quality')
            ->assertStatus(200)
            ->assertSee('Indikator Mutu Pelayanan')
            ->assertSee('Waktu Tunggu Pelayanan (Menit)')
            ->assertSee('Kepatuhan Target SPM (≤ 30 Menit)');
    }

    public function test_pharmacy_report_repository_data_structure(): void
    {
        $data = PharmacyReportRepository::getYearlyMatrix(2026);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('year', $data);
        $this->assertArrayHasKey('months', $data);
        $this->assertArrayHasKey('totals', $data);
        $this->assertArrayHasKey('averages', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('charts', $data);

        $this->assertCount(12, $data['months']);
        $this->assertArrayHasKey('total_resep', $data['totals']);
        $this->assertArrayHasKey('total_pasien', $data['totals']);
        $this->assertArrayHasKey('diserahkan', $data['totals']);
        $this->assertArrayHasKey('ranap_harian', $data['totals']);
        $this->assertArrayHasKey('resep_pulang', $data['totals']);
        $this->assertArrayHasKey('ranap', $data['totals']);
        $this->assertArrayHasKey('total_resep_pulang', $data['summary']);
        $this->assertArrayHasKey('total_ranap_harian', $data['summary']);
        $this->assertArrayHasKey('avg_waktu_tunggu', $data['summary']);
    }
}
