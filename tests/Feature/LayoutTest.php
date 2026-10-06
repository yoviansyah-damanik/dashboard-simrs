<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    public function test_authenticated_user_can_render_header_and_layout(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        $headerView = view('components.header', ['menus' => []])->render();
        $this->assertNotEmpty($headerView);
        $this->assertStringContainsString($user->name, $headerView);

        $sidebarView = view('components.sidebar', ['menus' => []])->render();
        $this->assertNotEmpty($sidebarView);

        $footerView = view('components.footer')->render();
        $this->assertNotEmpty($footerView);
    }
}
