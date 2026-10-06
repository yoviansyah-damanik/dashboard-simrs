<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Outpatient\Recap as OutpatientRecap;
use App\Livewire\Inpatient\Recap as InpatientRecap;

class PatientRecapPagesTest extends TestCase
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

    public function test_outpatient_recap_can_render(): void
    {
        $this->authenticateUser();

        Livewire::test(OutpatientRecap::class)
            ->assertStatus(200)
            ->assertSee('Rawat Jalan')
            ->assertSee('Tabel')
            ->assertSee('Grafik')
            ->assertSee('Total Kunjungan');
    }

    public function test_inpatient_recap_can_render_and_switch_tabs(): void
    {
        $this->authenticateUser();

        Livewire::test(InpatientRecap::class)
            ->assertStatus(200)
            ->assertSee('Rekapitulasi Rawat Inap')
            ->assertSee('Pasien Dirawat')
            ->assertSee('Rekapitulasi')
            ->assertSee('Snapshot Bed')
            ->call('switchTab', 'recap')
            ->assertSee('Total Pasien')
            ->assertSee('Hari Perawatan')
            ->call('switchTab', 'snapshot')
            ->assertSee('Kapasitas Bed Keseluruhan');
    }
}
