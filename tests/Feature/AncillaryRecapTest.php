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
            ->assertSee('Total Pelayanan Penunjang')
            ->assertSee('Pasien Terlayani');

        Livewire::test(\App\Livewire\Ancillary\YearlyMatrix::class)
            ->assertStatus(200)
            ->assertSee('Total Pelayanan Penunjang')
            ->assertSee('Pasien Terlayani')
            ->assertSee('Asal Pasien')
            ->assertSee('Kontribusi Penunjang')
            ->assertSee('Semua Penunjang')
            ->assertSee('Farmasi')
            ->assertSee('Laboratorium')
            ->assertSee('Radiologi')
            ->assertSee('Gizi')
            ->set('activeTab', 'farmasi')
            ->assertStatus(200)
            ->assertSee('Indikator Farmasi')
            ->assertSee('Total Lembar Resep')
            ->assertSee('Resep Pulang')
            ->set('activeTab', 'lab')
            ->assertStatus(200)
            ->assertSee('Indikator Laboratorium')
            ->assertSee('Patologi Klinik (PK)')
            ->assertSee('Akumulasi Ralan')
            ->set('activeTab', 'rad')
            ->assertStatus(200)
            ->assertSee('Indikator Radiologi')
            ->assertSee('CR / X-Ray Konvensional')
            ->assertSee('Akumulasi Ralan')
            ->set('activeTab', 'gizi')
            ->assertStatus(200)
            ->assertSee('Indikator Pelayanan Gizi')
            ->assertSee('Total Porsi Makanan/Diet Disajikan')
            ->assertSee('Sarapan Pagi')
            ->assertSee('Makan Siang')
            ->assertSee('Makan Sore / Malam');
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
        $this->assertArrayHasKey('top_diets', $data);

        $this->assertCount(12, $data['months']);
        $this->assertArrayHasKey('laboratorium', $data['totals']);
        $this->assertArrayHasKey('radiologi', $data['totals']);
        $this->assertArrayHasKey('farmasi', $data['totals']);
        $this->assertArrayHasKey('gizi', $data['totals']);
        $this->assertArrayHasKey('gabungan', $data['totals']);

        $this->assertArrayHasKey('total_pelayanan', $data['summary']);
        $this->assertArrayHasKey('total_farmasi', $data['summary']);
        $this->assertArrayHasKey('total_gizi', $data['summary']);
        $this->assertArrayHasKey('contrib_farmasi_percent', $data['summary']);
        $this->assertArrayHasKey('contrib_gizi_percent', $data['summary']);
    }
}
