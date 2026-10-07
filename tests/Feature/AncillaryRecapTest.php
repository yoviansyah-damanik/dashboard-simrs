<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Laboratory\Recap as LaboratoryRecap;
use App\Livewire\Radiology\Recap as RadiologyRecap;

class AncillaryRecapTest extends TestCase
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

    public function test_laboratory_recap_can_render_chart_and_figures(): void
    {
        $this->authenticateUser();

        Livewire::test(LaboratoryRecap::class)
            ->assertStatus(200)
            ->assertSee('Laboratorium')
            ->assertSee('Total Pemeriksaan Lab')
            ->assertSee('Tren Pemeriksaan Laboratorium')
            ->assertDontSee('Total Nilai Tindakan')
            ->assertDontSee('Akumulasi Tarif SIMRS')
            ->assertSee('Dokter Pengirim')
            ->set('mainView', 'figures')
            ->assertStatus(200)
            ->assertSee('Status Pelayanan')
            ->assertSee('Kategori Laboratorium')
            ->assertDontSee('Demografi Pasien')
            ->assertSee('Dokter Pengirim Terbanyak')
            ->set('period', 'this_year')
            ->assertStatus(200);
    }

    public function test_radiology_recap_can_render_chart_and_figures(): void
    {
        $this->authenticateUser();

        Livewire::test(RadiologyRecap::class)
            ->assertStatus(200)
            ->assertSee('Radiologi')
            ->assertSee('Total Pemeriksaan Radiologi')
            ->assertSee('Tren Pemeriksaan Radiologi')
            ->assertDontSee('Total Nilai Tindakan')
            ->assertDontSee('Akumulasi Biaya Radiologi')
            ->assertSee('Modalitas (Modality)')
            ->assertSee('Dokter Pengirim')
            ->set('mainView', 'figures')
            ->assertStatus(200)
            ->assertSee('Status Pelayanan')
            ->assertSee('Modalitas Radiologi (Modality)')
            ->assertSee('Pemeriksaan Terbanyak')
            ->assertDontSee('Demografi Pasien')
            ->assertSee('Dokter Pengirim Terbanyak')
            ->set('period', 'this_year')
            ->assertStatus(200);
    }

    public function test_laboratory_and_radiology_recap_routes_are_accessible(): void
    {
        $this->authenticateUser();

        $responseLab = $this->get(route('laboratory.recap'));
        $responseLab->assertStatus(200);

        $responseRad = $this->get(route('radiology.recap'));
        $responseRad->assertStatus(200);
    }

    public function test_radiology_index_can_render_and_filter_by_modality(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\Radiology\Index::class)
            ->assertStatus(200)
            ->assertSee('Modality')
            ->set('modality', 'CR')
            ->assertStatus(200)
            ->set('modality', 'US')
            ->assertStatus(200);
    }

    public function test_ancillary_yearly_matrix_page_renders_successfully(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('ancillary.yearly-matrix'));
        $response->assertStatus(200)
            ->assertSee('Matriks Indikator Penunjang')
            ->assertSee('Total Pemeriksaan')
            ->assertSee('Pasien Terlayani');

        Livewire::test(\App\Livewire\Ancillary\YearlyMatrix::class)
            ->assertStatus(200)
            ->assertSee('Total Pemeriksaan')
            ->assertSee('Pasien Terlayani')
            ->assertSee('Asal Pasien')
            ->assertSee('Rasio Pelayanan')
            ->assertSee('Semua Penunjang')
            ->set('activeTab', 'lab')
            ->assertStatus(200)
            ->assertSee('Indikator Laboratorium')
            ->assertSee('Patologi Klinik (PK)')
            ->set('activeTab', 'rad')
            ->assertStatus(200)
            ->assertSee('Indikator Radiologi')
            ->assertSee('CR / X-Ray Konvensional');
    }

    public function test_ancillary_yearly_matrix_repository_data_structure(): void
    {
        $data = \App\Repository\AncillaryReportRepository::getYearlyMatrix(2026);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('months', $data);
        $this->assertArrayHasKey('totals', $data);
        $this->assertArrayHasKey('averages', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('charts', $data);

        $this->assertCount(12, $data['months']);
        $this->assertArrayHasKey('laboratorium', $data['totals']);
        $this->assertArrayHasKey('radiologi', $data['totals']);
        $this->assertArrayHasKey('gabungan', $data['totals']);
    }
}
