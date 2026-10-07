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

    public function test_patient_report_trend_and_summary_math_balances(): void
    {
        $this->authenticateUser();

        $summary = \App\Repository\PatientReportRepository::getSummary('2024-01-01', '2024-01-31');
        $this->assertEquals(
            $summary['total_kunjungan'],
            $summary['rawat_jalan'] + $summary['igd'] + $summary['rawat_inap'],
            'Summary total kunjungan must equal rawat jalan (poli) + igd + rawat inap'
        );
        $this->assertEquals(
            $summary['total_rawat_jalan'],
            $summary['rawat_jalan'] + $summary['igd'],
            'Summary total rawat jalan must equal poli + igd'
        );

        $trend = \App\Repository\PatientReportRepository::getTrendData('2024-01-01', '2024-01-31');
        for ($i = 0; $i < count($trend['labels']); $i++) {
            $kunjungan = $trend['kunjungan'][$i];
            $poli = $trend['rawatJalan'][$i];
            $igd = $trend['igd'][$i];
            $totRalan = $trend['totalRawatJalan'][$i];
            $ranap = $trend['rawatInap'][$i];

            $this->assertEquals($totRalan, $poli + $igd, "Day {$trend['labels'][$i]} totalRawatJalan must equal poli + igd");
            $this->assertEquals($kunjungan, $totRalan + $ranap, "Day {$trend['labels'][$i]} kunjungan must equal totalRawatJalan + ranap");
        }
    }
}
