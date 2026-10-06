<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Room\Index as RoomIndex;

class RoomTest extends TestCase
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

    public function test_room_page_can_render(): void
    {
        $this->authenticateUser();

        Livewire::test(RoomIndex::class)
            ->assertStatus(200)
            ->assertSee('Monitoring Kamar')
            ->assertSee('Total Kapasitas')
            ->assertSee('Bed Tersedia')
            ->assertSee('Bed Terisi')
            ->assertSee('Tingkat Okupansi');
    }

    public function test_room_modal_opens_and_closes(): void
    {
        $this->authenticateUser();

        $component = Livewire::test(RoomIndex::class);
        $rooms = $component->viewData('rooms');

        if ($rooms && $rooms->isNotEmpty()) {
            $firstClass = $rooms->keys()->first();

            $component->call('setShow', $firstClass)
                ->assertSet('roomActive', $firstClass)
                ->assertSee('Detail Kamar: ' . $firstClass)
                ->call('closeModal')
                ->assertSet('roomActive', null);
        }
    }
}
