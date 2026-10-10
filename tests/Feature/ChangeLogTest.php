<?php

namespace Tests\Feature;

use App\Models\User;
use App\Helpers\GeneralHelper;
use App\Livewire\ChangeLog;
use App\View\Components\Sidebar;
use Livewire\Livewire;
use Tests\TestCase;

class ChangeLogTest extends TestCase
{
    protected function getAuthUser(): User
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        return $user;
    }

    /**
     * Memastikan GeneralHelper mengembalikan versi v2.1.1 dan data seluruh versi valid.
     */
    public function test_general_helper_returns_version_2_0_0_and_changelogs(): void
    {
        $current = GeneralHelper::getVersion();
        $this->assertEquals('v2.1.1', $current['version']);
        $this->assertIsArray($current['changeLog']);
        $this->assertNotEmpty($current['changeLog']);

        $all = GeneralHelper::getAllVersions();
        $this->assertIsArray($all);
        $this->assertGreaterThanOrEqual(7, count($all));
        $this->assertEquals('2.1.1', $all[0]['version']);
        $this->assertEquals('patch', $all[0]['type']);
    }

    /**
     * Memastikan halaman /changelog dapat diakses dengan sukses (HTTP 200).
     */
    public function test_authenticated_user_can_access_changelog_page(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $response = $this->get('/changelog');
        $response->assertStatus(200);
        $response->assertSee('Catatan Rilis Aplikasi', false);
        $response->assertSee('v2.1.1', false);
        $response->assertSee('Major Release', false);
    }

    /**
     * Memastikan rute alias /change-log me-redirect ke /changelog.
     */
    public function test_change_log_alias_redirects(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $response = $this->get('/change-log');
        $response->assertRedirect(route('changelog'));
    }

    /**
     * Memastikan menu sidebar dan badge memuat Catatan Rilis (v2.1.1).
     */
    public function test_sidebar_contains_changelog_menu(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        $sidebar = (new Sidebar())->render();
        $menus = $sidebar->getData()['menus'] ?? [];

        $lainnya = collect($menus)->firstWhere('title', 'Lainnya');
        $this->assertNotNull($lainnya, 'Grup menu Lainnya harus ada di sidebar.');

        $items = collect($lainnya['items']);
        $changelogMenu = $items->firstWhere('title', 'Catatan Rilis (v2.1.1)');
        $this->assertNotNull($changelogMenu, 'Menu Catatan Rilis harus terdaftar di Sidebar.');
        $this->assertEquals(route('changelog'), $changelogMenu['href']);
    }

    /**
     * Memastikan interaksi filter pencarian dan tipe rilis pada Livewire ChangeLog berjalan.
     */
    public function test_changelog_filtering_and_search(): void
    {
        $user = $this->getAuthUser();
        $this->actingAs($user);

        Livewire::test(ChangeLog::class)
            ->assertSee('v2.1.1')
            ->set('selectedType', 'major')
            ->assertSet('selectedType', 'major')
            ->assertSee('v2.1.1')
            ->set('search', 'ICD-9')
            ->assertSee('ICD-9')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('selectedType', 'all');
    }
}
