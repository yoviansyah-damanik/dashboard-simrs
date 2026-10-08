<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repository\MedicalServicesReportRepository;
use Livewire\Livewire;
use Tests\TestCase;

class MedicalServicesSummaryTest extends TestCase
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
     * Memastikan route ringkasan layanan medis dapat diakses dengan sukses.
     */
    public function test_medical_services_summary_route_accessible(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('medical-services.summary'));
        $response->assertStatus(200)
            ->assertSee('Ringkasan Layanan Medis')
            ->assertSee('Rawat Jalan')
            ->assertSee('Gawat Darurat')
            ->assertSee('Rawat Inap');
    }

    /**
     * Memastikan komponen Livewire ringkasan layanan medis me-render dan tab dapat berganti.
     */
    public function test_medical_services_summary_livewire_renders_and_switches_tabs(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\MedicalServices\Summary::class)
            ->assertStatus(200)
            ->assertSee('Ringkasan Layanan Medis')
            ->assertSee('Rawat Jalan (Poli)')
            ->assertSee('Gawat Darurat (IGD)')
            ->assertSee('Rawat Inap (Ranap)')
            ->set('activeTab', 'poli')
            ->assertStatus(200)
            ->assertSee('Nama Poliklinik Spesialis')
            ->set('activeTab', 'igd')
            ->assertStatus(200)
            ->assertSee('Alur Keluar Pasien IGD')
            ->set('activeTab', 'ranap')
            ->assertStatus(200)
            ->assertSee('Status Pulang');
    }

    /**
     * Memastikan struktur data dari MedicalServicesReportRepository lengkap dan valid.
     */
    public function test_medical_services_report_repository_data_structure(): void
    {
        $data = MedicalServicesReportRepository::getSummary(date('Y-m-01'), date('Y-m-t'));

        $this->assertIsArray($data);
        $this->assertArrayHasKey('period', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('outpatient', $data);
        $this->assertArrayHasKey('emergency', $data);
        $this->assertArrayHasKey('inpatient', $data);
        $this->assertArrayHasKey('cara_bayar', $data);
        $this->assertArrayHasKey('charts', $data);

        // Validasi metrik utama
        $this->assertArrayHasKey('total_layanan', $data['summary']);
        $this->assertArrayHasKey('total_pasien', $data['summary']);
        $this->assertArrayHasKey('poli_kunjungan', $data['summary']);
        $this->assertArrayHasKey('igd_kunjungan', $data['summary']);
        $this->assertArrayHasKey('ranap_kunjungan', $data['summary']);
        $this->assertArrayHasKey('ranap_admissions', $data['summary']);
        $this->assertArrayHasKey('ranap_discharges', $data['summary']);
        $this->assertArrayHasKey('ranap_active', $data['summary']);
        $this->assertArrayHasKey('ranap_alos', $data['summary']);

        // Validasi ralan / poli
        $this->assertArrayHasKey('total', $data['outpatient']);
        $this->assertArrayHasKey('pasien', $data['outpatient']);
        $this->assertArrayHasKey('top_clinics', $data['outpatient']);

        // Validasi IGD
        $this->assertArrayHasKey('total', $data['emergency']);
        $this->assertArrayHasKey('pasien', $data['emergency']);
        $this->assertArrayHasKey('pulang', $data['emergency']);
        $this->assertArrayHasKey('dirawat', $data['emergency']);

        // Validasi Ranap
        $this->assertArrayHasKey('total', $data['inpatient']);
        $this->assertArrayHasKey('admissions', $data['inpatient']);
        $this->assertArrayHasKey('discharges', $data['inpatient']);
        $this->assertArrayHasKey('active', $data['inpatient']);
        $this->assertArrayHasKey('top_wards', $data['inpatient']);
    }

    /**
     * Memastikan pergantian filter periode dan sinkronisasi tanggal bekerja sesuai harapan.
     */
    public function test_medical_services_summary_date_filtering(): void
    {
        $this->authenticateUser();

        Livewire::test(\App\Livewire\MedicalServices\Summary::class)
            ->set('period', 'today')
            ->assertSet('startDate', date('Y-m-d'))
            ->assertSet('endDate', date('Y-m-d'))
            ->set('period', 'this_year')
            ->assertSet('startDate', date('Y-01-01'))
            ->assertSet('endDate', date('Y-12-31'));
    }
}
