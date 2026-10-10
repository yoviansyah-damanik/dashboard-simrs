<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_page_renders_successfully_with_dark_mode_button(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('darkMode');
        $response->assertSee('Mode Terang');
        $response->assertSee('showChangelog', false);
        $response->assertSee('Catatan Rilis & Riwayat Versi', false);
        $response->assertSee('v2.1.1', false);
    }
}
