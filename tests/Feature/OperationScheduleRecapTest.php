<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\OperationSchedule\Recap;

class OperationScheduleRecapTest extends TestCase
{
    public function test_recap_component_can_render(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        Livewire::test(Recap::class)
            ->assertStatus(200)
            ->assertSee('Rekap Operasi')
            ->assertSee('Tabel')
            ->assertSee('Grafik');
    }
}
