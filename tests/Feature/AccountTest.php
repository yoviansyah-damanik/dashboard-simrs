<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserHistory;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Account;

class AccountTest extends TestCase
{
    public function test_account_component_can_render_and_switch_tabs(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        // Ensure at least 1 history exists
        UserHistory::firstOrCreate([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => '127.0.0.1',
            'browser' => 'Chrome 120.0',
            'platform' => 'Windows',
            'device' => 'Desktop',
            'device_type' => 'desktop',
            'is_robot' => false,
        ]);

        Livewire::test(Account::class)
            ->assertStatus(200)
            ->assertSee('Informasi Akun')
            ->assertSee($user->name)
            ->call('switchTab', 'password')
            ->assertSet('type', 'password')
            ->assertSee('Keamanan Kata Sandi')
            ->call('switchTab', 'history')
            ->assertSet('type', 'history')
            ->assertSee('Riwayat Sesi');
    }
}
