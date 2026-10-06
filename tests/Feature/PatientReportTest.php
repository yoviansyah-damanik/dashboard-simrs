<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\PatientReport\Index as PatientReportIndex;

class PatientReportTest extends TestCase
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

    public function test_patient_report_monthly_renders(): void
    {
        $this->authenticateUser();

        Livewire::test(PatientReportIndex::class)
            ->assertStatus(200)
            ->assertSee('Laporan Kunjungan dan Pengunjung')
            ->assertSee('Periode Bulanan')
            ->assertSee('Periode Tahunan')
            ->assertDontSee('Rekapitulasi Kunjungan dan Pengunjung Per Bulan');
    }

    public function test_patient_report_yearly_renders_monthly_breakdown_table(): void
    {
        $this->authenticateUser();

        Livewire::test(PatientReportIndex::class)
            ->call('setPeriod', 'yearly')
            ->assertStatus(200)
            ->assertSee('Rekapitulasi Kunjungan dan Pengunjung Per Bulan')
            ->assertSee('Januari')
            ->assertSee('Desember')
            ->assertSee('TOTAL TAHUN');
    }
}
