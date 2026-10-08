<?php

namespace Tests\Feature;

use App\Models\User;
use App\View\Components\Sidebar;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    /**
     * Memastikan header, sidebar, dan footer dapat dirender dengan baik untuk pengguna terautentikasi.
     */
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

    /**
     * Memastikan struktur menu Sidebar memiliki grup menu Pendaftaran tersendiri.
     */
    public function test_sidebar_has_dedicated_pendaftaran_menu_group(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        $view = (new Sidebar())->render();
        $menus = $view->getData()['menus'] ?? [];

        // Cari grup Pendaftaran
        $pendaftaranGroup = collect($menus)->firstWhere('title', 'Pendaftaran');
        $this->assertNotNull($pendaftaranGroup, 'Grup menu Pendaftaran harus tersedia di sidebar.');

        $items = collect($pendaftaranGroup['items']);
        $this->assertNotNull($items->firstWhere('title', 'Data Pasien'));
        $this->assertNotNull($items->firstWhere('title', 'Rekap Pendaftaran'));

        // Pastikan Layanan Medis tidak lagi memuat menu Pendaftaran
        $layananMedisGroup = collect($menus)->firstWhere('title', 'Layanan Medis');
        $this->assertNotNull($layananMedisGroup);
        $layananMedisItems = collect($layananMedisGroup['items']);
        $this->assertNull($layananMedisItems->firstWhere('title', 'Pendaftaran'));
        $this->assertNotNull($layananMedisItems->firstWhere('title', 'Ringkasan'));
    }
}
