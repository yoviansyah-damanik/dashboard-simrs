<?php

namespace Tests\Feature;

use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorPagesTest extends TestCase
{
    public function test_404_not_found_page_renders_correctly(): void
    {
        $response = $this->get('/non-existent-route-for-testing-404');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Ke Beranda');
    }

    public function test_403_view_can_be_rendered(): void
    {
        $exception = new HttpException(403, 'Akses Ditolak Khusus');
        $view = view('errors.403', ['exception' => $exception])->render();

        $this->assertStringContainsString('403', $view);
        $this->assertStringContainsString('Akses Ditolak', $view);
        $this->assertStringContainsString('Ke Beranda', $view);
    }

    public function test_419_view_can_be_rendered(): void
    {
        $exception = new HttpException(419, 'Sesi Telah Kedaluwarsa');
        $view = view('errors.419', ['exception' => $exception])->render();

        $this->assertStringContainsString('419', $view);
        $this->assertStringContainsString('Sesi Telah Kedaluwarsa', $view);
        $this->assertStringContainsString('Masuk Kembali', $view);
    }

    public function test_429_view_can_be_rendered(): void
    {
        $exception = new HttpException(429, 'Terlalu Banyak Permintaan');
        $view = view('errors.429', ['exception' => $exception])->render();

        $this->assertStringContainsString('429', $view);
        $this->assertStringContainsString('Terlalu Banyak Permintaan', $view);
    }

    public function test_generic_4xx_view_can_be_rendered(): void
    {
        $exception = new HttpException(405, 'Metode HTTP Tidak Diizinkan');
        $view = view('errors.4xx', ['exception' => $exception])->render();

        $this->assertStringContainsString('405', $view);
        $this->assertStringContainsString('Permintaan Klien Tidak Valid', $view);
    }

    public function test_500_view_can_be_rendered(): void
    {
        $exception = new HttpException(500, 'Kesalahan Internal');
        $view = view('errors.500', ['exception' => $exception])->render();

        $this->assertStringContainsString('500', $view);
        $this->assertStringContainsString('Kesalahan Server Internal', $view);
        $this->assertStringContainsString('Coba Muat Ulang', $view);
    }

    public function test_503_view_can_be_rendered(): void
    {
        $exception = new HttpException(503, 'Layanan Pemeliharaan');
        $view = view('errors.503', ['exception' => $exception])->render();

        $this->assertStringContainsString('503', $view);
        $this->assertStringContainsString('Layanan Dalam Pemeliharaan', $view);
    }

    public function test_generic_5xx_view_can_be_rendered(): void
    {
        $exception = new HttpException(502, 'Bad Gateway');
        $view = view('errors.5xx', ['exception' => $exception])->render();

        $this->assertStringContainsString('502', $view);
        $this->assertStringContainsString('Gangguan Layanan Server', $view);
    }
}
